<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi 2FA — TretanInvite</title>
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
            font-size: 1.2rem;
            color: #f1f5f9;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            letter-spacing: 0.4em;
            text-align: center;
        }
        .login-input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.2); }
    </style>
</head>
<body class="page-bg min-h-screen flex items-center justify-center p-6">
    <div id="otp-stars" class="star-wrap fixed inset-0 pointer-events-none z-0"></div>
    <div class="relative z-10 w-full max-w-sm">
        <div class="login-card p-8">
            <div class="text-center mb-8">
                <div class="text-3xl font-black bg-gradient-to-r from-amber-400 to-orange-400 bg-clip-text text-transparent mb-1">🔐 TretanInvite</div>
                <div class="text-xs text-slate-400 font-semibold bg-white/5 px-3 py-1 rounded-full border border-white/10 inline-block">Verifikasi 2FA</div>
            </div>

            @if(session('otp_resent'))
                <div class="mb-5 p-3 rounded-xl bg-emerald-900/30 border border-emerald-700/40 text-emerald-300 text-sm">
                    ✅ {{ session('otp_resent') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-red-900/30 border border-red-700/40 text-red-300 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <p class="text-slate-400 text-sm text-center mb-6">
                Kode verifikasi 6 digit telah dikirim ke email admin.<br>
                <span class="text-slate-500 text-xs">Berlaku 10 menit.</span>
            </p>

            <form action="{{ route('admin.otp.verify') }}" method="post" class="space-y-5">
                @csrf
                <div>
                    <label for="otp" class="block text-sm font-semibold text-slate-300 mb-2">Kode OTP</label>
                    <input type="text" name="otp" id="otp" required autofocus
                           maxlength="6" pattern="\d{6}" inputmode="numeric"
                           placeholder="000000"
                           class="login-input @error('otp') border-red-500 @enderror"
                           autocomplete="one-time-code">
                    @error('otp')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full btn-glow py-2.5 rounded-xl font-bold text-sm tracking-wide">
                    Verifikasi &amp; Masuk
                </button>
            </form>

            <div class="mt-6 text-center space-y-2">
                <form action="{{ route('admin.otp.resend') }}" method="post" class="inline">
                    @csrf
                    <button type="submit" class="text-amber-400/70 hover:text-amber-400 text-sm transition">
                        Kirim ulang kode
                    </button>
                </form>
                <br>
                <a href="{{ route('admin.login') }}" class="text-slate-500 hover:text-slate-300 text-xs transition">
                    ← Kembali ke login
                </a>
            </div>
        </div>
    </div>
    <script src="/js/tretaninvite.js"></script>
    <script @nonce>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.TI) TI.spawnStars('otp-stars', 40);
        });
    </script>
</body>
</html>
