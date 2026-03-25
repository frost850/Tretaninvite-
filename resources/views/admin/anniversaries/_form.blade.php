{{-- Form lengkap untuk Anniversary (Hari Jadi Pernikahan) --}}
{{-- Variables: $w (Wedding|null untuk create), $template (string), $templateInfo (array) --}}

@php $old = fn($k, $def='') => old($k, $w->$k ?? $def); @endphp

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
               placeholder="anniversary-budi-ani-25th" required
               @isset($w) readonly @endisset
               class="field-input @isset($w) bg-stone-50 text-stone-500 cursor-not-allowed @endisset">
        <p class="field-hint">
            Link undangan: <strong>{{ url('/') }}/<span id="slug-preview">{{ old('slug', $w->slug ?? 'anniversary-budi-ani-25th') }}</span></strong>
            @isset($w) · Slug tidak bisa diubah setelah dibuat. @endisset
        </p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     PASANGAN
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">� Pasangan</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="field-label">Nama Pasangan Pertama*</label>
            <input type="text" name="bride_name" value="{{ $old('bride_name') }}" required placeholder="Budi Santoso" class="field-input">
            <p class="field-hint">Nama suami / pasangan pertama</p>
        </div>
        <div>
            <label class="field-label">Nama Pasangan Kedua*</label>
            <input type="text" name="groom_name" value="{{ $old('groom_name') }}" required placeholder="Ani Rahayu" class="field-input">
            <p class="field-hint">Nama istri / pasangan kedua</p>
        </div>
        <div>
            <label class="field-label">Tahun Anniversary</label>
            <input type="number" name="bride_age" value="{{ $old('bride_age') }}" placeholder="25" min="1" max="100" class="field-input">
            <p class="field-hint">Contoh: 25 untuk perayaan perak (opsional)</p>
        </div>
        <div>
            <label class="field-label">Teks Pembuka Undangan (opsional)</label>
            <textarea name="opening_text" rows="3" placeholder="Dengan penuh rasa syukur, kami merayakan 25 tahun bersama…" class="field-input">{{ $old('opening_text') }}</textarea>
            <p class="field-hint">Pesan romantis atau kutipan pembuka di halaman undangan</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     DETAIL PERAYAAN
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">🥂 Detail Perayaan</h2>
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="field-label">Tanggal Perayaan*</label>
                <input type="date" name="event_date" value="{{ old('event_date', isset($w) && $w->event_date ? $w->event_date->format('Y-m-d') : '') }}" required class="field-input">
            </div>
            <div>
                <label class="field-label">Jam Perayaan*</label>
                <input type="text" name="reception_time" value="{{ $old('reception_time') }}" required placeholder="18.00 WIB — selesai" class="field-input">
                <p class="field-hint">Contoh: 18.00 - 22.00 WIB</p>
            </div>
        </div>
        <div>
            <label class="field-label">Nama Venue / Tempat*</label>
            <input type="text" name="location" value="{{ $old('location') }}" required placeholder="Ballroom Hotel Grand Hyatt, Jakarta" class="field-input">
        </div>
        <div>
            <label class="field-label">Alamat Lengkap (opsional)</label>
            <input type="text" name="reception_location" value="{{ $old('reception_location') }}" placeholder="Jl. M.H. Thamrin No.28, Jakarta Pusat" class="field-input">
        </div>
        <div>
            <label class="field-label">Dress Code (opsional)</label>
            <input type="text" name="dresscode" value="{{ $old('dresscode') }}" placeholder="Formal / Black Tie / Batik" class="field-input">
        </div>
        <div>
            <label class="field-label">Link Google Maps</label>
            <input type="url" name="map_link" value="{{ $old('map_link') }}" placeholder="https://maps.google.com/?q=..." class="field-input">
        </div>
        <div>
            <label class="field-label">Embed Google Maps (opsional)</label>
            <textarea name="map_embed" rows="3" placeholder='&lt;iframe src="https://www.google.com/maps/embed?..." ...&gt;&lt;/iframe&gt;' class="field-input font-mono text-xs">{{ $old('map_embed') }}</textarea>
            <p class="field-hint">Dari Google Maps → Share → Embed a map → salin kode iframe</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     TAMBAHAN
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">🎵 Musik & Galeri</h2>
    <div class="space-y-4">
        @if(in_array(($package ?? ($w->package ?? 'basic')), ['premium', 'vip']))
        <div>
            <label class="field-label">URL Musik Latar (opsional)</label>
            <input type="url" name="music_url" value="{{ $old('music_url') }}" placeholder="https://.../lagu-anniversary.mp3" class="field-input">
            <p class="field-hint">Link file MP3 yang diputar otomatis saat halaman dibuka. Khusus paket Premium.</p>
        </div>
        <div>
            <label class="field-label flex items-center gap-2">
                <input type="checkbox" name="has_gallery" value="1" {{ ($w->has_gallery ?? false) ? 'checked' : '' }} class="w-4 h-4 text-pink-600">
                Aktifkan Galeri Foto
            </label>
            <p class="field-hint">Upload foto-foto kenangan untuk galeri slideshow. Khusus paket Premium/VIP.</p>
        </div>
        @else
        <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-300">
            🎵 <strong>Musik latar</strong> dan 📸 <strong>Galeri foto</strong> hanya tersedia di paket <strong>Premium</strong>.
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     GALERI FOTO (Edit Mode Only, Premium Package)
═══════════════════════════════════════════════════════════════ --}}
@isset($w)
@if($w->has_gallery)
<div class="form-section">
    <h2 class="section-title">📸 Galeri Foto</h2>
    <p class="text-sm text-stone-600 mb-2">Upload foto-foto kenangan untuk ditampilkan di galeri undangan</p>

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
                class="px-6 py-3 bg-pink-600 hover:bg-pink-700 text-white rounded-lg font-medium transition-colors">
            + Upload Foto
        </button>
        <button type="button" id="gallery-upload-btn" onclick="uploadGalleryPhotos({{ $w->id }})"
                class="hidden px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium ml-2 transition-colors">
            ✓ Simpan Foto
        </button>
        <p class="text-sm text-stone-500 mt-3">Format: JPG/PNG, maks 5MB per foto</p>
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
    document.getElementById('slug-preview').textContent = this.value || 'anniversary-budi-ani-25th';
});
</script>
