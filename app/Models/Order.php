<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property \Carbon\Carbon|null $event_date
 * @property \Carbon\Carbon|null $expires_at
 */
class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'template',
        'package',
        'bride_name',
        'groom_name',
        'event_date',
        'location',
        'customer_name',
        'customer_phone',
        'customer_email',
        'notes',
        'renewal_days',
        'status',
        'payment_status',
        'payment_proof',
        'payment_token',
        'public_token',
        'expires_at',
        'wedding_id',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'expires_at' => 'datetime',
            'customer_email' => 'string',
        ];
    }

    /** Generate token unik saat order dibuat */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->payment_token = Str::random(48);
            $order->public_token  = Str::random(32);
        });
    }

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }

    /** Apakah order sudah kedaluwarsa (belum bayar & waktu habis) */
    public function isExpired(): bool
    {
        if ($this->payment_status !== 'belum_bayar') {
            return false; // sudah upload, jangan dihapus
        }
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    /** Sisa detik sebelum kedaluwarsa */
    public function secondsRemaining(): int
    {
        if (!$this->expires_at) return 0;
        return max(0, now()->diffInSeconds($this->expires_at, false));
    }

    /** Apakah pesanan ini untuk template ulang tahun? */
    public function isBirthday(): bool
    {
        return str_starts_with($this->template ?? '', 'birthday');
    }

    /** Apakah pesanan ini untuk template greeting card? */
    public function isGreeting(): bool
    {
        return str_starts_with($this->template ?? '', 'greeting');
    }

    /** Apakah pesanan ini untuk template anniversary? */
    public function isAnniversary(): bool
    {
        return str_starts_with($this->template ?? '', 'anniversary');
    }

    /** Apakah ini pesanan perpanjangan? */
    public function isRenewal(): bool
    {
        return !is_null($this->renewal_days) && $this->renewal_days > 0;
    }

    /**
     * Harga perpanjangan berdasarkan hari.
     * Rp 1.000/hari, 10 hari = Rp 9.000 (diskon), 30 hari = Rp 27.000 (diskon).
     */
    public static function renewalPriceFor(int $days): int
    {
        return match (true) {
            $days === 30 => 27000,
            $days === 10 => 9000,
            default      => $days * 1000,
        };
    }

    /** Harga paket dalam rupiah (integer) */
    public function packageAmount(): int
    {
        if ($this->isRenewal()) {
            return self::renewalPriceFor((int) $this->renewal_days);
        }

        if ($this->isBirthday() || $this->isGreeting()) {
            return match ($this->package ?? 'basic') {
                'premium' => 49999,
                default   => 39999,
            };
        }
        return match ($this->package ?? 'basic') {
            'premium' => 89000,
            'vip'     => 199000,
            'trial'   => 0,
            default   => 59000,
        };
    }

    /** Label paket yang dipesan */
    public function packageLabel(): string
    {
        if ($this->isRenewal()) {
            return '🔄 Perpanjang ' . $this->renewal_days . ' Hari';
        }
        return match ($this->package ?? 'basic') {
            'trial'   => '🔖 Trial',
            'premium' => '⭐ Premium',
            'vip'     => '♛ VIP',
            default   => '💙 Basic',
        };
    }

    /** Harga paket dalam rupiah (string format) */
    public function packagePrice(): string
    {
        $amt = $this->packageAmount();
        return 'Rp ' . number_format($amt, 0, ',', '.');
    }

    /** Warna badge paket */
    public function packageColor(): string
    {
        return match ($this->package ?? 'basic') {
            'premium' => 'bg-amber-900/40 text-amber-300 border border-amber-700/40',
            'vip'     => 'bg-purple-900/40 text-purple-300 border border-purple-700/40',
            default   => 'bg-blue-900/40 text-blue-300 border border-blue-700/40',
        };
    }

    /** Label status yang ramah */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'baru'      => '🆕 Baru',
            'diproses'  => '⚙️ Diproses',
            'selesai'   => '✅ Selesai',
            default     => $this->status,
        };
    }

    /** Warna badge status */
    public function statusColor(): string
    {
        return match ($this->status) {
            'baru'     => 'bg-amber-900/40 text-amber-300',
            'diproses' => 'bg-blue-900/40 text-blue-300',
            'selesai'  => 'bg-green-900/40 text-green-300',
            default    => 'bg-slate-800 text-slate-400',
        };
    }

    /** Label status pembayaran */
    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'belum_bayar'          => '⏳ Belum Bayar',
            'menunggu_konfirmasi'  => '🔍 Menunggu Konfirmasi',
            'lunas'                => '✅ Lunas',
            'ditolak'              => '❌ Ditolak',
            default                => $this->payment_status,
        };
    }

    /** Warna badge payment status */
    public function paymentStatusColor(): string
    {
        return match ($this->payment_status) {
            'belum_bayar'         => 'bg-red-900/40 text-red-300',
            'menunggu_konfirmasi' => 'bg-yellow-900/40 text-yellow-300',
            'lunas'               => 'bg-green-900/40 text-green-300',
            'ditolak'             => 'bg-red-900/40 text-red-300 border border-red-700/40',
            default               => 'bg-slate-800 text-slate-400',
        };
    }

    /** WA link untuk hubungi admin dengan ringkasan order (konteks: order baru) */
    public function adminWhatsappLink(string $adminPhone): string
    {
        $nama = $this->groom_name ? "{$this->bride_name} & {$this->groom_name}" : $this->bride_name;
        $msg = "Halo, saya baru saja memesan undangan digital.\n\n"
            . "Order ID: #{$this->id}\n"
            . "Nama: {$nama}\n"
            . "Template: {$this->template}\n"
            . "Paket: " . $this->packageLabel() . " (" . $this->packagePrice() . ")\n"
            . ($this->event_date ? "Tanggal: " . $this->event_date->format('d M Y') . "\n" : '')
            . ($this->location ? "Lokasi: {$this->location}\n" : '')
            . "Nama pemesan: {$this->customer_name}\n"
            . "No WA: {$this->customer_phone}\n"
            . ($this->notes ? "Catatan: {$this->notes}\n" : '');

        return 'https://wa.me/' . preg_replace('/\D+/', '', $adminPhone) . '?text=' . rawurlencode($msg);
    }

    /** WA link khusus konfirmasi pembayaran */
    public function paymentWhatsappLink(string $adminPhone): string
    {
        $nama = $this->groom_name ? "{$this->bride_name} & {$this->groom_name}" : $this->bride_name;
        $msg = "Halo, saya sudah melakukan pembayaran untuk undangan digital.\n\n"
            . "Order ID: #{$this->id}\n"
            . "Nama: {$nama}\n"
            . "Template: {$this->template}\n"
            . "Paket: " . $this->packageLabel() . " (" . $this->packagePrice() . ")\n"
            . "Nama pemesan: {$this->customer_name}\n"
            . "No WA: {$this->customer_phone}\n\n"
            . "Mohon dikonfirmasi pembayarannya. Terima kasih 🙏";

        return 'https://wa.me/' . preg_replace('/\D+/', '', $adminPhone) . '?text=' . rawurlencode($msg);
    }

    /**
     * Generate WA link dari admin ke customer dengan template pesan sesuai konteks.
     *
     * @param string $type  Salah satu: 'lunas' | 'diproses' | 'selesai' | 'pengingat_bayar'
     * @param array  $opts  Opsi tambahan: ['wedding_url', 'tracking_url', 'payment_url']
     */
    public function customerWhatsappLink(string $type, array $opts = []): string
    {
        $nama  = $this->groom_name ? "{$this->bride_name} & {$this->groom_name}" : $this->bride_name;
        $phone = preg_replace('/[^0-9]/', '', $this->customer_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $msg = match ($type) {
            'lunas' =>
                "Halo {$this->customer_name},\n\n"
                . "Pembayaran undangan *{$nama}* ({$this->packageLabel()} - {$this->packagePrice()}) sudah kami terima dan dikonfirmasi.\n\n"
                . "Undangan Anda sedang kami proses dan akan segera kami kabari bila sudah selesai. Terima kasih.",

            'diproses' =>
                "Halo {$this->customer_name},\n\n"
                . "Undangan *{$nama}* sedang kami kerjakan.\n\n"
                . "Estimasi selesai dalam 1x24 jam. Kami akan menghubungi Anda kembali saat undangan sudah siap. Terima kasih atas kesabarannya.",

            'selesai' =>
                "Halo {$this->customer_name},\n\n"
                . "Undangan digital *{$nama}* sudah selesai dan siap digunakan.\n\n"
                . (isset($opts['wedding_url']) ? "Link undangan: {$opts['wedding_url']}\n" : '')
                . (isset($opts['tracking_url']) ? "Tracking tamu: {$opts['tracking_url']}\n" : '')
                . "\nSilakan dicek, dan hubungi kami jika ada yang perlu direvisi. Terima kasih.",

            'pengingat_bayar' =>
                "Halo {$this->customer_name},\n\n"
                . "Kami ingin mengingatkan bahwa pesanan undangan *{$nama}* ({$this->packageLabel()} - {$this->packagePrice()}) belum kami terima pembayarannya.\n\n"
                . (isset($opts['payment_url']) ? "Lanjutkan pembayaran di sini:\n{$opts['payment_url']}\n\n" : '')
                . "Jika ada pertanyaan, silakan hubungi kami. Terima kasih.",

            'ditolak' =>
                "Halo {$this->customer_name},\n\n"
                . "Mohon maaf, kami tidak dapat mengkonfirmasi pembayaran untuk pesanan undangan *{$nama}*.\n\n"
                . (isset($opts['reason']) ? "Alasan: {$opts['reason']}\n\n" : '')
                . "Silakan hubungi kami untuk informasi lebih lanjut atau melakukan pembayaran ulang. Terima kasih.",

            default => '',
        };

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($msg);
    }
}
