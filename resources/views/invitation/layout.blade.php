<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if(empty($wedding->groom_name))
            Undangan Ulang Tahun {{ $wedding->bride_name }}
        @else
            Undangan {{ $wedding->bride_name }} & {{ $wedding->groom_name }}
        @endif
    </title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    @stack('fonts')
    @stack('styles')
</head>
<body>
    @if(!empty($isPreview) && isset($wedding))
        <div style="position:sticky;top:0;z-index:9999;display:flex;align-items:center;justify-content:space-between;gap:12px;padding:8px 16px;font-size:.875rem;font-weight:500;box-shadow:0 1px 4px rgba(0,0,0,.2);background:#f59e0b;color:#1c1917;">
            <a href="{{ route('packages.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;color:#1c1917;text-decoration:none;padding:4px 12px;background:rgba(0,0,0,.12);border-radius:999px;" onmouseover="this.style.background='rgba(0,0,0,.22)'" onmouseout="this.style.background='rgba(0,0,0,.12)'">
                ← Kembali ke Paket
            </a>
            <span style="font-size:.75rem;opacity:.75;">PREVIEW — Tampilan contoh template <strong>{{ $wedding->template }}</strong></span>
            @if(session('admin_authenticated'))
                <a href="{{ route('admin.weddings.create.form', $wedding->template) }}" style="font-weight:700;color:#1c1917;text-decoration:underline;">Buat Undangan →</a>
            @else
                <a href="{{ route('orders.create', ['template' => $wedding->template]) }}" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;color:#1c1917;padding:4px 12px;background:rgba(0,0,0,.12);border-radius:999px;text-decoration:none;" onmouseover="this.style.background='rgba(0,0,0,.22)'" onmouseout="this.style.background='rgba(0,0,0,.12)'">
                    Pesan Template Ini →
                </a>
            @endif
        </div>
    @elseif(session('admin_authenticated') && isset($wedding))
        <div style="position:fixed;top:0;left:0;right:0;z-index:9999;background:rgba(28,25,23,.88);backdrop-filter:blur(8px);display:flex;align-items:center;justify-content:space-between;padding:6px 16px;gap:12px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;">
                <a href="{{ route('admin.weddings.index') }}"
                   style="color:#fbbf24;font-size:.75rem;font-weight:600;text-decoration:none;letter-spacing:.05em;display:flex;align-items:center;gap:6px;"
                   onmouseover="this.style.color='#fde68a'" onmouseout="this.style.color='#fbbf24'">
                    ← Daftar Undangan
                </a>
                <span style="color:rgba(255,255,255,.2);font-size:.7rem;">|</span>
                @php
                    $isBirthdayTemplate  = str_starts_with($wedding->template ?? '', 'birthday');
                    $isGreetingTemplate  = str_starts_with($wedding->template ?? '', 'greeting');
                    $editRoute = $isBirthdayTemplate
                        ? route('admin.birthdays.edit', $wedding->id)
                        : ($isGreetingTemplate
                            ? route('admin.greetings.edit', $wedding->id)
                            : route('admin.weddings.edit', $wedding->id));
                @endphp
                <a href="{{ $editRoute }}"
                   style="color:#fbbf24;font-size:.75rem;font-weight:600;text-decoration:none;letter-spacing:.05em;"
                   onmouseover="this.style.color='#fde68a'" onmouseout="this.style.color='#fbbf24'">
                    ✏️ Edit Undangan
                </a>
                @if(!$isGreetingTemplate)
                <span style="color:rgba(255,255,255,.2);font-size:.7rem;">|</span>
                <a href="{{ route('admin.guests.index', ['wedding_id' => $wedding->id]) }}"
                   style="color:#fbbf24;font-size:.75rem;font-weight:600;text-decoration:none;letter-spacing:.05em;"
                   onmouseover="this.style.color='#fde68a'" onmouseout="this.style.color='#fbbf24'">
                    👥 Daftar Tamu
                </a>
                @endif
            </div>
            <span style="color:rgba(255,200,100,.6);font-size:.68rem;font-style:italic;">
                🔒 Bar ini hanya Anda (admin) yang melihat — tamu tidak melihat ini
            </span>
        </div>
        {{-- Spacer agar konten tidak tertutup bar --}}
        <div style="height:38px;"></div>
    @elseif(isset($wedding) && $wedding->isTrial() && isset($guest))
        {{-- Trial banner — hanya untuk tamu bernama (?to=) --}}
        <div style="position:sticky;top:0;z-index:9999;text-align:center;padding:8px 16px;font-size:.875rem;font-weight:600;box-shadow:0 1px 4px rgba(0,0,0,.2);background:#fbbf24;color:#451a03;">
            ⏱ <strong>Mode Percobaan</strong> — Aktif s/d {{ $wedding->trial_expires_at->format('d M Y') }}.
            <a href="{{ route('orders.create', ['template' => $wedding->template]) }}" style="text-decoration:underline;font-weight:700;margin-left:4px;">Pesan Sekarang →</a>
        </div>
    @elseif(isset($wedding) && $wedding->isArchived() && isset($guest))
        {{-- Mode Arsip — hanya untuk tamu bernama (?to=) --}}
        <div style="position:sticky;top:0;z-index:9999;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:8px 16px;font-size:.8rem;font-weight:600;box-shadow:0 1px 6px rgba(0,0,0,.3);background:linear-gradient(135deg,#1e293b,#0f172a);border-bottom:1px solid rgba(255,255,255,.08);color:rgba(255,255,255,.7);">
            <span>🗄️ <strong style="color:#e2e8f0;">Mode Arsip</strong> — Masa aktif habis. Undangan bisa dilihat, RSVP &amp; interaksi sudah ditutup.</span>
            <a href="{{ route('orders.create', ['template' => $wedding->template]) }}" style="white-space:nowrap;color:#fbbf24;font-weight:700;text-decoration:none;padding:3px 10px;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);border-radius:8px;" onmouseover="this.style.background='rgba(245,158,11,.25)'" onmouseout="this.style.background='rgba(245,158,11,.15)'">Perpanjang →</a>
        </div>
    @elseif(isset($wedding) && $wedding->isExpired() && isset($guest))
        {{-- Expired banner — hanya untuk tamu bernama (?to=) --}}
        <div style="position:sticky;top:0;z-index:9999;text-align:center;padding:8px 16px;font-size:.875rem;font-weight:600;box-shadow:0 1px 4px rgba(0,0,0,.2);background:#ef4444;color:#fff;">
            ⚠️ Masa aktif undangan ini sudah berakhir.
        </div>
    @endif
    @yield('content')
    @stack('scripts')
</body>
</html>
