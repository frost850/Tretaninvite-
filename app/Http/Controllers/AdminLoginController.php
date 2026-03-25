<?php

namespace App\Http\Controllers;

use App\Mail\SuperAdminLoginMail;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function showLoginForm(Request $request): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // BH-6: Per-account brute force lockout (max 10 gagal / 15 menit, terpisah per email)
        $lockCacheKey = 'login_attempts:' . md5(strtolower($request->email));
        $attempts     = Cache::get($lockCacheKey, 0);
        if ($attempts >= 10) {
            Log::warning('Admin login blocked: too many attempts', [
                'email' => $request->email,
                'ip'    => $request->ip(),
            ]);
            AuditLogger::log('login_blocked', null, null, [
                'email'  => $request->email,
                'reason' => 'rate_limit_exceeded',
            ], $request);
            return back()->withErrors(['email' => 'Terlalu banyak percobaan login. Coba lagi dalam 15 menit.'])->withInput();
        }

        $redirect = session('redirect_after', route('admin.dashboard'));
        session()->forget('redirect_after');

        // BH-2+C-1: Bandingkan host secara tepat (cegah subdomain bypass seperti example.com.evil.com)
        $appHost      = parse_url(url('/'), PHP_URL_HOST);
        $redirectHost = $redirect ? parse_url($redirect, PHP_URL_HOST) : null;
        if (!$redirect || $redirectHost !== $appHost) {
            $redirect = route('admin.dashboard');
        }

        // ── 1. Super-admin check (config-based) ──────────────────────────────
        $configPassword = config('admin.password');
        $allowedEmails  = config('admin.allowed_emails', []);

        if (in_array($request->email, $allowedEmails)) {
            if ($configPassword === '' || $configPassword === 'admin123') {
                if (app()->environment('production')) {
                    abort(500, 'Set ADMIN_PASSWORD di .env untuk production.');
                }
            }

            // FIX-1: Support bcrypt-hashed ADMIN_PASSWORD (hash starts with $2y$ = bcrypt)
            $isHashed = str_starts_with((string) $configPassword, '$2y$')
                        || str_starts_with((string) $configPassword, '$argon');
            $passwordValid = $isHashed
                ? Hash::check($request->password, (string) $configPassword)
                : hash_equals((string) $configPassword, $request->password); // BH-4: constant-time compare

            if (!$passwordValid) {
                Cache::put($lockCacheKey, $attempts + 1, now()->addMinutes(15)); // BH-6
                Log::warning('Admin login failed (super-admin): wrong password', [
                    'email' => $request->email,
                    'ip'    => $request->ip(),
                    'ua'    => $request->userAgent(),
                ]);
                AuditLogger::log('login_failed', 'super_admin', null, ['email' => $request->email], $request);
                // BH-1: field 'email' (bukan 'password') agar sama dengan sub-admin → tidak bocorkan status
                return back()->withErrors(['email' => 'Email atau password tidak valid.'])->withInput();
            }

            Cache::forget($lockCacheKey); // BH-6: reset counter setelah login berhasil

            // FIX-2: 2FA — kirim OTP email sebelum menyelesaikan login jika ADMIN_EMAIL dikonfigurasi
            $adminEmail = config('admin.email');
            $otpSent    = false;
            if ($adminEmail) {
                $otp    = (string) random_int(100000, 999999);
                $otpKey = 'super_admin_otp:' . md5($request->email);
                Cache::put($otpKey, Hash::make($otp), now()->addMinutes(10));
                try {
                    Mail::to($adminEmail)->send(new SuperAdminLoginMail($otp, $request->ip()));
                    $otpSent = true;
                } catch (\Throwable $e) {
                    Log::warning('Super-admin 2FA email failed', ['ip' => $request->ip(), 'error' => $e->getMessage()]);
                }
            }

            if ($otpSent) {
                session()->regenerate(); // C-3: regenerate sebelum menyimpan data pending OTP
                Session::put('admin_pending_otp_email', $request->email);
                Session::put('admin_pending_redirect', $redirect);
                AuditLogger::log('login_2fa_sent', 'super_admin', null, ['email' => $request->email], $request);
                return redirect()->route('admin.otp.show');
            }

            // 2FA tidak dikonfigurasi → selesaikan login langsung
            // C-3: Regenerate session ID untuk mencegah session fixation
            session()->regenerate();

            Session::put('admin_authenticated', true);
            Session::put('admin_email', $request->email);
            Session::put('admin_name', 'Super Admin');
            Session::put('admin_is_super', true);
            AuditLogger::log('login_success', 'super_admin', null, ['email' => $request->email], $request);
            session()->flash('welcome_message', 'Selamat datang, Super Admin!');

            return redirect($redirect);
        }

        // ── 2. Sub-admin check (database-based) ──────────────────────────────
        $user = User::where('email', $request->email)
                    ->where('role', 'admin')
                    ->where('is_active', true)
                    ->first();

        if (!$user) {
            Cache::put($lockCacheKey, $attempts + 1, now()->addMinutes(15)); // BH-6
            Log::warning('Admin login failed: email not found or inactive', [
                'email' => $request->email,
                'ip'    => $request->ip(),
                'ua'    => $request->userAgent(),
            ]);
            AuditLogger::log('login_failed', 'sub_admin', null, ['email' => $request->email], $request);
            // C-2: Pesan generik — jangan bocorkan apakah email ada atau tidak
            return back()->withErrors(['email' => 'Email atau password tidak valid.'])->withInput();
        }

        $validPassword = Hash::check($request->password, $user->password);
        $validOtp      = $user->otp_token
                         && Hash::check($request->password, $user->otp_token)
                         && $user->otp_expires_at
                         && now()->lessThanOrEqualTo($user->otp_expires_at);

        if (!$validPassword && !$validOtp) {
            Cache::put($lockCacheKey, $attempts + 1, now()->addMinutes(15)); // BH-6
            Log::warning('Admin login failed (sub-admin): wrong password or OTP', [
                'email'   => $request->email,
                'user_id' => $user->id,
                'ip'      => $request->ip(),
                'ua'      => $request->userAgent(),
            ]);
            AuditLogger::log('login_failed', 'sub_admin', $user->id, ['email' => $request->email], $request);
            // C-2: Semua gagal pakai field 'email' agar tidak bocorkan info akun
            return back()->withErrors(['email' => 'Email atau password tidak valid.'])->withInput();
        }

        // M-6: Hapus OTP segera setelah login berhasil (tidak dibiarkan aktif di DB)
        $user->update([
            'last_login_at'  => now(),
            'otp_token'      => null,
            'otp_expires_at' => null,
        ]);

        Cache::forget($lockCacheKey); // BH-6: reset counter setelah login berhasil
        // C-3: Regenerate session ID untuk mencegah session fixation
        session()->regenerate();

        Session::put('admin_authenticated', true);
        Session::put('admin_email', $user->email);
        Session::put('admin_user_id', $user->id);
        Session::put('admin_name', $user->name);
        Session::put('admin_is_super', false);
        AuditLogger::log('login_success', 'sub_admin', $user->id, ['email' => $user->email], $request);
        session()->flash('welcome_message', 'Selamat datang, ' . $user->name . '!');

        // Force password change if first login / OTP used
        if ($user->must_change_password || $validOtp) {
            $user->update(['must_change_password' => true]);
            return redirect()->route('admin.profile');
        }

        return redirect($redirect);
    }

    public function logout(Request $request): RedirectResponse
    {
        AuditLogger::log('logout', session('admin_is_super') ? 'super_admin' : 'sub_admin', null, ['email' => session('admin_email')], $request);
        // BH-5: Invalidate seluruh session (hapus data + ganti session ID) agar cookie lama tidak bisa dipakai ulang
        Session::invalidate();
        session()->regenerateToken();
        return redirect()->route('welcome');
    }

    // ─── 2FA OTP ─────────────────────────────────────────────────────────────

    public function showOtp(): View|RedirectResponse
    {
        if (!Session::has('admin_pending_otp_email')) {
            return redirect()->route('admin.login');
        }
        return view('admin.login-otp');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        if (!Session::has('admin_pending_otp_email')) {
            return redirect()->route('admin.login');
        }

        $request->validate(['otp' => 'required|digits:6']);

        $email      = Session::get('admin_pending_otp_email');
        $otpKey     = 'super_admin_otp:' . md5($email);
        $failKey    = 'super_admin_otp_fails:' . md5($email);
        $stored     = Cache::get($otpKey);
        $failCount  = Cache::get($failKey, 0);

        if ($failCount >= 10) {
            Cache::forget($otpKey);
            Session::pull('admin_pending_otp_email');
            Session::pull('admin_pending_redirect');
            AuditLogger::log('login_2fa_blocked', 'super_admin', null, ['email' => $email], $request);
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Terlalu banyak percobaan OTP. Silakan login ulang dalam 1 jam.']);
        }

        if (!$stored || !Hash::check($request->otp, $stored)) {
            Cache::put($failKey, $failCount + 1, now()->addHour());
            AuditLogger::log('login_2fa_failed', 'super_admin', null, ['email' => $email, 'attempt' => $failCount + 1], $request);
            return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kedaluwarsa.'])->withInput();
        }

        Cache::forget($failKey);

        Cache::forget($otpKey);
        $redirect = Session::get('admin_pending_redirect', route('admin.dashboard'));
        Session::pull('admin_pending_otp_email');
        Session::pull('admin_pending_redirect');

        Session::put('admin_authenticated', true);
        Session::put('admin_email', $email);
        Session::put('admin_name', 'Super Admin');
        Session::put('admin_is_super', true);
        AuditLogger::log('login_success', 'super_admin', null, ['email' => $email], $request);
        session()->flash('welcome_message', 'Selamat datang, Super Admin!');

        return redirect($redirect);
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        if (!Session::has('admin_pending_otp_email')) {
            return redirect()->route('admin.login');
        }

        $email      = Session::get('admin_pending_otp_email');
        $adminEmail = config('admin.email');
        if ($adminEmail) {
            $otp    = (string) random_int(100000, 999999);
            $otpKey = 'super_admin_otp:' . md5($email);
            Cache::put($otpKey, Hash::make($otp), now()->addMinutes(10));
            try {
                Mail::to($adminEmail)->send(new SuperAdminLoginMail($otp, $request->ip()));
            } catch (\Throwable $e) {
                Log::warning('Super-admin 2FA resend failed', ['ip' => $request->ip(), 'error' => $e->getMessage()]);
            }
        }

        return redirect()->route('admin.otp.show')->with('otp_resent', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
