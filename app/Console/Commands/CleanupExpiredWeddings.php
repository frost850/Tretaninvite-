<?php

namespace App\Console\Commands;

use App\Models\Wedding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredWeddings extends Command
{
    protected $signature   = 'weddings:cleanup-expired';
    protected $description = 'Hapus undangan yang sudah melewati masa aktifnya (trial/basic/premium).';

    public function handle(): int
    {
        $expired = Wedding::whereNotNull('trial_expires_at')
            ->where('trial_expires_at', '<', now())
            ->with('guests')
            ->get();

        if ($expired->isEmpty()) {
            $this->info('Tidak ada undangan yang kedaluwarsa.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($expired as $wedding) {
            // Hapus foto profil & galeri dari storage
            $photos = array_filter([
                $wedding->bride_photo,
                $wedding->groom_photo,
                $wedding->couple_photo,
            ]);
            foreach ($photos as $photo) {
                if ($photo && Storage::disk('public')->exists($photo)) {
                    Storage::disk('public')->delete($photo);
                }
            }

            // Hapus gambar galeri
            foreach ($wedding->gallery ?? [] as $item) {
                if ($item->path && Storage::disk('public')->exists($item->path)) {
                    Storage::disk('public')->delete($item->path);
                }
            }

            $this->line("Hapus: {$wedding->slug} ({$wedding->package}, expired {$wedding->trial_expires_at})");
            $wedding->delete(); // cascade ke guests via DB
            $count++;
        }

        $this->info("Selesai. {$count} undangan dihapus.");
        return self::SUCCESS;
    }
}
