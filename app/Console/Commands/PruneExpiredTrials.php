<?php

namespace App\Console\Commands;

use App\Models\Wedding;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class PruneExpiredTrials extends Command
{
    protected $signature   = 'trial:prune {--days=7 : Hapus trial yang sudah expired lebih dari X hari}';
    protected $description = 'Hapus data undangan trial yang sudah expired lebih dari X hari (default: 7 hari).';

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $cutoff  = now()->subDays($days);

        $weddings = Wedding::where('package', 'trial')
            ->where('trial_expires_at', '<', $cutoff)
            ->get();

        if ($weddings->isEmpty()) {
            $this->info("Tidak ada trial expired lebih dari {$days} hari.");
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$weddings->count()} trial untuk dihapus...");

        $deleted = 0;
        foreach ($weddings as $wedding) {
            // Hapus foto jika ada
            foreach (['bride_photo', 'groom_photo', 'couple_photo'] as $field) {
                if ($wedding->$field) {
                    Storage::disk('public')->delete($wedding->$field);
                }
            }

            // Hapus galeri
            $wedding->load('gallery');
            foreach ($wedding->gallery as $photo) {
                Storage::disk('public')->delete($photo->path);
                $photo->delete();
            }

            // Hapus tamu
            $wedding->guests()->delete();

            $wedding->delete();
            $deleted++;

            $this->line("  ✓ Dihapus: {$wedding->slug} (expired: {$wedding->trial_expires_at})");
        }

        $this->info("Selesai. {$deleted} undangan trial dihapus.");
        return self::SUCCESS;
    }
}
