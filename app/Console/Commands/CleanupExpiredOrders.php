<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredOrders extends Command
{
    protected $signature   = 'orders:cleanup-expired';
    protected $description = 'Hapus pesanan yang kedaluwarsa sebelum melakukan pembayaran.';

    public function handle(): int
    {
        $expired = Order::where('payment_status', 'belum_bayar')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Tidak ada pesanan yang kedaluwarsa.');
            return self::SUCCESS;
        }

        foreach ($expired as $order) {
            // Hapus file bukti (jika ada, walaupun belum_bayar seharusnya tidak ada)
            if ($order->payment_proof && Storage::disk('local')->exists($order->payment_proof)) {
                Storage::disk('local')->delete($order->payment_proof);
            }
            $order->delete();
            $this->line("Dihapus: Order #{$order->id} ({$order->customer_name}) – kedaluwarsa {$order->expires_at}");
        }

        $this->info("Selesai. {$expired->count()} pesanan dihapus.");
        return self::SUCCESS;
    }
}
