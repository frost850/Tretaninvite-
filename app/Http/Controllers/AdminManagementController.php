<?php

namespace App\Http\Controllers;

use App\Mail\AdminOtpMail;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
{
    private function requireSuperAdmin(): void
    {
        if (!session('admin_is_super')) {
            abort(403, 'Halaman ini hanya dapat diakses oleh Super Admin.');
        }
    }

    public function index()
    {
        $this->requireSuperAdmin();
        $admins = User::where('role', 'admin')->orderBy('created_at', 'desc')->get();
        $superAdminEmail = config('admin.allowed_emails.0') ?? config('admin.allowed_emails')[0] ?? null;
        return view('admin.admins.index', compact('admins', 'superAdminEmail'));
    }

    public function store(Request $request)
    {
        $this->requireSuperAdmin();
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
        ]);

        // Cegah membuat sub-admin dengan email yang sudah dipakai sebagai super-admin
        // (ADMIN_EMAILS). Login super-admin selalu diutamakan → OTP tidak akan pernah cocok.
        $allowedEmails = config('admin.allowed_emails', []);
        if (in_array(strtolower($request->email), array_map('strtolower', $allowedEmails))) {
            return back()
                ->withErrors(['email' => 'Email ini sudah digunakan sebagai akun super-admin. Gunakan email lain.'])
                ->withInput();
        }

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        $user = User::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'password'            => Hash::make($otp),
            'role'                => 'admin',
            'otp_token'           => Hash::make($otp), // C-4: simpan hash, bukan plaintext
            'otp_expires_at'      => now()->addHours(24),
            'must_change_password'=> true,
            'is_active'           => true,
            'added_by'            => session('admin_email') ?? 'super_admin',
        ]);

        $loginUrl = route('admin.login');

        try {
            Mail::to($user->email)->send(new AdminOtpMail($otp, $user->name, $loginUrl));
            $mailStatus = 'Email berhasil dikirim ke ' . $user->email;
        } catch (\Exception $e) {
            Log::warning('AdminOtpMail failed on create', ['email' => $user->email, 'error' => $e->getMessage()]);
            $mailStatus = 'Admin ditambahkan, namun email gagal dikirim. Silakan reset OTP secara manual dari daftar admin.';
        }

        AuditLogger::log('admin_created', 'user', $user->id, ['email' => $user->email, 'name' => $user->name]);

        return redirect()->route('admin.admins.index')
                         ->with('success', 'Admin ' . $user->name . ' berhasil ditambahkan. ' . $mailStatus);
    }

    public function resetOtp(User $user)
    {
        $this->requireSuperAdmin();
        if ($user->role !== 'admin') {
            return redirect()->route('admin.admins.index')->with('error', 'Tidak dapat mereset OTP super admin.');
        }

        $otp = (string) random_int(100000, 999999);

        $user->update([
            'password'            => Hash::make($otp),
            'otp_token'           => Hash::make($otp), // C-4: simpan hash, bukan plaintext
            'otp_expires_at'      => now()->addHours(24),
            'must_change_password'=> true,
        ]);

        $loginUrl = route('admin.login');

        try {
            Mail::to($user->email)->send(new AdminOtpMail($otp, $user->name, $loginUrl));
            $mailStatus = 'Email OTP baru berhasil dikirim.';
        } catch (\Exception $e) {
            Log::warning('AdminOtpMail failed on reset', ['email' => $user->email, 'error' => $e->getMessage()]);
            $mailStatus = 'OTP direset, namun email gagal dikirim. Silakan coba reset ulang OTP.';
        }

        return redirect()->route('admin.admins.index')
                         ->with('success', $mailStatus);
    }

    public function toggleActive(User $user)
    {
        $this->requireSuperAdmin();
        if ($user->role !== 'admin') {
            return redirect()->route('admin.admins.index')->with('error', 'Tidak dapat menonaktifkan super admin.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        AuditLogger::log($user->is_active ? 'admin_activated' : 'admin_deactivated', 'user', $user->id, ['email' => $user->email, 'name' => $user->name]);

        return redirect()->route('admin.admins.index')
                         ->with('success', 'Admin ' . $user->name . ' berhasil ' . $status . '.');
    }

    public function destroy(User $user)
    {
        $this->requireSuperAdmin();
        if ($user->role !== 'admin') {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Tidak dapat menghapus super admin.'], 403);
            }
            return redirect()->route('admin.admins.index')->with('error', 'Tidak dapat menghapus super admin.');
        }

        $name = $user->name;
        $email = $user->email;
        $userId = $user->id;
        $user->delete();

        AuditLogger::log('admin_deleted', 'user', $userId, ['email' => $email, 'name' => $name]);

        if (request()->wantsJson()) {
            return response()->json(['message' => "Admin {$name} berhasil dihapus."]);
        }

        return redirect()->route('admin.admins.index')
                         ->with('success', 'Admin ' . $name . ' berhasil dihapus.');
    }
}
