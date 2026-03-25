<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin — TretanInvite</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="/css/tretaninvite.css">
    <style @nonce>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .login-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        }
        .login-input {
            width: 100%;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.9rem;
            color: #f1f5f9;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .login-input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.2); }
    </style>
</head>
<body class="page-bg min-h-screen flex items-center justify-center p-6">
    <div id="login-stars" class="star-wrap fixed inset-0 pointer-events-none z-0"></div>
    <div class="relative z-10 w-full max-w-sm">
        <div class="login-card p-8">
            <div class="text-center mb-8">
                <div class="text-3xl font-black bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent mb-1">✨ TretanInvite</div>
                <div class="text-xs text-slate-400 font-semibold bg-white/5 px-3 py-1 rounded-full border border-white/10 inline-block">Admin Panel</div>
            </div>

            @if(session('status'))
                <div class="mb-5 p-3 rounded-xl bg-red-900/30 border border-red-700/40 text-red-300 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="post" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-300 mb-2">Email Admin</label>
                    <input type="email" name="email" id="email" required autofocus
                           value="{{ old('email') }}"
                           placeholder="admin@email.com"
                           class="login-input @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-300 mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                           placeholder="••••••••"
                           class="login-input @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full btn-glow py-2.5 rounded-xl font-bold text-sm tracking-wide">
                    Masuk ke Admin
                </button>
            </form>

            <p class="mt-6 text-center">
                <a href="{{ route('welcome') }}" class="text-amber-400/70 hover:text-amber-400 text-sm transition">← Kembali ke beranda</a>
            </p>
        </div>
    </div>
    <script src="/js/tretaninvite.js"></script>
    <script @nonce>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.TI) TI.spawnStars('login-stars', 40);
        });
    </script>
