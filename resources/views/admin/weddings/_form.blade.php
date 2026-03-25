{{-- Form untuk Wedding/Pernikahan --}}
{{-- Variables: $w (Wedding|null untuk create), $template (string), $templateInfo (array) --}}

@php
    $old     = fn($k, $def='') => old($k, $w->$k ?? $def);
    $package = $package ?? ($w->package ?? 'basic');
@endphp

<input type="hidden" name="template" value="{{ $template }}">

{{-- ═══════════════════════════════════════════════════════════════
     ALAMAT UNDANGAN
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">🔗 Alamat Undangan</h2>
    <div>
        <label class="field-label" for="slug">Slug (URL)*</label>
        <input type="text" name="slug" id="slug"
               value="{{ old('slug', $w->slug ?? '') }}"
               placeholder="dewanata-anni" required
               @isset($w) readonly @endisset
               class="field-input @isset($w) bg-stone-50 text-stone-500 cursor-not-allowed @endisset">
        <p class="field-hint">
            Link undangan: <strong>{{ url('/') }}/<span id="slug-preview">{{ old('slug', $w->slug ?? 'dewanata-anni') }}</span></strong>
            @isset($w) · Slug tidak bisa diubah setelah dibuat. @endisset
        </p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     FIELD SPESIFIK PERNIKAHAN
═══════════════════════════════════════════════════════════════ --}}
@include('admin.weddings._form-wedding', ['w' => $w ?? null, 'template' => $template, 'templateInfo' => $templateInfo, 'old' => $old, 'package' => $package])

{{-- ═══════════════════════════════════════════════════════════════
     FOTO PROFIL MEMPELAI (Edit Mode Only)
═══════════════════════════════════════════════════════════════ --}}
@isset($w)

{{-- ═══════════════════════════════════════════════════════════════
     FOTO PROFIL MEMPELAI (Edit Mode Only)
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">👤 Foto Profil Mempelai</h2>
    <p class="text-sm text-stone-600 mb-1">Foto profil yang tampil di kartu mempelai dalam undangan.</p>
    @if($package === 'vip')
    <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-4">
        ✦ <strong>VIP Royal:</strong> Foto wanita & pria juga otomatis menjadi <strong>latar belakang bagian Hero</strong> (split-screen kiri & kanan) dan <strong>background galeri foto</strong>.
    </p>
    @else
    <p class="text-xs text-stone-500 mb-4">JPG/PNG, maks 2MB per foto.</p>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Foto Mempelai Wanita --}}
        <div class="space-y-3">
            <label class="field-label font-semibold">👰 Foto Mempelai Wanita</label>
            @if($package === 'vip')
            <div class="flex flex-wrap gap-1 mb-1">
                <span class="text-xs bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full">Kartu Profil</span>
                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Latar Hero Kiri</span>
                <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full">BG Galeri</span>
            </div>
            @endif
            @if($w->bride_photo)
            <div class="relative group">
                <img src="{{ asset('storage/' . $w->bride_photo) }}" class="w-full h-64 object-cover rounded-lg border-2 border-pink-200 shadow-md">
                <button type="button" onclick="deleteProfilePhoto('bride', {{ $w->id }})"
                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                    🗑️ Hapus
                </button>
            </div>
            @else
            <div class="w-full h-64 bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg border-2 border-dashed border-pink-300 flex items-center justify-center" id="bride-photo-preview">
                <span class="text-6xl">👰</span>
            </div>
            @endif
            <input type="file" accept="image/jpeg,image/png,image/jpg" id="bride-photo-input" class="hidden"
                   onchange="previewAndRevealUpload(this, 'bride-photo-preview', 'bride-upload-btn')">
            <button type="button" onclick="document.getElementById('bride-photo-input').click()"
                    class="w-full px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg font-medium text-sm transition-colors">
                📷 Pilih Foto
            </button>
            <button type="button" id="bride-upload-btn" onclick="uploadProfilePhoto('bride', {{ $w->id }})"
                    class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                ✓ Upload Foto Wanita
            </button>
            <p class="text-xs text-stone-500">JPG/PNG, maks 2MB</p>
        </div>

        {{-- Foto Mempelai Pria --}}
        <div class="space-y-3">
            <label class="field-label font-semibold">🤵 Foto Mempelai Pria</label>
            @if($package === 'vip')
            <div class="flex flex-wrap gap-1 mb-1">
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Kartu Profil</span>
                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Latar Hero Kanan</span>
                <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full">BG Galeri</span>
            </div>
            @endif
            @if($w->groom_photo)
            <div class="relative group">
                <img src="{{ asset('storage/' . $w->groom_photo) }}" class="w-full h-64 object-cover rounded-lg border-2 border-blue-200 shadow-md">
                <button type="button" onclick="deleteProfilePhoto('groom', {{ $w->id }})"
                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                    🗑️ Hapus
                </button>
            </div>
            @else
            <div class="w-full h-64 bg-gradient-to-br from-blue-50 to-sky-50 rounded-lg border-2 border-dashed border-blue-300 flex items-center justify-center" id="groom-photo-preview">
                <span class="text-6xl">🤵</span>
            </div>
            @endif
            <input type="file" accept="image/jpeg,image/png,image/jpg" id="groom-photo-input" class="hidden"
                   onchange="previewAndRevealUpload(this, 'groom-photo-preview', 'groom-upload-btn')">
            <button type="button" onclick="document.getElementById('groom-photo-input').click()"
                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors">
                📷 Pilih Foto
            </button>
            <button type="button" id="groom-upload-btn" onclick="uploadProfilePhoto('groom', {{ $w->id }})"
                    class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                ✓ Upload Foto Pria
            </button>
            <p class="text-xs text-stone-500">JPG/PNG, maks 2MB</p>
        </div>

        {{-- Foto Couple --}}
        <div class="space-y-3">
            <label class="field-label font-semibold">💑 Foto Couple / Bersama</label>
            @if($package === 'vip')
            <div class="flex flex-wrap gap-1 mb-1">
                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">Foto Bersama (opsional)</span>
            </div>
            @endif
            @if($w->couple_photo)
            <div class="relative group">
                <img src="{{ asset('storage/' . $w->couple_photo) }}" class="w-full h-64 object-cover rounded-lg border-2 border-purple-200 shadow-md">
                <button type="button" onclick="deleteProfilePhoto('couple', {{ $w->id }})"
                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                    🗑️ Hapus
                </button>
            </div>
            @else
            <div class="w-full h-64 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border-2 border-dashed border-purple-300 flex items-center justify-center" id="couple-photo-preview">
                <span class="text-6xl">💑</span>
            </div>
            @endif
            <input type="file" accept="image/jpeg,image/png,image/jpg" id="couple-photo-input" class="hidden"
                   onchange="previewAndRevealUpload(this, 'couple-photo-preview', 'couple-upload-btn')">
            <button type="button" onclick="document.getElementById('couple-photo-input').click()"
                    class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium text-sm transition-colors">
                📷 Pilih Foto
            </button>
            <button type="button" id="couple-upload-btn" onclick="uploadProfilePhoto('couple', {{ $w->id }})"
                    class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                ✓ Upload Foto Couple
            </button>
            <p class="text-xs text-stone-500">JPG/PNG, maks 2MB</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     BACKGROUND / LATAR UNDANGAN (VIP Royal Only)
═══════════════════════════════════════════════════════════════ --}}
@if($package === 'vip')
<div class="form-section border-2 border-yellow-200 bg-yellow-50/40">
    <h2 class="section-title">🌄 Background Isi Undangan <span class="ml-2 text-xs font-normal text-yellow-700 bg-yellow-200 px-2 py-0.5 rounded-full">VIP Royal</span></h2>
    <p class="text-sm text-stone-600 mb-4">Foto latar di berbagai bagian dalam halaman undangan. Berbeda dari foto profil di atas.</p>

    {{-- Diagram info --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6 text-xs">
        <div class="bg-white border border-yellow-200 rounded-lg p-3 text-center">
            <div class="text-2xl mb-1">🎭</div>
            <div class="font-semibold text-stone-700">Halaman Sampul</div>
            <div class="text-stone-500 mt-1">Splash screen pembuka<br>(sebelum undangan dibuka)</div>
            <div class="mt-2 text-yellow-700 font-medium">→ Foto Sampul Cover</div>
        </div>
        <div class="bg-white border border-indigo-200 rounded-lg p-3 text-center">
            <div class="text-2xl mb-1">🖼️</div>
            <div class="font-semibold text-stone-700">Bagian Hero</div>
            <div class="text-stone-500 mt-1">Split-screen besar setelah undangan dibuka (kiri & kanan)</div>
            <div class="mt-2 text-indigo-700 font-medium">→ Foto Wanita &amp; Pria</div>
        </div>
        <div class="bg-white border border-violet-200 rounded-lg p-3 text-center">
            <div class="text-2xl mb-1">🎞️</div>
            <div class="font-semibold text-stone-700">Bagian Galeri</div>
            <div class="text-stone-500 mt-1">Background kabur di belakang kartu galeri foto</div>
            <div class="mt-2 text-violet-700 font-medium">→ Foto Wanita &amp; Pria</div>
        </div>
    </div>

    {{-- ① Cover photo upload --}}
    <div class="border-t border-yellow-200 pt-5">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-lg">🎭</span>
            <h3 class="font-semibold text-stone-700">Background Halaman Sampul (Splash Screen)</h3>
        </div>
        <p class="text-xs text-stone-500 mb-4">Tampil sebagai background halaman pembuka sebelum tamu membuka undangan. Jika kosong, otomatis pakai foto mempelai wanita.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <div class="space-y-3">
                @if($w->cover_photo)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $w->cover_photo) }}" class="w-full h-64 object-cover rounded-xl border-2 border-yellow-300 shadow-md">
                    <div class="absolute top-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded">Saat ini digunakan</div>
                    <button type="button" onclick="deleteProfilePhoto('cover', {{ $w->id }})"
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        🗑️ Hapus
                    </button>
                </div>
                @else
                <div class="w-full h-64 bg-gradient-to-br from-yellow-50 to-amber-100 rounded-xl border-2 border-dashed border-yellow-400 flex flex-col items-center justify-center gap-2" id="cover-photo-preview">
                    <span class="text-4xl">🎭</span>
                    <span class="text-xs text-yellow-700">Belum ada foto sampul</span>
                    @if($w->bride_photo)
                    <span class="text-xs text-amber-600 bg-amber-100 px-2 py-1 rounded">⚠️ Fallback: foto mempelai wanita</span>
                    @endif
                </div>
                @endif
                <input type="file" accept="image/jpeg,image/png,image/jpg" id="cover-photo-input" class="hidden"
                       onchange="previewAndRevealUpload(this, 'cover-photo-preview', 'cover-upload-btn')">
                <button type="button" onclick="document.getElementById('cover-photo-input').click()"
                        class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium text-sm transition-colors">
                    📷 Pilih Foto Sampul
                </button>
                <button type="button" id="cover-upload-btn" onclick="uploadProfilePhoto('cover', {{ $w->id }})"
                        class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                    ✓ Upload Foto Sampul
                </button>
                <p class="text-xs text-stone-400">JPG/PNG, maks 5MB. Portrait/landscape beresolusi tinggi.</p>
            </div>
            <div class="bg-white/5 border border-stone-200/20 rounded-xl p-4 text-sm text-stone-400 space-y-2">
                <p class="font-semibold text-stone-300 text-xs">💡 Tips</p>
                <ul class="space-y-1.5 text-xs">
                    <li class="flex gap-2"><span class="text-yellow-500 shrink-0">◈</span> Foto prewedding terbaik atau foto couple</li>
                    <li class="flex gap-2"><span class="text-yellow-500 shrink-0">◈</span> Min. 1080×1350px (portrait) atau 1920×1080px (landscape)</li>
                    <li class="flex gap-2"><span class="text-yellow-500 shrink-0">◈</span> Overlay gelap otomatis diterapkan agar teks terbaca</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ② Hero background --}}
    <div class="border-t border-yellow-200 pt-5 mt-5">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-lg">🖼️</span>
            <h3 class="font-semibold text-stone-700">Background Bagian Hero (Split-Screen)</h3>
        </div>
        <p class="text-xs text-stone-500 mb-4">
            Setelah undangan dibuka, tampil layar split: foto wanita di kiri, foto pria di kanan.
            <strong>Foto yang sama juga menjadi background blur di bagian Galeri.</strong>
        </p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Hero kiri: bride --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full font-semibold">Sisi Kiri Hero</span>
                    <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full">BG Galeri (kiri)</span>
                </div>
                <label class="field-label font-semibold">👰 Foto Mempelai Wanita</label>
                @if($w->bride_photo)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $w->bride_photo) }}" class="w-full h-56 object-cover rounded-xl border-2 border-pink-300 shadow-md">
                    <div class="absolute top-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded">Saat ini digunakan</div>
                    <button type="button" onclick="deleteProfilePhoto('bride', {{ $w->id }})"
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        🗑️ Hapus
                    </button>
                </div>
                @else
                <div class="w-full h-56 bg-gradient-to-br from-pink-50 to-purple-50 rounded-xl border-2 border-dashed border-pink-300 flex flex-col items-center justify-center gap-2" id="hero-bride-preview">
                    <span class="text-4xl">👰</span>
                    <span class="text-xs text-pink-600">Belum ada foto wanita</span>
                </div>
                @endif
                <input type="file" accept="image/jpeg,image/png,image/jpg" id="hero-bride-input" class="hidden"
                       onchange="previewAndRevealUpload(this, 'hero-bride-preview', 'hero-bride-upload-btn')">
                <button type="button" onclick="document.getElementById('hero-bride-input').click()"
                        class="w-full px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg font-medium text-sm transition-colors">
                    📷 Pilih Foto Wanita
                </button>
                <button type="button" id="hero-bride-upload-btn" onclick="uploadProfilePhotoFromInput('bride', {{ $w->id }}, 'hero-bride-input')"
                        class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                    ✓ Upload Foto Wanita
                </button>
                <p class="text-xs text-stone-400">JPG/PNG, maks 5MB</p>
            </div>

            {{-- Hero kanan: groom --}}
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">Sisi Kanan Hero</span>
                    <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full">BG Galeri (kanan)</span>
                </div>
                <label class="field-label font-semibold">🤵 Foto Mempelai Pria</label>
                @if($w->groom_photo)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $w->groom_photo) }}" class="w-full h-56 object-cover rounded-xl border-2 border-blue-300 shadow-md">
                    <div class="absolute top-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded">Saat ini digunakan</div>
                    <button type="button" onclick="deleteProfilePhoto('groom', {{ $w->id }})"
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        🗑️ Hapus
                    </button>
                </div>
                @else
                <div class="w-full h-56 bg-gradient-to-br from-blue-50 to-sky-50 rounded-xl border-2 border-dashed border-blue-300 flex flex-col items-center justify-center gap-2" id="hero-groom-preview">
                    <span class="text-4xl">🤵</span>
                    <span class="text-xs text-blue-600">Belum ada foto pria</span>
                </div>
                @endif
                <input type="file" accept="image/jpeg,image/png,image/jpg" id="hero-groom-input" class="hidden"
                       onchange="previewAndRevealUpload(this, 'hero-groom-preview', 'hero-groom-upload-btn')">
                <button type="button" onclick="document.getElementById('hero-groom-input').click()"
                        class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors">
                    📷 Pilih Foto Pria
                </button>
                <button type="button" id="hero-groom-upload-btn" onclick="uploadProfilePhotoFromInput('groom', {{ $w->id }}, 'hero-groom-input')"
                        class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                    ✓ Upload Foto Pria
                </button>
                <p class="text-xs text-stone-400">JPG/PNG, maks 5MB</p>
            </div>
        </div>

        {{-- Galeri note --}}
        <div class="mt-4 flex items-start gap-3 bg-violet-50/10 border border-violet-300/20 rounded-xl p-4">
            <span class="text-2xl shrink-0">🎞️</span>
            <div class="text-xs text-stone-400">
                <strong class="text-stone-300">Background Galeri</strong> — Bagian galeri foto secara otomatis menggunakan foto wanita (sisi kiri, blur) dan foto pria (sisi kanan, blur) sebagai latar belakang dekoratif. Tidak perlu upload terpisah.
            </div>
        </div>
    </div>

    {{-- ③ Mempelai section bg --}}
    <div class="border-t border-yellow-200 pt-5 mt-5">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-lg">💑</span>
            <h3 class="font-semibold text-stone-700">Background Section Mempelai</h3>
        </div>
        <p class="text-xs text-stone-500 mb-4">Foto yang muncul sebagai latar di belakang kartu profil mempelai. Overlay gelap otomatis diterapkan.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <div class="space-y-3">
                @if($w->bg_mempelai_photo)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $w->bg_mempelai_photo) }}" class="w-full h-48 object-cover rounded-xl border-2 border-pink-300 shadow-md">
                    <div class="absolute top-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded">Saat ini digunakan</div>
                    <button type="button" onclick="deleteProfilePhoto('bg_mempelai', {{ $w->id }})"
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        🗑️ Hapus
                    </button>
                </div>
                @else
                <div class="w-full h-48 bg-gradient-to-br from-pink-50 to-rose-50 rounded-xl border-2 border-dashed border-pink-300 flex flex-col items-center justify-center gap-2" id="bg_mempelai-photo-preview">
                    <span class="text-3xl">💑</span>
                    <span class="text-xs text-pink-500">Belum ada foto background mempelai</span>
                </div>
                @endif
                <input type="file" accept="image/jpeg,image/png,image/jpg" id="bg_mempelai-photo-input" class="hidden"
                       onchange="previewAndRevealUpload(this, 'bg_mempelai-photo-preview', 'bg_mempelai-upload-btn')">
                <button type="button" onclick="document.getElementById('bg_mempelai-photo-input').click()"
                        class="w-full px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg font-medium text-sm transition-colors">
                    📷 Pilih Foto Background Mempelai
                </button>
                <button type="button" id="bg_mempelai-upload-btn" onclick="uploadProfilePhoto('bg_mempelai', {{ $w->id }})"
                        class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                    ✓ Upload
                </button>
                <p class="text-xs text-stone-400">JPG/PNG, maks 5MB</p>
            </div>
            <div class="bg-white/5 border border-stone-200/20 rounded-xl p-4 text-xs text-stone-400 space-y-2">
                <p class="font-semibold text-stone-300">Section: Mempelai</p>
                <p>Foto ini muncul sebagai background di bagian kartu profil wanita &amp; pria, dengan gelap otomatis 82%.</p>
                <p class="text-stone-500">Cocok menggunakan foto landscape pemandangan, bunga, atau momen romantis.</p>
            </div>
        </div>
    </div>

    {{-- ④ Acara section bg --}}
    <div class="border-t border-yellow-200 pt-5 mt-5">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-lg">📅</span>
            <h3 class="font-semibold text-stone-700">Background Section Acara</h3>
        </div>
        <p class="text-xs text-stone-500 mb-4">Foto yang muncul di belakang kartu-kartu acara (Akad, Resepsi, dll). Overlay gelap otomatis.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <div class="space-y-3">
                @if($w->bg_acara_photo)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $w->bg_acara_photo) }}" class="w-full h-48 object-cover rounded-xl border-2 border-amber-300 shadow-md">
                    <div class="absolute top-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded">Saat ini digunakan</div>
                    <button type="button" onclick="deleteProfilePhoto('bg_acara', {{ $w->id }})"
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        🗑️ Hapus
                    </button>
                </div>
                @else
                <div class="w-full h-48 bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl border-2 border-dashed border-amber-300 flex flex-col items-center justify-center gap-2" id="bg_acara-photo-preview">
                    <span class="text-3xl">📅</span>
                    <span class="text-xs text-amber-600">Belum ada foto background acara</span>
                </div>
                @endif
                <input type="file" accept="image/jpeg,image/png,image/jpg" id="bg_acara-photo-input" class="hidden"
                       onchange="previewAndRevealUpload(this, 'bg_acara-photo-preview', 'bg_acara-upload-btn')">
                <button type="button" onclick="document.getElementById('bg_acara-photo-input').click()"
                        class="w-full px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium text-sm transition-colors">
                    📷 Pilih Foto Background Acara
                </button>
                <button type="button" id="bg_acara-upload-btn" onclick="uploadProfilePhoto('bg_acara', {{ $w->id }})"
                        class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                    ✓ Upload
                </button>
                <p class="text-xs text-stone-400">JPG/PNG, maks 5MB</p>
            </div>
            <div class="bg-white/5 border border-stone-200/20 rounded-xl p-4 text-xs text-stone-400 space-y-2">
                <p class="font-semibold text-stone-300">Section: Acara / Hari Istimewa</p>
                <p>Foto ini muncul di belakang kartu Akad Nikah, Resepsi, dan acara tambahan lainnya.</p>
                <p class="text-stone-500">Cocok menggunakan foto venue, dekorasi akad/resepsi, atau bunga.</p>
            </div>
        </div>
    </div>

    {{-- ⑤ Lokasi section bg --}}
    <div class="border-t border-yellow-200 pt-5 mt-5">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-lg">📍</span>
            <h3 class="font-semibold text-stone-700">Background Section Lokasi</h3>
        </div>
        <p class="text-xs text-stone-500 mb-4">Foto yang muncul di belakang bagian peta lokasi acara. Overlay gelap otomatis.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <div class="space-y-3">
                @if($w->bg_lokasi_photo)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $w->bg_lokasi_photo) }}" class="w-full h-48 object-cover rounded-xl border-2 border-teal-300 shadow-md">
                    <div class="absolute top-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded">Saat ini digunakan</div>
                    <button type="button" onclick="deleteProfilePhoto('bg_lokasi', {{ $w->id }})"
                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        🗑️ Hapus
                    </button>
                </div>
                @else
                <div class="w-full h-48 bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl border-2 border-dashed border-teal-300 flex flex-col items-center justify-center gap-2" id="bg_lokasi-photo-preview">
                    <span class="text-3xl">📍</span>
                    <span class="text-xs text-teal-600">Belum ada foto background lokasi</span>
                </div>
                @endif
                <input type="file" accept="image/jpeg,image/png,image/jpg" id="bg_lokasi-photo-input" class="hidden"
                       onchange="previewAndRevealUpload(this, 'bg_lokasi-photo-preview', 'bg_lokasi-upload-btn')">
                <button type="button" onclick="document.getElementById('bg_lokasi-photo-input').click()"
                        class="w-full px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-medium text-sm transition-colors">
                    📷 Pilih Foto Background Lokasi
                </button>
                <button type="button" id="bg_lokasi-upload-btn" onclick="uploadProfilePhoto('bg_lokasi', {{ $w->id }})"
                        class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                    ✓ Upload
                </button>
                <p class="text-xs text-stone-400">JPG/PNG, maks 5MB</p>
            </div>
            <div class="bg-white/5 border border-stone-200/20 rounded-xl p-4 text-xs text-stone-400 space-y-2">
                <p class="font-semibold text-stone-300">Section: Lokasi Acara</p>
                <p>Foto ini muncul di belakang peta/embed lokasi dan nama venue.</p>
                <p class="text-stone-500">Cocok menggunakan foto eksterior gedung, taman, atau jalan menuju venue.</p>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════
     GALERI FOTO (Edit Mode Only, Premium Package)
═══════════════════════════════════════════════════════════════ --}}
@if($w->has_gallery)
<div class="form-section">
    <h2 class="section-title">📸 Galeri Foto</h2>
    <p class="text-sm text-stone-600 mb-4">Upload 6–10 foto untuk ditampilkan di galeri undangan.</p>

    @if($w->gallery->count() > 0)
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
        @foreach($w->gallery as $photo)
        <div class="relative group" id="photo-{{ $photo->id }}">
            <img src="{{ asset('storage/' . $photo->path) }}" class="w-full h-32 object-cover rounded-lg border-2 border-stone-200">
            <button type="button" onclick="deleteGalleryPhoto({{ $photo->id }}, {{ $w->id }})"
                    class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors">
                🗑️
            </button>
        </div>
        @endforeach
    </div>
    @endif

    <div class="border-2 border-dashed border-stone-300 rounded-lg p-6 text-center">
        <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/jpg" id="gallery-input" class="hidden">
        <button type="button" onclick="document.getElementById('gallery-input').click()"
                class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
            + Upload Foto
        </button>
        <button type="button" id="gallery-upload-btn" onclick="uploadGalleryPhotos({{ $w->id }})"
                class="hidden px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium ml-2 transition-colors">
            ✓ Simpan Foto
        </button>
        <p class="text-sm text-stone-500 mt-3">Format: JPG/PNG, maks 2MB per foto. Pilih 6–10 foto.</p>
        <div id="gallery-preview-container" class="mt-4 grid grid-cols-3 gap-2"></div>
    </div>
</div>
@endif
@endisset

{{-- ═══════════════════════════════════════════════════════════════
     JAVASCRIPT
═══════════════════════════════════════════════════════════════ --}}
<script @nonce>
document.getElementById('slug')?.addEventListener('input', function () {
    document.getElementById('slug-preview').textContent = this.value || 'dewanata-anni';
});

function previewAndRevealUpload(input, previewId, btnId) {
    const preview = document.getElementById(previewId);
    const btn = document.getElementById(btnId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            if (preview) preview.innerHTML = `<img src="${e.target.result}" class="w-full h-64 object-cover rounded-lg border-2 shadow-md">`;
            if (btn) btn.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

async function uploadProfilePhoto(type, weddingId) {
    const input = document.getElementById(type + '-photo-input');
    const btn = document.getElementById(type + '-upload-btn');
    if (!input?.files?.[0]) { admToast('Pilih foto terlebih dahulu', 'warning'); return; }

    const formData = new FormData();
    formData.append('photo', input.files[0]);
    formData.append('type', type);
    formData.append('_token', '{{ csrf_token() }}');

    btn.disabled = true; btn.textContent = '⏳ Mengupload...';
    try {
        const res = await fetch(`/admin/weddings/${weddingId}/profile-photo`, { method: 'POST', body: formData });
        const result = await res.json();
        result.success ? location.reload() : admToast(result.message || 'Upload gagal', 'error');
    } catch (e) { admToast('Error: ' + e.message, 'error'); }
    finally { btn.disabled = false; btn.textContent = '✓ Upload Foto'; }
}

async function uploadProfilePhotoFromInput(type, weddingId, inputId) {
    const input = document.getElementById(inputId);
    const btn = document.getElementById('hero-' + type + '-upload-btn');
    if (!input?.files?.[0]) { admToast('Pilih foto terlebih dahulu', 'warning'); return; }

    const formData = new FormData();
    formData.append('photo', input.files[0]);
    formData.append('type', type);
    formData.append('_token', '{{ csrf_token() }}');

    btn.disabled = true; btn.textContent = '⏳ Mengupload...';
    try {
        const res = await fetch(`/admin/weddings/${weddingId}/profile-photo`, { method: 'POST', body: formData });
        const result = await res.json();
        result.success ? location.reload() : admToast(result.message || 'Upload gagal', 'error');
    } catch (e) { admToast('Error: ' + e.message, 'error'); }
    finally { btn.disabled = false; btn.textContent = '✓ Upload Foto'; }
}

function deleteProfilePhoto(type, weddingId) {
    admConfirm('Yakin hapus foto ini?', async () => {
        try {
            const res = await fetch(`/admin/weddings/${weddingId}/profile-photo/${type}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const result = await res.json();
            result.success ? location.reload() : admToast(result.message || 'Hapus gagal', 'error');
        } catch (e) { admToast('Error: ' + e.message, 'error'); }
    }, {danger: true});
}

document.getElementById('gallery-input')?.addEventListener('change', function () {
    const preview = document.getElementById('gallery-preview-container');
    const btn = document.getElementById('gallery-upload-btn');
    preview.innerHTML = '';
    if (this.files.length > 0) {
        btn.classList.remove('hidden');
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-24 object-cover rounded border">`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    } else { btn.classList.add('hidden'); }
});

async function uploadGalleryPhotos(weddingId) {
    const input = document.getElementById('gallery-input');
    const btn = document.getElementById('gallery-upload-btn');
    if (!input?.files?.length) { admToast('Pilih foto terlebih dahulu', 'warning'); return; }

    const formData = new FormData();
    Array.from(input.files).forEach(f => formData.append('photos[]', f));
    formData.append('_token', '{{ csrf_token() }}');

    btn.disabled = true; btn.textContent = '⏳ Mengupload...';
    try {
        const res = await fetch(`/admin/weddings/${weddingId}/gallery`, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });
        const result = await res.json();
        if (result.success) {
            location.reload();
        } else {
            admToast(result.message || (result.errors ? JSON.stringify(result.errors) : 'Upload gagal ('+res.status+')'), 'error');
        }
    } catch (e) { admToast('Error: ' + e.message, 'error'); }
    finally { btn.disabled = false; btn.textContent = '✓ Simpan Foto'; }
}

function deleteGalleryPhoto(photoId, weddingId) {
    admConfirm('Yakin hapus foto ini?', async () => {
        try {
            const res = await fetch(`/admin/weddings/${weddingId}/gallery/${photoId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            const result = await res.json();
            result.success ? document.getElementById('photo-' + photoId)?.remove() : admToast(result.message || 'Hapus gagal', 'error');
        } catch (e) { admToast('Error: ' + e.message, 'error'); }
    }, {danger: true});
}
</script>
