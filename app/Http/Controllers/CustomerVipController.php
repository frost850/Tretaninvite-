<?php

namespace App\Http\Controllers;

use App\Models\Guestbook;
use App\Models\Guest;
use App\Models\Order;
use App\Models\Wedding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerVipController extends Controller
{
    /* ─── Resolve wedding dari public_token order ─────────────────────── */

    /**
     * Resolve wedding dari token order.
     * Menerima paket premium (dashboard lite) maupun VIP (dashboard penuh).
     */
    private function resolveWedding(string $token): Wedding
    {
        $order = Order::where('public_token', $token)
            ->with('wedding')
            ->firstOrFail();

        abort_if(!$order->wedding, 404, 'Undangan belum tersedia. Hubungi admin.');
        abort_unless($order->payment_status === 'lunas', 403, 'Portal hanya tersedia setelah pembayaran dikonfirmasi lunas.');
        abort_unless($order->wedding->hasPremiumDashboard(), 403, 'Portal ini hanya untuk paket Premium atau VIP.');

        return $order->wedding;
    }

    /** Guard: fitur eksklusif VIP — abort 403 jika hanya premium */
    private function requireVip(Wedding $wedding): void
    {
        abort_unless($wedding->isVip(), 403, 'Fitur ini hanya tersedia untuk paket VIP.');
    }

    /* ─── Dashboard pelanggan ─────────────────────────────────────────── */

    public function dashboard(string $token): View
    {
        $wedding = $this->resolveWedding($token);

        $guests = $wedding->guests();

        $stats = [
            'total_guests' => (clone $guests)->count(),
            'rsvp_hadir'   => (clone $guests)->where('is_attending', true)->count(),
            'rsvp_tidak'   => (clone $guests)->where('is_attending', false)->count(),
            'belum_rsvp'   => (clone $guests)->whereNull('replied_at')->count(),
            'belum_buka'   => (clone $guests)->whereNull('first_opened_at')->count(),
            'total_pax'    => (clone $guests)->where('is_attending', true)->sum('pax'),
            // VIP-only stats
            'checked_in'   => $wedding->isVip() ? (clone $guests)->whereNotNull('checked_in_at')->count() : null,
            'guestbook'    => ($wedding->isVip() && $wedding->guestbook_enabled)
                                ? $wedding->guestbook()->where('is_approved', true)->count()
                                : null,
        ];

        $view = $wedding->isVip() ? 'customer.vip.dashboard' : 'customer.premium.dashboard';

        return view($view, compact('wedding', 'stats', 'token') + ['isArchived' => $wedding->isArchived()]);
    }

    /* ─── Guestbook: lihat & moderasi ────────────────────────────────── */

    public function guestbook(string $token): View
    {
        $wedding = $this->resolveWedding($token);
        $this->requireVip($wedding);
        abort_unless($wedding->guestbook_enabled, 403, 'Guestbook belum diaktifkan.');

        $entries = $wedding->guestbook()->latest()->paginate(20);

        return view('customer.vip.guestbook', compact('wedding', 'entries', 'token'));
    }

    public function guestbookToggle(string $token, Guestbook $entry): RedirectResponse
    {
        $wedding = $this->resolveWedding($token);
        abort_unless($entry->wedding_id === $wedding->id, 403);

        $entry->update(['is_approved' => !$entry->is_approved]);

        return back()->with('success', $entry->is_approved ? 'Ucapan ditampilkan.' : 'Ucapan disembunyikan.');
    }

    public function guestbookDestroy(string $token, Guestbook $entry): RedirectResponse
    {
        $wedding = $this->resolveWedding($token);
        abort_unless($entry->wedding_id === $wedding->id, 403);

        $entry->delete();

        return redirect()->route('my.vip.guestbook', $token)
            ->with('success', 'Ucapan dihapus.');
    }

    /* ─── QR Codes ────────────────────────────────────────────────────── */

    public function qrCodes(string $token): View
    {
        $wedding = $this->resolveWedding($token);
        $this->requireVip($wedding);
        $guests  = $wedding->guests()->orderBy('guest_name')->get();

        return view('customer.vip.qr-codes', compact('wedding', 'guests', 'token'));
    }

    /* ─── Halaman scanner ─────────────────────────────────────────────── */

    public function scan(string $token): View
    {
        $wedding = $this->resolveWedding($token);
        $this->requireVip($wedding);

        $stats = [
            'total'      => $wedding->guests()->count(),
            'checked_in' => $wedding->guests()->whereNotNull('checked_in_at')->count(),
        ];

        return view('customer.vip.scan', compact('wedding', 'stats', 'token'));
    }

    /* ─── Endpoint check-in (JSON) ────────────────────────────────────── */

    public function checkIn(string $token, Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|string|max:2000',
        ]);

        $wedding = $this->resolveWedding($token);
        $this->requireVip($wedding);

        // Parse URL yang discan: ambil ?to= parameternya
        $parsed = parse_url($request->input('url'));
        parse_str($parsed['query'] ?? '', $qs);
        $toParam = $qs['to'] ?? null;

        if (!$toParam) {
            return response()->json(['status' => 'error', 'message' => 'QR tidak valid — bukan link undangan.'], 422);
        }

        // Cari tamu berdasarkan slug_name
        $guest = $wedding->guests()
            ->where(function ($q) use ($toParam) {
                $q->where('slug_name', $toParam)
                  ->orWhereRaw('LOWER(REPLACE(guest_name, \' \', \'-\')) = ?', [strtolower($toParam)]);
            })
            ->first();

        if (!$guest) {
            return response()->json(['status' => 'error', 'message' => 'Tamu tidak ditemukan untuk undangan ini.'], 404);
        }

        if ($guest->checked_in_at) {
            return response()->json([
                'status'  => 'already',
                'message' => 'Tamu sudah check-in sebelumnya.',
                'guest'   => [
                    'name'          => $guest->guest_name,
                    'group'         => $guest->group_name,
                    'pax'           => $guest->pax,
                    'checked_in_at' => $guest->checked_in_at->format('H:i'),
                ],
            ]);
        }

        $guest->update([
            'is_attending'  => true,
            'replied_at'    => $guest->replied_at ?? now(),
            'checked_in_at' => now(),
            'pax'           => $guest->pax ?? 1,
        ]);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Check-in berhasil.',
            'guest'   => [
                'name'  => $guest->guest_name,
                'group' => $guest->group_name,
                'pax'   => $guest->pax,
            ],
        ]);
    }

    /* ─── Cetak undangan fisik ────────────────────────────────────────── */

    public function printInvitations(string $token, Request $request): View
    {
        $wedding = $this->resolveWedding($token);
        // Print tersedia untuk premium & VIP (tidak perlu requireVip)

        $selectedIds = $request->input('guests');
        $guests = $selectedIds
            ? $wedding->guests()->whereIn('id', (array) $selectedIds)->orderBy('guest_name')->get()
            : $wedding->guests()->orderBy('guest_name')->get();

        $allGuests = $wedding->guests()->orderBy('guest_name')->get();

        return view('admin.print-invitations', compact('wedding', 'guests', 'allGuests', 'token'));
    }

    /* ─── Kelola Tamu (Premium & VIP) ────────────────────────────────── */

    public function guests(string $token): View
    {
        $wedding = $this->resolveWedding($token);
        $guests  = $wedding->guests()->orderBy('guest_name')->paginate(50);

        return view('customer.vip.guests', compact('wedding', 'guests', 'token'));
    }

    public function guestStore(string $token, Request $request): RedirectResponse
    {
        $wedding = $this->resolveWedding($token);

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

        return back()->with('success', "Tamu \"{$name}\" berhasil ditambahkan.");
    }

    public function guestDestroy(string $token, Guest $guest): RedirectResponse
    {
        $wedding = $this->resolveWedding($token);
        abort_unless($guest->wedding_id === $wedding->id, 403);

        $name = $guest->guest_name;
        $guest->delete();

        return back()->with('success', "Tamu \"{$name}\" dihapus.");
    }

    public function guestUpdate(string $token, Guest $guest, Request $request): RedirectResponse
    {
        $wedding = $this->resolveWedding($token);
        abort_unless($guest->wedding_id === $wedding->id, 403);

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

        $guest->update([
            'guest_name' => $name,
            'phone'      => $data['phone'] ?? null,
        ]);

        return back()->with('success', "Tamu \"{$name}\" berhasil diperbarui.");
    }
}
