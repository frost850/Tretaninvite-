<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Wedding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrialGuestController extends Controller
{
    /** Cari wedding trial by slug, abort jika bukan trial */
    private function resolveTrialWedding(string $slug): Wedding
    {
        $wedding = Wedding::where('slug', $slug)->firstOrFail();

        abort_if(!$wedding->isTrial(), 403, 'Hanya tersedia untuk undangan percobaan.');

        return $wedding;
    }

    /**
     * Verifikasi kepemilikan:
     * 1. Session token disimpan saat wedding dibuat (primer).
     * 2. IP matching sebagai fallback jika session hilang (misal tutup browser & buka lagi).
     */
    private function canManage(Wedding $wedding): bool
    {
        return session('trial_manage:' . $wedding->slug) === $wedding->id
            || request()->ip() === $wedding->creator_ip;
    }

    /** Halaman daftar & tambah tamu — publik, tanpa login */
    public function index(string $slug): View
    {
        $wedding    = $this->resolveTrialWedding($slug);
        $guests     = $wedding->guests()->orderBy('id')->get();
        $limit      = $wedding->guestLimit();
        $canManage  = $this->canManage($wedding);

        return view('trial.guests', compact('wedding', 'guests', 'limit', 'canManage'));
    }

    /** Tambah satu tamu */
    public function store(Request $request, string $slug): RedirectResponse
    {
        $wedding = $this->resolveTrialWedding($slug);

        // Hanya pemilik trial yang boleh menambah tamu
        abort_if(!$this->canManage($wedding), 403, 'Anda tidak memiliki akses untuk menambah tamu.');

        // Cek batas
        $limit        = $wedding->guestLimit();
        $currentCount = $wedding->guests()->count();
        if ($limit !== null && $currentCount >= $limit) {
            return back()->withErrors([
                'guest_name' => "Batas {$limit} tamu untuk ujicoba sudah tercapai. Upgrade paket untuk menambah lebih banyak.",
            ])->withInput();
        }

        $data = $request->validate([
            'guest_name' => 'required|string|max:255',
            'phone'      => 'nullable|string|max:20',
        ], [
            'guest_name.required' => 'Nama tamu tidak boleh kosong.',
        ]);

        $name = Guest::sanitizeName($data['guest_name']);
        if (empty($name)) {
            return back()->withErrors(['guest_name' => 'Nama tidak valid.'])->withInput();
        }

        Guest::create([
            'wedding_id' => $wedding->id,
            'guest_name' => $name,
            'slug_name'  => Guest::generateSlugName($wedding->id),
            'phone'      => $data['phone'] ?? null,
        ]);

        return back()->with('success', "✅ Tamu \"{$name}\" berhasil ditambahkan.");
    }

    /** Hapus tamu — hanya pemilik trial yang boleh menghapus */
    public function destroy(string $slug, Guest $guest): RedirectResponse
    {
        $wedding = $this->resolveTrialWedding($slug);

        abort_if($guest->wedding_id !== $wedding->id, 403);
        abort_if(!$this->canManage($wedding), 403, 'Anda tidak memiliki akses untuk menghapus tamu ini.');

        $name = $guest->guest_name;
        $guest->delete();

        return back()->with('success', "🗑️ Tamu \"{$name}\" dihapus.");
    }
}
