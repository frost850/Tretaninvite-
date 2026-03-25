<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function show()
    {
        $userId = session('admin_user_id');
        if (!$userId) {
            return redirect()->route('admin.dashboard')->with('error', 'Fitur profil hanya tersedia untuk sub-admin.');
        }

        $user = User::findOrFail($userId);
        return view('admin.profile', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $userId = session('admin_user_id');
        if (!$userId) {
            return redirect()->route('admin.dashboard')->with('error', 'Fitur profil hanya tersedia untuk sub-admin.');
        }

        $user = User::findOrFail($userId);

        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&^()_\-+=\[\]{};:\'",.\/<>]/',
            ],
        ], [
            'new_password.min'       => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            'new_password.regex'     => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus (contoh: @$!%*#?&).',
        ]);

        // Accept current password OR the OTP token
        $validCurrent = Hash::check($request->current_password, $user->password);
        $validOtp     = $user->otp_token
                        && Hash::check($request->current_password, $user->otp_token) // BH-9: hash compare (=== broken setelah C-4)
                        && $user->otp_expires_at
                        && now()->lessThanOrEqualTo($user->otp_expires_at);

        if (!$validCurrent && !$validOtp) {
            return back()->withErrors(['current_password' => 'Password saat ini / OTP tidak valid atau sudah kadaluarsa.']);
        }

        $user->update([
            'password'            => Hash::make($request->new_password),
            'otp_token'           => null,
            'otp_expires_at'      => null,
            'must_change_password'=> false,
        ]);

        // Regenerate session to invalidate any stolen session tokens
        session()->regenerate();

        return redirect()->route('admin.dashboard')
                         ->with('success', 'Password berhasil diperbarui. Selamat datang!');
    }
}
