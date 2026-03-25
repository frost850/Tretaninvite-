@extends('customer.layout')

@section('title', 'Kelola Tamu — ' . $wedding->bride_name . ($wedding->groom_name ? ' & ' . $wedding->groom_name : ''))

@section('portal_badge')
    @if($wedding->isVip())
        <span class="text-xs text-yellow-400 font-semibold bg-yellow-500/10 px-2 py-0.5 rounded-md border border-yellow-500/20">VIP</span>
    @else
        <span class="text-xs text-sky-400 font-semibold bg-sky-500/10 px-2 py-0.5 rounded-md border border-sky-500/20">Premium</span>
    @endif
@endsection

@section('nav')
    <a href="{{ route('my.vip.dashboard', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('my.vip.guests', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.guests') ? 'active' : '' }}">Tamu</a>
    @if($wedding->isVip() && $wedding->guestbook_enabled)
    <a href="{{ route('my.vip.guestbook', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.guestbook') ? 'active' : '' }}">Guestbook</a>
    @endif
    @if($wedding->isVip())
    <a href="{{ route('my.vip.qr-codes', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.qr-codes') ? 'active' : '' }}">QR Code</a>
    <a href="{{ route('my.vip.scan', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.scan') ? 'active' : '' }}">Scanner</a>
    @endif
    <a href="{{ route('my.vip.print', $token) }}"
       class="cust-nav-link {{ request()->routeIs('my.vip.print') ? 'active' : '' }}">🌸 Cetak Fisik</a>
@endsection

@section('content')

{{-- Header --}}
<div class="mb-6 fade-up">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('my.vip.dashboard', $token) }}" class="text-slate-500 text-sm hover:text-slate-300 transition">← Dashboard</a>
            <h1 class="text-2xl font-black text-slate-100 mt-1">Kelola Tamu</h1>
            <p class="text-slate-400 text-sm mt-0.5">
                {{ $wedding->bride_name }}{{ $wedding->groom_name ? ' & ' . $wedding->groom_name : '' }}
                &mdash; <span class="text-sky-400 font-semibold">{{ $guests->total() }} tamu</span>
            </p>
        </div>
        {{-- tombol cetak fisik dihapus untuk premium --}}
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-900/40 border border-green-700/40 text-green-300 text-sm fade-up">
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="mb-4 px-4 py-3 rounded-xl bg-red-900/40 border border-red-700/40 text-red-300 text-sm fade-up">
    {{ $errors->first() }}
</div>
@endif

{{-- Form tambah tamu --}}
<div class="mb-6 fade-up stagger-1">
    <form method="POST" action="{{ route('my.vip.guests.store', $token) }}"
          class="rounded-xl bg-slate-800 border border-white/5 p-5">
        @csrf
        <h2 class="text-slate-300 font-semibold mb-4">Tambah Tamu</h2>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="guest_name" value="{{ old('guest_name') }}"
                   placeholder="Nama tamu..."
                   class="flex-1 rounded-lg px-4 py-2.5 text-sm border border-white/10 focus:border-sky-500/50 focus:outline-none focus:ring-1 focus:ring-sky-500/30 transition"
                   autocomplete="off" required maxlength="255">
            <input type="text" name="phone" value="{{ old('phone') }}"
                   placeholder="No. WhatsApp (opsional)"
                   class="w-full sm:w-48 rounded-lg px-4 py-2.5 text-sm border border-white/10 focus:border-sky-500/50 focus:outline-none focus:ring-1 focus:ring-sky-500/30 transition"
                   autocomplete="off" maxlength="20">
            <button type="submit"
                    class="shrink-0 px-5 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm font-semibold transition">
                + Tambah
            </button>
        </div>
    </form>
</div>

{{-- Daftar tamu --}}
<div class="fade-up stagger-2">
    @if($guests->isEmpty())
    <div class="rounded-xl bg-slate-800/50 border border-dashed border-white/10 p-10 text-center">
        <div class="text-3xl mb-3">👥</div>
        <div class="text-slate-400 text-sm">Belum ada tamu. Tambahkan tamu pertama di atas.</div>
    </div>
    @else
    <div class="rounded-xl bg-slate-800 border border-white/5 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/5">
                    <th class="text-left text-slate-400 text-xs font-semibold uppercase tracking-wider px-5 py-3">#</th>
                    <th class="text-left text-slate-400 text-xs font-semibold uppercase tracking-wider px-5 py-3">Nama Tamu</th>
                    <th class="text-left text-slate-400 text-xs font-semibold uppercase tracking-wider px-5 py-3 hidden sm:table-cell">WhatsApp</th>
                    <th class="text-left text-slate-400 text-xs font-semibold uppercase tracking-wider px-5 py-3 hidden md:table-cell">Status</th>
                    <th class="text-left text-slate-400 text-xs font-semibold uppercase tracking-wider px-5 py-3">Bagikan</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($guests as $i => $guest)
                @php
                    $inviteUrl = url('/' . $wedding->slug) . '?to=' . urlencode($guest->guest_name);
                    $waPhone = preg_replace('/[^0-9]/', '', $guest->phone ?? '');
                    if ($waPhone) {
                        if (str_starts_with($waPhone, '0')) { $waPhone = '62' . substr($waPhone, 1); }
                        elseif (str_starts_with($waPhone, '8')) { $waPhone = '62' . $waPhone; }
                    }
                    $waText = 'Halo ' . $guest->guest_name . ', berikut link undangan Anda: ' . $inviteUrl;
                    $waUrl  = $waPhone
                        ? 'https://wa.me/' . $waPhone . '?text=' . rawurlencode($waText)
                        : 'https://wa.me/?text=' . rawurlencode($waText);
                @endphp
                <tr class="hover:bg-white/[0.02] transition"
                    x-data="{ confirm: false, editing: false, copied: false, name: {{ json_encode($guest->guest_name) }}, phone: {{ json_encode($guest->phone ?? '') }} }">
                    <td class="px-5 py-3 text-slate-500 text-xs">{{ $guests->firstItem() + $i }}</td>
                    {{-- Nama: normal atau form edit inline --}}
                    <td class="px-5 py-3">
                        <template x-if="!editing">
                            <div>
                                <div class="text-slate-100 font-medium">{{ $guest->guest_name }}</div>
                                @if($guest->group_name)
                                <div class="text-slate-500 text-xs">{{ $guest->group_name }}</div>
                                @endif
                            </div>
                        </template>
                        <template x-if="editing">
                            <input x-model="name" type="text" maxlength="255"
                                   class="w-full rounded-lg px-3 py-1.5 text-sm border border-sky-500/40 bg-slate-900 text-slate-100 focus:outline-none focus:ring-1 focus:ring-sky-500/50"
                                   placeholder="Nama tamu">
                        </template>
                    </td>
                    {{-- WA: normal atau form edit inline --}}
                    <td class="px-5 py-3 text-slate-400 text-xs hidden sm:table-cell">
                        <template x-if="!editing">
                            <span>{{ $guest->phone ?? '—' }}</span>
                        </template>
                        <template x-if="editing">
                            <input x-model="phone" type="text" maxlength="20"
                                   class="w-32 rounded-lg px-3 py-1.5 text-sm border border-sky-500/40 bg-slate-900 text-slate-100 focus:outline-none focus:ring-1 focus:ring-sky-500/50"
                                   placeholder="No. WA">
                        </template>
                    </td>
                    <td class="px-5 py-3 hidden md:table-cell">
                        @if($guest->checked_in_at)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-teal-900/50 border border-teal-700/40 text-teal-400 text-xs font-semibold">✓ Check-in</span>
                        @elseif($guest->is_attending === true)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-green-900/50 border border-green-700/40 text-green-400 text-xs">Hadir</span>
                        @elseif($guest->is_attending === false)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-red-900/50 border border-red-700/40 text-red-400 text-xs">Tidak Hadir</span>
                        @elseif($guest->first_opened_at)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-900/50 border border-blue-700/40 text-blue-400 text-xs">Dibuka</span>
                        @else
                            <span class="text-slate-500 text-xs">Belum dibuka</span>
                        @endif
                    </td>
                    {{-- Kolom Bagikan: WA + Salin Link --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            @if($waPhone)
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                               title="Kirim via WhatsApp"
                               class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-green-900/30 border border-green-700/30 text-green-400 text-xs font-semibold hover:bg-green-900/60 transition">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                WA
                            </a>
                            @endif
                            <button @click="navigator.clipboard.writeText('{{ $inviteUrl }}').then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                    title="Salin link undangan"
                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-lg border text-xs font-semibold transition"
                                    :class="copied ? 'bg-sky-900/50 border-sky-600/50 text-sky-300' : 'bg-slate-700/40 border-slate-600/30 text-slate-300 hover:bg-slate-700/70'">
                                <span x-show="!copied">Salin Link</span>
                                <span x-show="copied" x-cloak>✓ Tersalin!</span>
                            </button>
                        </div>
                    </td>
                    {{-- Kolom aksi: Edit / Simpan / Hapus --}}
                    <td class="px-5 py-3 text-right">
                        {{-- Mode normal: tombol Edit + Hapus --}}
                        <template x-if="!editing && !confirm">
                            <div class="flex items-center justify-end gap-1">
                                <button @click="editing = true; confirm = false"
                                        class="text-slate-400 hover:text-sky-400 transition text-xs px-2 py-1 rounded hover:bg-sky-900/20">
                                    Edit
                                </button>
                                <button @click="confirm = true"
                                        class="text-slate-500 hover:text-red-400 transition text-xs px-2 py-1 rounded hover:bg-red-900/20">
                                    Hapus
                                </button>
                            </div>
                        </template>
                        {{-- Mode edit: form PATCH --}}
                        <template x-if="editing">
                            <form method="POST"
                                  action="{{ route('my.vip.guests.update', [$token, $guest->id]) }}"
                                  class="flex items-center justify-end gap-1">
                                @csrf @method('PATCH')
                                <input type="hidden" name="guest_name" :value="name">
                                <input type="hidden" name="phone" :value="phone">
                                <button type="submit"
                                        class="text-sky-400 hover:text-sky-300 text-xs font-semibold px-2 py-1 rounded hover:bg-sky-900/20 transition">
                                    Simpan
                                </button>
                                <button type="button" @click="editing = false"
                                        class="text-slate-400 hover:text-slate-200 text-xs px-2 py-1 rounded hover:bg-white/5 transition">
                                    Batal
                                </button>
                            </form>
                        </template>
                        {{-- Konfirmasi hapus --}}
                        <template x-if="confirm">
                            <div class="flex items-center justify-end gap-1">
                                <span class="text-slate-400 text-xs">Yakin?</span>
                                <form method="POST"
                                      action="{{ route('my.vip.guests.destroy', [$token, $guest->id]) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-400 hover:text-red-300 text-xs font-semibold px-2 py-1 rounded hover:bg-red-900/20 transition">
                                        Ya, hapus
                                    </button>
                                </form>
                                <button @click="confirm = false"
                                        class="text-slate-400 hover:text-slate-200 text-xs px-2 py-1 rounded hover:bg-white/5 transition">
                                    Batal
                                </button>
                            </div>
                        </template>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($guests->hasPages())
        <div class="px-5 py-4 border-t border-white/5">
            {{ $guests->appends(['token' => $token])->links() }}
        </div>
        @endif
    </div>
    @endif
</div>

{{-- Info link undangan --}}
<div class="mt-6 rounded-xl border border-white/5 bg-slate-800/50 p-4 fade-up">
    <div class="text-slate-400 text-xs mb-2 font-semibold uppercase tracking-wider">Link Undangan Tamu</div>
    <div class="text-slate-300 text-sm">
        Link personal per tamu menggunakan format:<br>
        <code class="text-sky-400 text-xs bg-sky-900/20 px-2 py-1 rounded mt-1 inline-block">
            {{ url('/' . $wedding->slug) }}?to=<em>nama-tamu</em>
        </code>
    </div>
</div>

@endsection
