<?php

namespace App\Console\Commands;

use App\Mail\ExpiryReminderMail;
use App\Models\Order;
use App\Models\Wedding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyExpiringWeddings extends Command
{
    protected $signature   = 'weddings:notify-expiry';
    protected $description = 'Kirim email pengingat ke pelanggan 2 hari sebelum undangan expired.';

    public function handle(): int
    {
        $sent = 0;

        // ── H-2: expired antara 44 – 52 jam dari sekarang ────────────────────
        $in2d = Wedding::whereNotNull('trial_expires_at')
            ->whereBetween('trial_expires_at', [
                now()->addHours(44), // batas bawah: 44 jam
                now()->addHours(52), // batas atas:  52 jam
            ])
            ->whereNull('expiry_notified_2d_at')
            ->get();

        foreach ($in2d as $wedding) {
            $email = $this->resolveEmail($wedding);
            if ($email) {
                $customerName = $this->resolveCustomerName($wedding);
                Mail::to($email)->send(new ExpiryReminderMail($wedding, $customerName));
                $wedding->update(['expiry_notified_2d_at' => now()]);
                $this->line("✉ H-2 — {$wedding->slug} → {$email}");
                $sent++;
            } else {
                // Tandai tetap agar tidak diproses ulang terus-menerus
                $wedding->update(['expiry_notified_2d_at' => now()]);
                $this->line("⏭ H-2 — {$wedding->slug} — tidak ada email, dilewati");
            }
        }

        if ($sent === 0 && $in2d->isEmpty()) {
            $this->info('Tidak ada undangan yang perlu dinotifikasi saat ini.');
        } else {
            $this->info("Selesai. {$sent} email terkirim.");
        }

        return self::SUCCESS;
    }

    /**
     * Cari customer_email dari order yang terhubung ke wedding ini.
     * Gunakan order terbaru yang punya email.
     */
    private function resolveEmail(Wedding $wedding): ?string
    {
        // Prioritas 1: notify_email (pemilik undangan mengisi sendiri di form VIP)
        if ($wedding->notify_email && filter_var($wedding->notify_email, FILTER_VALIDATE_EMAIL)) {
            return $wedding->notify_email;
        }

        // Prioritas 2: customer_email dari order yang terhubung
        $order = Order::where('wedding_id', $wedding->id)
            ->whereNotNull('customer_email')
            ->where('customer_email', '!=', '')
            ->latest()
            ->first();

        if ($order && filter_var($order->customer_email, FILTER_VALIDATE_EMAIL)) {
            return $order->customer_email;
        }

        return null;
    }

    private function resolveCustomerName(Wedding $wedding): string
    {
        $order = Order::where('wedding_id', $wedding->id)
            ->whereNotNull('customer_name')
            ->latest()
            ->first();

        return $order?->customer_name ?? $wedding->bride_name;
    }
}
