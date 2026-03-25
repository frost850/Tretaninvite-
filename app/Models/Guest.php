<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Guest extends Model
{
    protected $fillable = [
        'wedding_id',
        'guest_name',
        'group_name',
        'slug_name',
        'phone',
        'email',
        'notes',
        'is_attending',
        'replied_at',
        'pax',
        'first_opened_at',
        'open_count',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'is_attending' => 'boolean',
            'replied_at' => 'datetime',
            'first_opened_at' => 'datetime',
            'open_count' => 'integer',
            'checked_in_at' => 'datetime',
        ];
    }

    /** Apakah tamu sudah check-in di venue */
    public function isCheckedIn(): bool
    {
        return $this->checked_in_at !== null;
    }

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }

    /** Generate kode unik untuk link ?to= (G001, G002, ...) */
    public static function generateSlugName(int $weddingId): string
    {
        $existing = static::where('wedding_id', $weddingId)
            ->whereNotNull('slug_name')
            ->pluck('slug_name');
        $maxNum = $existing->map(function ($s) {
            return preg_match('/^G(\d+)$/', $s, $m) ? (int) $m[1] : 0;
        })->push(0)->max();
        return 'G' . str_pad((string) ($maxNum + 1), 3, '0', STR_PAD_LEFT);
    }

    /** Link undangan personal untuk tamu ini */
    public function invitationUrl(): string
    {
        $base = url('/' . $this->wedding->slug);
        $to   = Str::slug($this->guest_name);
        return $base . '?to=' . rawurlencode($to);
    }

    /** Sanitasi nama sebelum simpan */
    public static function sanitizeName(?string $name): string
    {
        if ($name === null || $name === '') {
            return '';
        }
        return trim(strip_tags($name));
    }

    protected static function booted(): void
    {
        static::creating(function (Guest $guest) {
            if (empty($guest->guest_name)) {
                return;
            }
            $guest->guest_name = self::sanitizeName($guest->guest_name);
            if (empty($guest->slug_name) && $guest->wedding_id) {
                $guest->slug_name = self::generateSlugName($guest->wedding_id);
            }
        });

        static::updating(function (Guest $guest) {
            if (array_key_exists('guest_name', $guest->getDirty())) {
                $guest->guest_name = self::sanitizeName($guest->guest_name);
            }
        });
    }
}
