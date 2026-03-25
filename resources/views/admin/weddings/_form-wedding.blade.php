{{-- Form fields khusus untuk Wedding/Pernikahan --}}
{{-- Variables: $w (Wedding|null for create), $template (string), $templateInfo (array), $old (closure) --}}

{{-- ─── MEMPELAI WANITA ─────────────────────────────────────────── --}}
<div class="form-section">
    <h2 class="section-title">👰 Mempelai Wanita</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="field-label">Nama Panggilan*</label>
            <input type="text" name="bride_name" value="{{ $old('bride_name') }}" required placeholder="Anni" class="field-input">
        </div>
        <div>
            <label class="field-label">Umur (opsional)</label>
            <input type="number" name="bride_age" value="{{ $old('bride_age') }}" placeholder="" min="1" max="150" class="field-input">
        </div>
        <div class="sm:col-span-2">
            <label class="field-label">Nama Lengkap (formal)</label>
            <input type="text" name="bride_fullname" value="{{ $old('bride_fullname') }}" placeholder="Annisa Rahmawati binti Abdul Rahman" class="field-input">
            <p class="field-hint">Nama formal yang tampil di undangan. Kosongkan jika tidak ingin ditampilkan.</p>
        </div>
        <div>
            <label class="field-label">Nama Ayah</label>
            <input type="text" name="bride_father" value="{{ $old('bride_father') }}" placeholder="Bapak Abdul Rahman" class="field-input">
        </div>
        <div>
            <label class="field-label">Nama Ibu</label>
            <input type="text" name="bride_mother" value="{{ $old('bride_mother') }}" placeholder="Ibu Siti Fatimah" class="field-input">
        </div>
        <div>
            <label class="field-label">Orang Tua (kombinasi, opsional)</label>
            <input type="text" name="bride_parent" value="{{ $old('bride_parent') }}" placeholder="Putri dari Bapak … &amp; Ibu …" class="field-input">
            <p class="field-hint">Isi jika ingin menulis kalimat sendiri. Jika Nama Ayah &amp; Ibu sudah diisi di atas, kolom ini diabaikan.</p>
        </div>
        <div>
            <label class="field-label">No. WhatsApp</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">+62</span>
                <input type="text" name="bride_wa" value="{{ $old('bride_wa') }}" placeholder="81234567890" class="field-input pl-10">
            </div>
            <p class="field-hint">Nomor WA untuk tombol kontak di undangan. Tanpa 0 di depan.</p>
        </div>
        <div></div>
        <div>
            <label class="field-label">Bank</label>
            <input type="text" name="bride_bank" value="{{ $old('bride_bank') }}" placeholder="BCA / BRI / Mandiri" class="field-input">
        </div>
        <div>
            <label class="field-label">No. Rekening</label>
            <input type="text" name="bride_norek" value="{{ $old('bride_norek') }}" placeholder="1234567890" class="field-input">
        </div>
    </div>
</div>

{{-- ─── MEMPELAI PRIA ───────────────────────────────────────────── --}}
<div class="form-section">
    <h2 class="section-title">🤵 Mempelai Pria</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="field-label">Nama Panggilan*</label>
            <input type="text" name="groom_name" value="{{ $old('groom_name') }}" required placeholder="Ahmad" class="field-input">
        </div>
        <div></div>
        <div class="sm:col-span-2">
            <label class="field-label">Nama Lengkap (formal)</label>
            <input type="text" name="groom_fullname" value="{{ $old('groom_fullname') }}" placeholder="Ahmad Fauzan bin Hendra Wijaya" class="field-input">
            <p class="field-hint">Nama formal yang tampil di undangan. Kosongkan jika tidak ingin ditampilkan.</p>
        </div>
        <div>
            <label class="field-label">Nama Ayah</label>
            <input type="text" name="groom_father" value="{{ $old('groom_father') }}" placeholder="Bapak Hendra Wijaya" class="field-input">
        </div>
        <div>
            <label class="field-label">Nama Ibu</label>
            <input type="text" name="groom_mother" value="{{ $old('groom_mother') }}" placeholder="Ibu Sri Wulandari" class="field-input">
        </div>
        <div>
            <label class="field-label">Orang Tua (kombinasi, opsional)</label>
            <input type="text" name="groom_parent" value="{{ $old('groom_parent') }}" placeholder="Putra dari Bapak … &amp; Ibu …" class="field-input">
            <p class="field-hint">Isi jika ingin menulis kalimat sendiri. Jika Nama Ayah &amp; Ibu sudah diisi di atas, kolom ini diabaikan.</p>
        </div>
        <div>
            <label class="field-label">No. WhatsApp</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">+62</span>
                <input type="text" name="groom_wa" value="{{ $old('groom_wa') }}" placeholder="81234567890" class="field-input pl-10">
            </div>
            <p class="field-hint">Nomor WA untuk tombol kontak di undangan. Tanpa 0 di depan.</p>
        </div>
        <div></div>
        <div>
            <label class="field-label">Bank</label>
            <input type="text" name="groom_bank" value="{{ $old('groom_bank') }}" placeholder="BCA / BRI / Mandiri" class="field-input">
        </div>
        <div>
            <label class="field-label">No. Rekening</label>
            <input type="text" name="groom_norek" value="{{ $old('groom_norek') }}" placeholder="1234567890" class="field-input">
        </div>
    </div>
</div>

{{-- ─── AKAD NIKAH ──────────────────────────────────────────────── --}}
<div class="form-section">
    <h2 class="section-title">🕌 Akad Nikah</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="field-label">Tanggal Akad</label>
            <input type="date" name="akad_date" value="{{ old('akad_date', isset($w) && $w->akad_date ? $w->akad_date->format('Y-m-d') : '') }}" class="field-input">
        </div>
        <div>
            <label class="field-label">Jam (contoh: 08.00 – 10.00)</label>
            <input type="text" name="akad_time" value="{{ $old('akad_time') }}" placeholder="08.00 – 10.00 WIB" class="field-input">
        </div>
        <div class="sm:col-span-2">
            <label class="field-label">Lokasi Akad</label>
            <input type="text" name="akad_location" value="{{ $old('akad_location') }}" placeholder="Masjid / Kediaman mempelai wanita" class="field-input">
        </div>
    </div>
</div>

{{-- ─── RESEPSI ─────────────────────────────────────────────────── --}}
<div class="form-section">
    <h2 class="section-title">🎊 Resepsi Pernikahan</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="field-label">Tanggal Resepsi</label>
            <input type="date" name="reception_date" value="{{ old('reception_date', isset($w) && $w->reception_date ? $w->reception_date->format('Y-m-d') : '') }}" class="field-input">
        </div>
        <div>
            <label class="field-label">Jam (contoh: 11.00 – 15.00)</label>
            <input type="text" name="reception_time" value="{{ $old('reception_time') }}" placeholder="11.00 – 15.00 WIB" class="field-input">
        </div>
        <div class="sm:col-span-2">
            <label class="field-label">Lokasi Resepsi</label>
            <input type="text" name="reception_location" value="{{ $old('reception_location') }}" placeholder="Nama gedung / ballroom, alamat" class="field-input">
        </div>
    </div>
    <p class="field-hint mt-2">💡 Jika akad & resepsi di tanggal/lokasi yang sama, isi salah satu saja. Field <em>Tanggal Acara</em> di bawah dipakai sebagai fallback countdown.</p>
</div>

{{-- ─── LOKASI & MAPS ───────────────────────────────────────────── --}}
<div class="form-section">
    <h2 class="section-title">📍 Lokasi & Maps</h2>
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="field-label">Nama Venue (umum)</label>
                <input type="text" name="location" value="{{ $old('location') }}" placeholder="Grand Ballroom, Kota" class="field-input">
            </div>
            <div>
                <label class="field-label">Tanggal Acara (untuk countdown)</label>
                <input type="date" name="event_date" value="{{ old('event_date', isset($w) && $w->event_date ? $w->event_date->format('Y-m-d') : '') }}" class="field-input">
            </div>
        </div>
        <div>
            <label class="field-label">Link Google Maps</label>
            <input type="url" name="map_link" value="{{ $old('map_link') }}" placeholder="https://maps.google.com/?q=..." class="field-input">
        </div>
        <div>
            <label class="field-label">Embed Google Maps (iframe code, opsional)</label>
            <textarea name="map_embed" rows="3" placeholder='&lt;iframe src="https://www.google.com/maps/embed?..." ...&gt;&lt;/iframe&gt;' class="field-input font-mono text-xs">{{ $old('map_embed') }}</textarea>
            <p class="field-hint">Dari Google Maps → Share → Embed a map → salin kode HTML-nya.</p>
        </div>
    </div>
</div>

{{-- ─── DETAIL LAIN ─────────────────────────────────────────────── --}}
<div class="form-section">
    <h2 class="section-title">✨ Detail Lainnya</h2>
    <div class="space-y-4">
        <div>
            <label class="field-label">Dresscode</label>
            <input type="text" name="dresscode" value="{{ $old('dresscode') }}" placeholder="Formal / Pastel / Batik" class="field-input">
        </div>
        <div>
            <label class="field-label">Teks Pembuka / Opening</label>
            <textarea name="opening_text" rows="3" placeholder="Bismillahirrahmanirrahim. Dengan memohon rahmat dan ridho Allah SWT…" class="field-input">{{ $old('opening_text') }}</textarea>
        </div>
        @if($package === 'vip')
        {{-- ♛ VIP ROYAL FIELDS ─────────────────────────────────── --}}
        <div class="rounded-xl border border-yellow-500/40 bg-yellow-500/5 p-4 space-y-4">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-lg">♛</span>
                <h3 class="font-bold text-yellow-400 tracking-wide">Fitur VIP Royal</h3>
                <span class="ml-auto text-xs text-yellow-600 bg-yellow-500/10 px-2 py-0.5 rounded-full border border-yellow-500/30">EKSKLUSIF</span>
            </div>

            {{-- Musik Latar (termasuk di VIP) --}}
            <div>
                <label class="field-label">URL Musik Latar <span class="text-yellow-500">♛</span></label>
                <input type="url" name="music_url" value="{{ $old('music_url') }}" placeholder="https://.../lagu.mp3" class="field-input">
                <p class="field-hint">Link file MP3 yang akan diputar otomatis saat undangan dibuka.</p>
            </div>

            {{-- Galeri Foto (termasuk di VIP) --}}
            <div>
                <label class="field-label flex items-center gap-2">
                    <input type="checkbox" name="has_gallery" value="1" {{ ($w->has_gallery ?? false) ? 'checked' : '' }} class="w-4 h-4 text-purple-600">
                    Aktifkan Galeri Foto
                </label>
                <p class="field-hint">Upload 6–10 foto prewedding untuk ditampilkan di galeri undangan.</p>
            </div>

            {{-- Video Prewedding --}}
            <div>
                <label class="field-label">URL Video Prewedding <span class="text-yellow-500">♛</span></label>
                <input type="url" name="video_url" value="{{ $old('video_url', $w->video_url ?? '') }}" placeholder="https://www.youtube.com/watch?v=..." class="field-input">
                <p class="field-hint">Link YouTube atau Vimeo untuk ditampilkan di bagian video undangan.</p>
            </div>

            {{-- Email Notifikasi RSVP --}}
            <div>
                <label class="field-label">Email Notifikasi RSVP <span class="text-yellow-500">♛</span></label>
                <input type="email" name="notify_email" value="{{ $old('notify_email', $w->notify_email ?? '') }}" placeholder="tamu@email.com" class="field-input">
                <p class="field-hint">Anda akan mendapat email setiap ada tamu yang konfirmasi kehadiran.</p>
            </div>

            {{-- Password Undangan --}}
            <div>
                <label class="field-label">Password Undangan (opsional) <span class="text-yellow-500">♛</span></label>
                <input type="text" name="vip_password" value="{{ $old('vip_password', '') }}" placeholder="Kosongkan jika tidak dikunci" class="field-input font-mono">
                <p class="field-hint">Jika diisi, tamu harus memasukkan password sebelum membuka undangan. Kosongkan untuk membiarkan publik.</p>
            </div>

            {{-- Guestbook --}}
            <div>
                <label class="field-label flex items-center gap-2">
                    <input type="checkbox" name="guestbook_enabled" value="1" {{ ($w->guestbook_enabled ?? true) ? 'checked' : '' }} class="w-4 h-4 text-yellow-500">
                    Aktifkan Buku Tamu Digital <span class="text-yellow-500">♛</span>
                </label>
                <p class="field-hint">Tamu dapat menulis ucapan selamat langsung di undangan.</p>
            </div>

            {{-- Extra Events (up to 5) --}}
            <div>
                <label class="field-label">Acara Tambahan <span class="text-yellow-500">♛</span> <span class="text-xs text-slate-400">(maks. 5 acara)</span></label>
                <p class="field-hint mb-3">Selain akad & resepsi, tambahkan acara lain: pengajian, sungkeman, dll.</p>
                @php
                    $extraEvents = old('extra_events', $w->extra_events ?? []);
                    if (!is_array($extraEvents)) { $extraEvents = []; }
                @endphp
                <div class="space-y-3">
                @for($ei = 0; $ei < 5; $ei++)
                    @php $ev = $extraEvents[$ei] ?? []; @endphp
                    <div class="rounded-lg border border-slate-700 bg-slate-800/50 p-3 space-y-2">
                        <p class="text-xs text-slate-400 font-medium">Acara ke-{{ $ei + 1 }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <input type="text" name="extra_events[{{ $ei }}][label]" value="{{ $ev['label'] ?? '' }}" placeholder="Nama acara (cth: Pengajian)" class="field-input text-sm">
                            <input type="date" name="extra_events[{{ $ei }}][date]" value="{{ $ev['date'] ?? '' }}" class="field-input text-sm">
                            <input type="text" name="extra_events[{{ $ei }}][time]" value="{{ $ev['time'] ?? '' }}" placeholder="Jam (cth: 09.00 WIB)" class="field-input text-sm">
                            <input type="text" name="extra_events[{{ $ei }}][location]" value="{{ $ev['location'] ?? '' }}" placeholder="Lokasi acara" class="field-input text-sm">
                        </div>
                    </div>
                @endfor
                </div>
            </div>
        </div>

        @elseif($package === 'premium')
        <div>
            <label class="field-label">URL Musik Latar (opsional)</label>
            <input type="url" name="music_url" value="{{ $old('music_url') }}" placeholder="https://.../lagu.mp3" class="field-input">
            <p class="field-hint">Link file MP3 yang akan diputar otomatis. Khusus paket Premium.</p>
        </div>
        <div>
            <label class="field-label flex items-center gap-2">
                <input type="checkbox" name="has_gallery" value="1" {{ ($w->has_gallery ?? false) ? 'checked' : '' }} class="w-4 h-4 text-purple-600">
                Aktifkan Galeri Foto
            </label>
            <p class="field-hint">Upload 6–10 foto untuk ditampilkan di galeri undangan. Khusus paket Premium.</p>
        </div>
        @else
        <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-300">
            🎵 <strong>Musik latar</strong> dan 📸 <strong>Galeri foto</strong> hanya tersedia di paket <strong>Premium</strong>.
        </div>
        @endif
    </div>
</div>

{{-- ─── KUSTOMISASI TEKS TEMPLATE ───────────────────────────────── --}}
@php
    $ct    = $w->custom_texts ?? [];
    $oldCt = fn(string $k) => old("custom_texts.$k", $ct[$k] ?? '');
@endphp
<div class="form-section">
    <h2 class="section-title">✏️ Kustomisasi Teks Template <span class="text-xs font-normal text-slate-400 ml-1">(opsional)</span></h2>
    <p class="text-sm text-slate-400 mb-4">Jika tidak diisi, undangan akan menggunakan teks bawaan template. Berguna untuk pelanggan non-Muslim atau yang ingin kalimat berbeda.</p>
    <div class="space-y-4">
        <div>
            <label class="field-label">Teks Kutipan / Ayat</label>
            <textarea name="custom_texts[quote_text]" rows="3" placeholder="Kosongkan = gunakan teks bawaan template" class="field-input">{{ $oldCt('quote_text') }}</textarea>
            <p class="field-hint">Default: <em>"Dan di antara tanda-tanda kekuasaan-Nya ialah Dia menciptakan untukmu pasangan dari jenismu sendiri…" (QS. Ar-Rum: 21)</em></p>
        </div>
        <div>
            <label class="field-label">Sumber Kutipan</label>
            <input type="text" name="custom_texts[quote_source]" value="{{ $oldCt('quote_source') }}" placeholder="QS. Ar-Rum: 21" class="field-input">
            <p class="field-hint">Contoh: QS. Ar-Rum: 21 · Rumi · Kahlil Gibran, dll</p>
        </div>
        <div>
            <label class="field-label">Nama Acara Resepsi</label>
            <input type="text" name="custom_texts[event_name]" value="{{ $oldCt('event_name') }}" placeholder="Walimatul Ursy" class="field-input">
            <p class="field-hint">Default: "Walimatul Ursy". Contoh lain: Resepsi Pernikahan, Wedding Reception, Syukuran Pernikahan.</p>
        </div>
        <div>
            <label class="field-label">Judul Acara Wisuda <span class="text-xs text-slate-500">(khusus template Patisserie)</span></label>
            <input type="text" name="custom_texts[ceremony_title]" value="{{ $oldCt('ceremony_title') }}" placeholder="Upacara Wisuda" class="field-input">
            <p class="field-hint">Konteks: "Prosesi Wisuda" → judul nama acaranya, default "Upacara Wisuda".</p>
        </div>
        <div>
            <label class="field-label">Judul Acara Syukuran <span class="text-xs text-slate-500">(khusus template Patisserie)</span></label>
            <input type="text" name="custom_texts[reception_title]" value="{{ $oldCt('reception_title') }}" placeholder="Tasyakuran Wisuda" class="field-input">
            <p class="field-hint">Default: "Tasyakuran Wisuda". Contoh lain: Resepsi Keluarga, Syukuran Kelulusan.</p>
        </div>
    </div>
</div>
