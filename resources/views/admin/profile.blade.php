@extends('admin.layout')

@section('title', 'Profil & Ganti Password')

@section('content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-6 fade-up stagger-1">
    <div>
        <p class="text-slate-500 text-xs">Akun Anda</p>
        <h1 class="text-2xl font-black text-slate-100 mt-0.5">Profil Admin</h1>
        <p class="text-slate-500 text-sm mt-0.5">Ganti password akun Anda.</p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-emerald-900/50 border border-emerald-700/60 text-emerald-300 text-sm fade-up stagger-1">
    ✅ {{ session('success') }}
</div>
@endif

@if($user->must_change_password)
<div class="mb-6 px-4 py-3 rounded-xl bg-amber-900/40 border border-amber-700/50 text-amber-300 text-sm fade-up stagger-1">
    ⚠️ <strong>Wajib ganti password!</strong> Anda menggunakan OTP sementara. Silakan buat password baru sebelum melanjutkan.
</div>
@endif

<div class="max-w-lg fade-up stagger-2">
    {{-- Info Card --}}
    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-blue-600/30 flex items-center justify-center text-2xl font-black text-blue-300">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-slate-100 font-bold text-lg">{{ $user->name }}</p>
                <p class="text-slate-400 text-sm">{{ $user->email }}</p>
                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full bg-blue-700/40 text-blue-300 text-xs font-bold">Admin</span>
            </div>
        </div>
    </div>

    {{-- Change Password Form --}}
    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-6">
        <h2 class="text-base font-bold text-slate-100 mb-5">🔐 Ganti Password</h2>

        <form method="POST" action="{{ route('admin.profile.password') }}">
            @csrf

            <div class="mb-4">
                <label class="text-xs text-slate-400 mb-1 block">Password / OTP Saat Ini</label>
                <input type="password" name="current_password" required
                       placeholder="Password lama atau OTP dari email"
                       class="w-full bg-slate-700/60 border border-slate-600/50 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('current_password') border-red-500 @enderror">
                @error('current_password')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-slate-500 text-xs mt-1">Jika baru pertama login, masukkan OTP yang dikirim ke email Anda.</p>
            </div>

            <div class="mb-4">
                <div class="flex items-center justify-between mb-1">
                    <label class="text-xs text-slate-400">Password Baru</label>
                    <button type="button" id="btn-generate"
                            class="text-xs font-bold text-blue-400 hover:text-blue-300 transition flex items-center gap-1">
                        ✨ Buat password kuat
                    </button>
                </div>
                <div class="relative">
                    <input type="password" name="new_password" id="new_password" required
                           placeholder="Min. 8 karakter — huruf besar, kecil, angka, simbol"
                           class="w-full bg-slate-700/60 border border-slate-600/50 rounded-xl px-4 py-2.5 pr-10 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('new_password') border-red-500 @enderror">
                    <button type="button" id="toggle-new"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 text-xs">👁</button>
                </div>
                @error('new_password')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror

                {{-- Strength meter --}}
                <div class="mt-3" id="strength-wrap">
                    <div class="flex gap-1 mb-2">
                        <div class="h-1 flex-1 rounded-full bg-slate-700" id="bar1"></div>
                        <div class="h-1 flex-1 rounded-full bg-slate-700" id="bar2"></div>
                        <div class="h-1 flex-1 rounded-full bg-slate-700" id="bar3"></div>
                        <div class="h-1 flex-1 rounded-full bg-slate-700" id="bar4"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                        <span class="text-xs flex items-center gap-1" id="req-len">⬜ Min. 8 karakter</span>
                        <span class="text-xs flex items-center gap-1" id="req-upper">⬜ Huruf besar (A-Z)</span>
                        <span class="text-xs flex items-center gap-1" id="req-lower">⬜ Huruf kecil (a-z)</span>
                        <span class="text-xs flex items-center gap-1" id="req-num">⬜ Angka (0-9)</span>
                        <span class="text-xs flex items-center gap-1 col-span-2" id="req-sym">⬜ Karakter khusus (!@#$%^&*)</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="text-xs text-slate-400 mb-1 block">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                           placeholder="Ulangi password baru"
                           class="w-full bg-slate-700/60 border border-slate-600/50 rounded-xl px-4 py-2.5 pr-10 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                    <button type="button" id="toggle-confirm"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-200 text-xs">👁</button>
                </div>
                <p class="text-xs mt-1" id="match-msg"></p>
            </div>

            <button type="submit" id="btn-submit"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold rounded-xl transition text-sm">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script @nonce>
(function () {
    const newPw   = document.getElementById('new_password');
    const confPw  = document.getElementById('new_password_confirmation');
    const matchMsg = document.getElementById('match-msg');
    const bars    = [1,2,3,4].map(i => document.getElementById('bar' + i));
    const reqs    = {
        len:   document.getElementById('req-len'),
        upper: document.getElementById('req-upper'),
        lower: document.getElementById('req-lower'),
        num:   document.getElementById('req-num'),
        sym:   document.getElementById('req-sym'),
    };

    const barColors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-emerald-500'];

    function checkReq(el, pass) {
        if (pass) {
            el.textContent = el.textContent.replace('⬜', '✅');
            el.className = 'text-xs flex items-center gap-1 text-emerald-400';
        } else {
            el.textContent = el.textContent.replace('✅', '⬜');
            el.className = 'text-xs flex items-center gap-1 text-slate-500';
        }
    }

    function evaluate(val) {
        const checks = {
            len:   val.length >= 8,
            upper: /[A-Z]/.test(val),
            lower: /[a-z]/.test(val),
            num:   /[0-9]/.test(val),
            sym:   /[@$!%*#?&^()\-_+=\[\]{};:'",./<>]/.test(val),
        };
        Object.entries(checks).forEach(([k, pass]) => checkReq(reqs[k], pass));

        const score = Object.values(checks).filter(Boolean).length;
        bars.forEach((b, i) => {
            b.className = 'h-1 flex-1 rounded-full ' + (i < score ? barColors[Math.min(score - 1, 3)] : 'bg-slate-700');
        });
        return checks;
    }

    newPw.addEventListener('input', () => {
        evaluate(newPw.value);
        checkMatch();
    });

    confPw.addEventListener('input', checkMatch);

    function checkMatch() {
        if (!confPw.value) { matchMsg.textContent = ''; return; }
        if (newPw.value === confPw.value) {
            matchMsg.textContent = '✅ Password cocok';
            matchMsg.className = 'text-xs mt-1 text-emerald-400';
        } else {
            matchMsg.textContent = '❌ Password tidak cocok';
            matchMsg.className = 'text-xs mt-1 text-red-400';
        }
    }

    // Toggle visibility
    function makeToggle(btnId, inputId) {
        document.getElementById(btnId).addEventListener('click', () => {
            const inp = document.getElementById(inputId);
            inp.type = inp.type === 'password' ? 'text' : 'password';
        });
    }
    makeToggle('toggle-new', 'new_password');
    makeToggle('toggle-confirm', 'new_password_confirmation');

    // Generate strong password
    document.getElementById('btn-generate').addEventListener('click', () => {
        const upper  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const lower  = 'abcdefghijklmnopqrstuvwxyz';
        const digits = '0123456789';
        const syms   = '@$!%*#?&^_-';
        const all    = upper + lower + digits + syms;
        let pw = [
            upper[Math.floor(Math.random() * upper.length)],
            upper[Math.floor(Math.random() * upper.length)],
            lower[Math.floor(Math.random() * lower.length)],
            lower[Math.floor(Math.random() * lower.length)],
            digits[Math.floor(Math.random() * digits.length)],
            digits[Math.floor(Math.random() * digits.length)],
            syms[Math.floor(Math.random() * syms.length)],
            syms[Math.floor(Math.random() * syms.length)],
        ];
        for (let i = pw.length; i < 14; i++) {
            pw.push(all[Math.floor(Math.random() * all.length)]);
        }
        // Shuffle
        pw = pw.sort(() => Math.random() - 0.5).join('');
        newPw.value  = pw;
        confPw.value = pw;
        newPw.type   = 'text';
        confPw.type  = 'text';
        evaluate(pw);
        checkMatch();

        // Copy to clipboard
        navigator.clipboard.writeText(pw).catch(() => {});

        const btn = document.getElementById('btn-generate');
        const orig = btn.textContent;
        btn.textContent = '✅ Tersalin ke clipboard!';
        setTimeout(() => btn.textContent = orig, 2500);
    });
})();
</script>
@endpush

@endsection
