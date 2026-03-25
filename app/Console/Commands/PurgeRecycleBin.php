<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Wedding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PurgeRecycleBin extends Command
{
    protected $signature   = 'recycle:purge {--days=30 : Hapus item yang sudah di-trash lebih dari N hari}';
    protected $description = 'Hapus permanen item di Recycle Bin yang sudah melewati batas waktu.';

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $cutoff  = now()->subDays($days);

        $weddings = Wedding::onlyTrashed()->where('deleted_at', '<', $cutoff)->get();
        $orders   = Order::onlyTrashed()->where('deleted_at', '<', $cutoff)->get();

        if ($weddings->isEmpty() && $orders->isEmpty()) {
            $this->info("Tidak ada item di Recycle Bin yang melewati {$days} hari.");
            return self::SUCCESS;
        }

        $weddingCount = 0;
        foreach ($weddings as $wedding) {
            $wedding->load('gallery');

            // Hapus foto galeri
            foreach ($wedding->gallery as $photo) {
                Storage::disk('public')->delete($photo->path);
                $photo->forceDelete();
            }

            // Hapus foto profil
            foreach ([
                'bride_photo', 'groom_photo', 'couple_photo',
                'cover_photo', 'bg_mempelai_photo', 'bg_acara_photo', 'bg_lokasi_photo',
            ] as $field) {
                if ($wedding->$field) {
                    Storage::disk('public')->delete($wedding->$field);
                }
            }

            $wedding->guests()->forceDelete();
            $wedding->forceDelete();

            $this->line("Purge wedding: {$wedding->slug} (dihapus {$wedding->deleted_at})");
            $weddingCount++;
        }

        $orderCount = 0;
        foreach ($orders as $order) {
            if ($order->payment_proof && Storage::disk('local')->exists($order->payment_proof)) {
                Storage::disk('local')->delete($order->payment_proof);
            }

            $this->line("Purge order: #{$order->id} {$order->customer_name} (dihapus {$order->deleted_at})");
            $order->forceDelete();
            $orderCount++;
        }

        $this->info("Recycle Bin purge selesai: {$weddingCount} undangan, {$orderCount} pesanan dihapus permanen.");
        return self::SUCCESS;
    }
}
