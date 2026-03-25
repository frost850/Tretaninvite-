{{-- Form lengkap untuk Birthday/Ulang Tahun --}}
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
               placeholder="birthday-aisyah" required
               @isset($w) readonly @endisset
               class="field-input @isset($w) bg-stone-50 text-stone-500 cursor-not-allowed @endisset">
        <p class="field-hint">
            Link undangan: <strong>{{ url('/') }}/<span id="slug-preview">{{ old('slug', $w->slug ?? 'birthday-aisyah') }}</span></strong>
            @isset($w) · Slug tidak bisa diubah setelah dibuat. @endisset
        </p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     YANG BERULANG TAHUN
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">🎂 Yang Berulang Tahun</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="field-label">Nama Lengkap*</label>
            <input type="text" name="bride_name" value="{{ $old('bride_name') }}" required placeholder="Aisyah Azzahra" class="field-input">
            <p class="field-hint">Nama anak yang berulang tahun</p>
        </div>
        <div>
            <label class="field-label">Umur yang Dirayakan*</label>
            <input type="number" name="bride_age" value="{{ $old('bride_age') }}" required placeholder="7" min="1" max="150" class="field-input">
            <p class="field-hint">Contoh: 7 (tujuh tahun)</p>
        </div>
        <div>
            <label class="field-label">Jenis Kelamin*</label>
            <div class="flex gap-4 mt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="bride_gender" value="female" {{ old('bride_gender', $w->bride_gender ?? 'female') === 'female' ? 'checked' : '' }}>
                    <span class="text-slate-200 text-sm">&#9792; Perempuan</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="bride_gender" value="male" {{ old('bride_gender', $w->bride_gender ?? '') === 'male' ? 'checked' : '' }}>
                    <span class="text-slate-200 text-sm">&#9794; Laki-laki</span>
                </label>
            </div>
            <p class="field-hint">Mempengaruhi teks di undangan (Sang Ratu / Sang Raja)</p>
        </div>
        <div>
            <label class="field-label">Nama Orang Tua</label>
            <input type="text" name="bride_parent" value="{{ $old('bride_parent') }}" placeholder="Bapak Ahmad & Ibu Siti" class="field-input">
            <p class="field-hint">Nama kedua orang tua</p>
        </div>
        <div>
            <label class="field-label">No. WhatsApp</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">+62</span>
                <input type="text" name="bride_wa" value="{{ $old('bride_wa') }}" placeholder="81234567890" class="field-input pl-10">
            </div>
            <p class="field-hint">Untuk tombol kirim ucapan di template Gatsby</p>
        </div>
        <div class="sm:col-span-2">
            <label class="field-label">Info Kado / Amplop (opsional)</label>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-stone-500 mb-1 block">Bank</label>
                    <input type="text" name="bride_bank" value="{{ $old('bride_bank') }}" placeholder="BCA / BRI / Mandiri" class="field-input">
                </div>
                <div>
                    <label class="text-xs text-stone-500 mb-1 block">No. Rekening</label>
                    <input type="text" name="bride_norek" value="{{ $old('bride_norek') }}" placeholder="1234567890" class="field-input">
                </div>
            </div>
            <p class="field-hint">Jika tamu ingin kirim kado berupa uang</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     DETAIL PESTA
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">🎉 Detail Pesta Ulang Tahun</h2>
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="field-label">Tanggal Pesta*</label>
                <input type="date" name="event_date" value="{{ old('event_date', isset($w) && $w->event_date ? $w->event_date->format('Y-m-d') : '') }}" required class="field-input">
            </div>
            <div>
                <label class="field-label">Jam Pesta*</label>
                <input type="text" name="reception_time" value="{{ $old('reception_time') }}" required placeholder="14.00 - 17.00 WIB" class="field-input">
                <p class="field-hint">Contoh: 14.00 - 17.00 WIB</p>
            </div>
        </div>
        <div>
            <label class="field-label">Nama Venue / Tempat*</label>
            <input type="text" name="location" value="{{ $old('location') }}" required placeholder="Restoran Bunga, Jakarta Selatan" class="field-input">
            <p class="field-hint">Nama lengkap venue atau rumah</p>
        </div>
        <div>
            <label class="field-label">Alamat Lengkap (opsional)</label>
            <input type="text" name="reception_location" value="{{ $old('reception_location') }}" placeholder="Jl. Sudirman No.1, Kebayoran Baru, Jakarta Selatan" class="field-input">
            <p class="field-hint">Alamat detail untuk memudahkan tamu</p>
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
     TEMA & DRESS CODE
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">🎨 Tema & Dress Code</h2>
    <div class="space-y-4">
        <div>
            <label class="field-label">Tema Pesta (opsional)</label>
            <input type="text" name="dresscode" value="{{ $old('dresscode') }}" placeholder="Princess & Unicorn / Superhero / Rainbow Party" class="field-input">
            <p class="field-hint">Tema dekorasi atau dress code untuk tamu</p>
        </div>
        <div>
            <label class="field-label">Pesan / Catatan Khusus (opsional)</label>
            <textarea name="opening_text" rows="4" placeholder="Mohon konfirmasi kehadiran paling lambat 3 hari sebelum acara. Terima kasih! 💕" class="field-input">{{ $old('opening_text') }}</textarea>
            <p class="field-hint">Pesan untuk tamu (RSVP deadline, aturan gift, dll)</p>
        </div>
        @if(in_array(($package ?? ($w->package ?? 'basic')), ['premium', 'vip']))
        <div>
            <label class="field-label">URL Musik Latar (opsional)</label>
            <input type="url" name="music_url" value="{{ $old('music_url') }}" placeholder="https://.../lagu-birthday.mp3" class="field-input">
            <p class="field-hint">Link file MP3 yang akan diputar otomatis. Khusus paket Premium.</p>
        </div>
        <div>
            <label class="field-label flex items-center gap-2">
                <input type="checkbox" name="has_gallery" value="1" {{ ($w->has_gallery ?? false) ? 'checked' : '' }} class="w-4 h-4 text-pink-600">
                Aktifkan Galeri Foto
            </label>
            <p class="field-hint">Upload 6–10 foto untuk tampil di galeri <strong>dan</strong> sebagai background animasi 3D (slideshow Ken Burns) di setiap section undangan. Khusus paket Premium/VIP.</p>
        </div>
        @else
        <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-300">
            🎵 <strong>Musik latar</strong> dan 📸 <strong>Galeri foto</strong> hanya tersedia di paket <strong>Premium</strong>.
        </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     FOTO ANAK (Edit Mode Only)
═══════════════════════════════════════════════════════════════ --}}
@isset($w)
<div class="form-section">
    <h2 class="section-title">🖼️ Foto Anak</h2>
    <p class="text-sm text-stone-600 mb-4">Upload foto anak yang berulang tahun</p>

    <div class="max-w-xs">
        <div class="space-y-3">
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
                <span class="text-6xl">🎂</span>
            </div>
            @endif
            <input type="file" name="photo" accept="image/jpeg,image/png,image/jpg" id="bride-photo-input" class="hidden"
                   onchange="previewAndRevealUpload(this, 'bride-photo-preview', 'bride-upload-btn')">
            <button type="button" onclick="document.getElementById('bride-photo-input').click()"
                    class="w-full px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg font-medium text-sm transition-colors">
                📷 Pilih Foto
            </button>
            <button type="button" id="bride-upload-btn" onclick="uploadProfilePhoto('bride', {{ $w->id }})"
                    class="hidden w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors">
                ✓ Upload Foto
            </button>
            <p class="text-xs text-stone-500">JPG/PNG, maks 2MB</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     GALERI FOTO (Edit Mode Only, Premium Package)
═══════════════════════════════════════════════════════════════ --}}
@if($w->has_gallery)
<div class="form-section">
    <h2 class="section-title">📸 Galeri Foto</h2>
    <p class="text-sm text-stone-600 mb-2">Upload foto-foto pesta untuk ditampilkan di galeri undangan</p>
    <div class="rounded-lg border border-pink-400/30 bg-pink-500/10 px-4 py-3 text-sm text-pink-300 mb-4">
        ✨ <strong>Background Slideshow Premium:</strong> Foto galeri ini juga tampil sebagai <strong>background animasi 3D (Ken Burns)</strong> di setiap section undangan — Hero, About, Event, RSVP, dan Galeri. Foto berganti otomatis saat tamu scroll. Upload minimal 4–5 foto untuk efek terbaik.
    </div>

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
    document.getElementById('slug-preview').textContent = this.value || 'birthday-aisyah';
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
            if (result.success) {
                document.getElementById('photo-' + photoId)?.remove();
            } else {
                admToast(result.message || 'Hapus gagal', 'error');
            }
        } catch (e) { admToast('Error hapus: ' + e.message, 'error'); }
    }, {danger: true});
}
</script>
