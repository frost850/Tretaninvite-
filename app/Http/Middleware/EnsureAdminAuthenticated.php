<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('admin_authenticated', false)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }
            return redirect()->route('admin.login')->with('redirect_after', $request->url());
        }

        // Jika sub-admin (punya user_id di session), cek status is_active di DB setiap request
        $userId = session('admin_user_id');
        if ($userId) {
            $user = User::find($userId);
            if (!$user || !$user->is_active) {
                Session::flush();
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Akun Anda telah dinonaktifkan.'], 403);
                }
                return redirect()->route('admin.login')
                    ->withErrors(['email' => 'Akun Anda telah dinonaktifkan oleh Super Admin.']);
            }
        }

        // Back-fill admin_is_super untuk session lama
        if (session('admin_is_super') === null) {
            $isSuperAdmin = !$userId;
            session(['admin_is_super' => $isSuperAdmin]);
            if ($isSuperAdmin && !session('admin_name')) {
                session(['admin_name' => 'Super Admin']);
            }
        }

        return $next($request);
    }
}
