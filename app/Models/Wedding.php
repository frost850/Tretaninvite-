<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Wedding extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'slug',
        'bride_name',
        'bride_fullname',
        'bride_father',
        'bride_mother',
        'bride_age',
        'bride_gender',
        'bride_photo',
        'bride_parent',
        'bride_wa',
        'bride_bank',
        'bride_norek',
        'groom_name',
        'groom_fullname',
        'groom_father',
        'groom_mother',
        'groom_photo',
        'couple_photo',
        'groom_parent',
        'groom_wa',
        'groom_bank',
        'groom_norek',
        'event_date',
        'akad_date',
        'akad_time',
        'akad_location',
        'reception_date',
        'reception_time',
        'reception_location',
        'location',
        'map_link',
        'map_embed',
        'dresscode',
        'opening_text',
        'music_url',
        'is_active',
        'template',
        'tracking_token',
        'has_gallery',
        'package',
        'trial_expires_at',
        'creator_ip',
        // VIP fields
        'video_url',
        'cover_photo',
        'bg_mempelai_photo',
        'bg_acara_photo',
        'bg_lokasi_photo',
        'vip_password',
        'guestbook_enabled',
        'notify_email',
        'extra_events',
        'custom_texts',
        'expiry_notified_2d_at',
    ];

    /** --- Package Helpers --- */
    public function isTrial(): bool   { return $this->package === 'trial'; }
    public function isBasic(): bool   { return $this->package === 'basic'; }
    public function isPremium(): bool { return $this->package === 'premium'; }
    public function isVip(): bool     { return $this->package === 'vip'; }

    /** Jumlah hari aktif per paket */
    public static function expiryDays(string $package): int
    {
        return match ($package) {
            'trial'   => 1,
            'basic'   => 14,
            'premium' => 30,
            'vip'     => 90,
            default   => 14,
        };
    }

    /** Apakah undangan sudah lewat masa aktifnya (berlaku semua paket) */
    public function isExpired(): bool
    {
        return $this->trial_expires_at !== null
            && now()->isAfter($this->trial_expires_at);
    }

    /** Alias lama — tetap kompatibel */
    public function isTrialExpired(): bool
    {
        return $this->isTrial() && $this->isExpired();
    }

    /**
     * Mode arsip: undangan non-trial yang masa aktifnya sudah habis.
     * Link tetap bisa dibuka, RSVP & guestbook dinonaktifkan.
     */
    public function isArchived(): bool
    {
        return $this->isExpired() && !$this->isTrial();
    }

    public function hasGalleryAccess(): bool      { return $this->isPremium() || $this->isVip(); }
    public function hasMusicAccess(): bool        { return $this->isPremium() || $this->isVip(); }
    public function hasTrackingAccess(): bool     { return !$this->isTrial(); }
    public function hasVideoAccess(): bool        { return $this->isVip(); }
    public function hasGuestbookAccess(): bool    { return $this->isVip(); }
    public function hasPasswordAccess(): bool     { return $this->isVip(); }
    public function hasQrCodeAccess(): bool       { return $this->isVip(); }
    public function hasEmailNotifyAccess(): bool  { return $this->isVip(); }
    public function hasMultipleEventsAccess(): bool { return $this->isVip(); }
    public function hasLiveRsvpAccess(): bool     { return $this->isVip(); }
    public function guestLimit(): ?int            { return $this->isTrial() ? 3 : ($this->isBasic() ? 100 : null); }

    /** Paket yang dapat akses portal pelanggan (premium lite atau VIP penuh) */
    public function hasPremiumDashboard(): bool   { return $this->isPremium() || $this->isVip(); }
    /** Paket yang boleh kelola daftar tamu secara mandiri (tanpa admin) */
    public function hasGuestManageAccess(): bool  { return $this->isPremium() || $this->isVip(); }

    /**
     * Kembalikan custom text untuk key tertentu, atau $default jika belum diisi admin.
     */
    public function customText(string $key, string $default = ''): string
    {
        $value = ($this->custom_texts ?? [])[$key] ?? null;
        return (is_string($value) && $value !== '') ? $value : $default;
    }

    protected static function booted(): void
    {
        static::creating(function (Wedding $wedding) {
            if (empty($wedding->tracking_token)) {
                $wedding->tracking_token = Str::random(32);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'event_date'             => 'date',
            'akad_date'              => 'date',
            'reception_date'         => 'date',
            'is_active'              => 'boolean',
            'has_gallery'            => 'boolean',
            'guestbook_enabled'      => 'boolean',
            'extra_events'           => 'array',
            'custom_texts'           => 'array',
            'trial_expires_at'       => 'datetime',
            'expiry_notified_2d_at'  => 'datetime',
        ];
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(WeddingGallery::class)->orderBy('order');
    }

    /** Alias for gallery() — used by invitation templates */
    public function galleries(): HasMany
    {
        return $this->hasMany(WeddingGallery::class)->orderBy('order');
    }

    public function guestbook(): HasMany
    {
        return $this->hasMany(\App\Models\Guestbook::class)->orderByDesc('created_at');
    }
}
