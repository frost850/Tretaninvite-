@extends('admin.layout')

@section('title', 'Manajemen Admin')

@section('content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-6 fade-up stagger-1">
    <div>
        <p class="text-slate-500 text-xs">Kelola akses admin</p>
        <h1 class="text-2xl font-black text-slate-100 mt-0.5">Manajemen Admin</h1>
        <p class="text-slate-500 text-sm mt-0.5">Tambah atau hapus akun sub-admin. OTP akan dikirim via email.</p>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-emerald-900/50 border border-emerald-700/60 text-emerald-300 text-sm fade-up stagger-1">
    ✅ {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-xl bg-red-900/50 border border-red-700/60 text-red-300 text-sm fade-up stagger-1">
    ⚠️ {{ session('error') }}
</div>
@endif

{{-- Add Admin Form --}}
<div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-6 mb-6 fade-up stagger-2">
    <h2 class="text-lg font-bold text-slate-100 mb-4">➕ Tambah Admin Baru</h2>
    <form method="POST" action="{{ route('admin.admins.store') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div class="flex-1 min-w-52">
            <label class="text-xs text-slate-400 mb-1 block">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   placeholder="Nama admin"
                   class="w-full bg-slate-700/60 border border-slate-600/50 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex-1 min-w-64">
            <label class="text-xs text-slate-400 mb-1 block">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   placeholder="email@contoh.com"
                   class="w-full bg-slate-700/60 border border-slate-600/50 rounded-xl px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl transition whitespace-nowrap">
            Tambah &amp; Kirim OTP
        </button>
    </form>
</div>

{{-- Super Admin Info --}}
<div class="bg-slate-800/40 border border-slate-700/40 rounded-2xl p-4 mb-6 fade-up stagger-3">
    <h2 class="text-sm font-bold text-slate-300 mb-3">👑 Super Admin (hanya konfigurasi)</h2>
    <div class="flex items-center gap-4">
        <div class="w-9 h-9 rounded-full bg-amber-600/30 flex items-center justify-center text-base">👑</div>
        <div>
            <p class="text-sm text-slate-100 font-semibold">{{ $superAdminEmail ?? '-' }}</p>
            <p class="text-xs text-slate-500">Login via ADMIN_PASSWORD di .env — tidak bisa diedit dari sini</p>
        </div>
        <span class="ml-auto px-3 py-1 rounded-full bg-amber-700/40 text-amber-300 text-xs font-bold">Super Admin</span>
    </div>
</div>

{{-- Sub-admins Table --}}
<div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl overflow-hidden fade-up stagger-3">
    <div class="px-6 py-4 border-b border-slate-700/50 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-300">👥 Sub-Admin ({{ $admins->count() }})</h2>
    </div>

    @if($admins->isEmpty())
    <div class="px-6 py-12 text-center text-slate-500">
        <p class="text-3xl mb-2">🙍</p>
        <p class="text-sm">Belum ada sub-admin. Tambahkan di atas.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-700/40 text-xs text-slate-400 uppercase">
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Login Terakhir</th>
                    <th class="px-6 py-3 text-left">Ditambahkan Oleh</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/30">
                @foreach($admins as $admin)
                <tr class="hover:bg-slate-700/20 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600/30 flex items-center justify-center text-sm font-bold text-blue-300">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-slate-100 font-medium">{{ $admin->name }}</p>
                                @if($admin->must_change_password)
                                    <span class="text-amber-400 text-xs">⚠ Belum ganti password</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-300">{{ $admin->email }}</td>
                    <td class="px-6 py-4">
                        @if($admin->is_active)
                            <span class="px-2.5 py-1 rounded-full bg-emerald-900/50 text-emerald-400 text-xs font-bold">✓ Aktif</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-slate-700/60 text-slate-400 text-xs font-bold">✗ Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-400 text-xs">
                        {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Belum pernah' }}
                    </td>
                    <td class="px-6 py-4 text-slate-400 text-xs">{{ $admin->added_by ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-2">
                            {{-- Reset OTP --}}
                            <form method="POST" action="{{ route('admin.admins.resetOtp', $admin) }}"
                                  data-adm-confirm="Reset OTP dan kirim ulang ke email {{ $admin->email }}?">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="px-3 py-1.5 bg-amber-700/50 hover:bg-amber-600/60 text-amber-300 text-xs font-bold rounded-lg transition">
                                    🔑 Reset OTP
                                </button>
                            </form>

                            {{-- Toggle Active --}}
                            <form method="POST" action="{{ route('admin.admins.toggleActive', $admin) }}"
                                  data-adm-confirm="{{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }} admin ini?">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="px-3 py-1.5 {{ $admin->is_active ? 'bg-slate-700/60 hover:bg-slate-600/60 text-slate-300' : 'bg-emerald-800/50 hover:bg-emerald-700/50 text-emerald-300' }} text-xs font-bold rounded-lg transition">
                                    {{ $admin->is_active ? '⛔ Nonaktifkan' : '✓ Aktifkan' }}
                                </button>
                            </form>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}"
                                  data-adm-confirm="Hapus admin {{ $admin->name }}? Tindakan ini tidak bisa dibatalkan."
                                  data-adm-ajax data-adm-danger data-adm-remove-closest="tr">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1.5 bg-red-900/50 hover:bg-red-800/60 text-red-400 text-xs font-bold rounded-lg transition">
                                    🗑 Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
