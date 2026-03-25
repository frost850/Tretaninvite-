{{-- Form untuk Kartu Ucapan (Birthday) --}}
{{-- Variables: $w (Wedding|null untuk create), $template (string), $templateInfo (array) --}}

@php
$old = fn($k, $def='') => old($k, $w->$k ?? $def);
@endphp

<input type="hidden" name="template" value="{{ $template }}">

{{-- ═══════════════════════════════════════════════════════════════
     ALAMAT KARTU
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">🔗 Alamat Kartu</h2>
    <div>
        <label class="field-label" for="slug">Slug (URL)*</label>
        <input type="text" name="slug" id="slug"
               value="{{ old('slug', $w->slug ?? '') }}"
               placeholder="ucapan-aisyah" required
               @isset($w) readonly @endisset
               class="field-input @isset($w) bg-stone-50 text-stone-500 cursor-not-allowed @endisset">
        <p class="field-hint">
            Link kartu: <strong>{{ url('/') }}/<span id="slug-preview">{{ old('slug', $w->slug ?? 'ucapan-aisyah') }}</span></strong>
            @isset($w) · Slug tidak bisa diubah setelah dibuat. @endisset
        </p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     PENERIMA KARTU
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">🎂 Penerima Ucapan</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="field-label">Nama Penerima*</label>
            <input type="text" name="bride_name" value="{{ $old('bride_name') }}" required
                   placeholder="Aisyah Azzahra" class="field-input">
            <p class="field-hint">Nama orang yang berulang tahun</p>
        </div>
        <div>
            <label class="field-label">Usia yang Dirayakan</label>
            <input type="number" name="bride_age" value="{{ $old('bride_age') }}" placeholder="25" min="1" max="150" class="field-input">
            <p class="field-hint">Kosongkan jika tidak ingin ditampilkan</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     PENGIRIM KARTU
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">💌 Pengirim Ucapan</h2>
    <div>
        <label class="field-label">Nama Pengirim</label>
        <input type="text" name="groom_name" value="{{ $old('groom_name') }}"
               placeholder="Keluarga Besar Ahmad" class="field-input">
        <p class="field-hint">Nama pengirim kartu ucapan (opsional)</p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     PESAN UCAPAN
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">✍️ Pesan Ucapan</h2>
    <div class="space-y-4">
        <div>
            <label class="field-label">Pesan Ucapan</label>
            <textarea name="opening_text" rows="5"
                placeholder="Selamat ulang tahun! Semoga selalu diberikan kesehatan, kebahagiaan, dan keberkahan. 🎂"
                class="field-input">{{ $old('opening_text') }}</textarea>
            <p class="field-hint">Tulis pesan spesial yang akan ditampilkan di kartu ucapan</p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     MUSIK LATAR
═══════════════════════════════════════════════════════════════ --}}
<div class="form-section">
    <h2 class="section-title">🎵 Musik Latar (Opsional)</h2>
    <div>
        <label class="field-label">Link Musik</label>
        <input type="url" name="music_url" value="{{ $old('music_url') }}"
               placeholder="https://www.youtube.com/watch?v=..." class="field-input">
        <p class="field-hint">Link YouTube atau file audio MP3 langsung</p>
    </div>
</div>

@if(!isset($w))
<script @nonce>
    const slugInput = document.getElementById('slug');
    const slugPreview = document.getElementById('slug-preview');
    if (slugInput && slugPreview) {
        slugInput.addEventListener('input', () => {
            slugPreview.textContent = slugInput.value || 'ucapan-aisyah';
        });
    }
</script>
@endif
