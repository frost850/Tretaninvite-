<?php

namespace App\Http\Controllers;

use App\Models\Guestbook;
use App\Models\Guest;
use App\Models\Order;
use App\Models\Wedding;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class VipController extends Controller
{
    /* ═══════════════════════════════════════════════════
       HELPERS
    ═══════════════════════════════════════════════════ */

    private function vipWedding(int $id): Wedding
    {
        $w = Wedding::findOrFail($id);
        abort_unless($w->isVip(), 403, 'Fitur ini hanya untuk paket VIP.');
        return $w;
    }

    /* ═══════════════════════════════════════════════════
       VIP DASHBOARD — hub semua fitur VIP
    ═══════════════════════════════════════════════════ */

    public function dashboard(Request $request): View
    {
        $weddingId = $request->query('wedding_id');
        abort_if(!$weddingId, 400);
        $wedding = $this->vipWedding((int) $weddingId);

        $stats = [
            'total_guests'  => $wedding->guests()->count(),
            'rsvp_hadir'    => $wedding->guests()->where('is_attending', true)->count(),
            'rsvp_tidak'    => $wedding->guests()->where('is_attending', false)->count(),
            'belum_buka'    => $wedding->guests()->whereNull('first_opened_at')->count(),
            'total_pax'     => $wedding->guests()->where('is_attending', true)->sum('pax'),
            'checked_in'    => $wedding->guests()->whereNotNull('checked_in_at')->count(),
            'guestbook'     => $wedding->guestbook_enabled ? $wedding->guestbook()->where('is_approved', true)->count() : null,
        ];

        // Token untuk link portal pelanggan
        $customerToken = Order::where('wedding_id', $wedding->id)->latest()->value('public_token');

        return view('admin.vip.dashboard', compact('wedding', 'stats', 'customerToken'));
    }

    /* ═══════════════════════════════════════════════════
       LIVE RSVP — polling endpoint (JSON)
       GET /admin/vip/{wedding}/rsvp-live/data
    ═══════════════════════════════════════════════════ */

    public function rsvpLive(Request $request, Wedding $wedding): View
    {
        abort_unless($wedding->isVip(), 403);
        return view('admin.vip.rsvp-live', compact('wedding'));
    }

    public function rsvpLiveData(Wedding $wedding): JsonResponse
    {
        abort_unless($wedding->isVip(), 403);

        $guests = $wedding->guests()
            ->whereNotNull('replied_at')
            ->orderByDesc('replied_at')
            ->take(50)
            ->get(['id', 'guest_name', 'group_name', 'is_attending', 'pax', 'notes', 'replied_at']);

        $summary = [
            'hadir'  => $wedding->guests()->where('is_attending', true)->count(),
            'tidak'  => $wedding->guests()->where('is_attending', false)->count(),
            'total_pax' => $wedding->guests()->where('is_attending', true)->sum('pax'),
        ];

        return response()->json([
            'guests'  => $guests->map(fn($g) => [
                'name'      => $g->guest_name,
                'group'     => $g->group_name,
                'attending' => $g->is_attending,
                'pax'       => $g->pax,
                'notes'     => $g->notes,
                'time'      => $g->replied_at?->diffForHumans(),
            ]),
            'summary' => $summary,
        ]);
    }

    /* ═══════════════════════════════════════════════════
       QR CODE — halaman per tamu
    ═══════════════════════════════════════════════════ */

    public function qrCodes(Request $request): View
    {
        $weddingId = $request->query('wedding_id');
        abort_if(!$weddingId, 400);
        $wedding = $this->vipWedding((int) $weddingId);

        $guests = $wedding->guests()->orderBy('guest_name')->get();

        return view('admin.vip.qr-codes', compact('wedding', 'guests'));
    }

    /* ═══════════════════════════════════════════════════
       GUESTBOOK — admin lihat & moderasi
    ═══════════════════════════════════════════════════ */

    public function guestbook(Request $request): View
    {
        $weddingId = $request->query('wedding_id');
        abort_if(!$weddingId, 400);
        $wedding = $this->vipWedding((int) $weddingId);
        abort_unless($wedding->guestbook_enabled, 403, 'Guestbook belum diaktifkan.');

        $entries = $wedding->guestbook()->paginate(20);

        return view('admin.vip.guestbook', compact('wedding', 'entries'));
    }

    public function guestbookToggle(Guestbook $entry): RedirectResponse
    {
        $wedding = $entry->wedding;
        abort_unless($wedding && $wedding->isVip(), 403);
        // Pastikan entry benar-benar milik wedding VIP yang dikelola admin saat ini
        // (cek dilakukan via route model binding + isVip() — tidak perlu perbandingan tautologis))
        $entry->update(['is_approved' => !$entry->is_approved]);

        return back()->with('success', $entry->is_approved ? 'Ucapan ditampilkan.' : 'Ucapan disembunyikan.');
    }

    public function guestbookDestroy(Guestbook $entry): mixed
    {
        $wedding   = $entry->wedding;
        // P7-4: pastikan entry milik wedding VIP yang sah
        abort_unless($wedding && $wedding->isVip(), 403);
        $weddingId = $entry->wedding_id;
        $entry->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Ucapan dihapus.']);
        }

        return redirect()->route('admin.vip.guestbook', ['wedding_id' => $weddingId])
            ->with('success', 'Ucapan dihapus.');
    }

    /* ═══════════════════════════════════════════════════
       VIP SETTINGS — video, password, guestbook toggle, notif email, extra events
    ═══════════════════════════════════════════════════ */

    public function settings(Request $request): View
    {
        $weddingId = $request->query('wedding_id');
        abort_if(!$weddingId, 400);
        $wedding = $this->vipWedding((int) $weddingId);

        return view('admin.vip.settings', compact('wedding'));
    }

    public function settingsUpdate(Request $request, Wedding $wedding): RedirectResponse
    {
        abort_unless($wedding->isVip(), 403);

        $data = $request->validate([
            'video_url'         => 'nullable|url|max:500',
            'vip_password'      => 'nullable|string|min:4|max:100',
            'guestbook_enabled' => 'boolean',
            'notify_email'      => 'nullable|email|max:255',
            'extra_events'      => 'nullable|array|max:5',
            'extra_events.*.label'    => 'nullable|string|max:80',
            'extra_events.*.date'     => 'nullable|date',
            'extra_events.*.time'     => 'nullable|string|max:20',
            'extra_events.*.location' => 'nullable|string|max:255',
        ]);

        // Password: kosongkan jika tidak diisi (bukan ubah yg lama), hash jika diisi
        if (empty($data['vip_password'])) {
            unset($data['vip_password']);
        } else {
            $data['vip_password'] = bcrypt($data['vip_password']);
        }

        $data['guestbook_enabled'] = $request->boolean('guestbook_enabled');

        // Bersihkan extra_events kosong
        if (isset($data['extra_events'])) {
            $data['extra_events'] = array_values(
                array_filter($data['extra_events'], fn($e) => !empty($e['label']))
            );
        }

        $wedding->update($data);

        return back()->with('success', 'Pengaturan VIP disimpan.');
    }

    public function settingsClearPassword(Wedding $wedding): RedirectResponse
    {
        abort_unless($wedding->isVip(), 403);
        $wedding->update(['vip_password' => null]);

        return back()->with('success', 'Password dihapus — undangan kini terbuka.');
    }

    /* ═══════════════════════════════════════════════════
       QR SCAN CHECK-IN — halaman scanner & endpoint API
    ═══════════════════════════════════════════════════ */

    public function scanPage(Request $request): View
    {
        $weddingId = $request->query('wedding_id');
        abort_if(!$weddingId, 400);
        $wedding = $this->vipWedding((int) $weddingId);

        $stats = [
            'total'      => $wedding->guests()->count(),
            'checked_in' => $wedding->guests()->whereNotNull('checked_in_at')->count(),
        ];

        return view('admin.vip.scan', compact('wedding', 'stats'));
    }

    public function checkIn(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'wedding_id' => 'required|integer|exists:weddings,id',
            'url'        => 'required|string|max:2000',
        ]);

        $wedding = $this->vipWedding((int) $request->input('wedding_id'));

        // Parse URL yang discan: ambil ?to= parameternya
        $parsed = parse_url($request->input('url'));
        parse_str($parsed['query'] ?? '', $qs);
        $toParam = $qs['to'] ?? null;

        if (!$toParam) {
            return response()->json(['status' => 'error', 'message' => 'QR tidak valid — bukan link undangan.'], 422);
        }

        // Cari tamu: cocokkan slug_name ATAU slug dari guest_name
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
                    'name'         => $guest->guest_name,
                    'group'        => $guest->group_name,
                    'pax'          => $guest->pax,
                    'checked_in_at'=> $guest->checked_in_at->format('H:i'),
                ],
            ]);
        }

        // Tandai hadir + check-in
        $guest->update([
            'is_attending'  => true,
            'replied_at'    => $guest->replied_at ?? now(),
            'checked_in_at' => now(),
            'pax'           => $guest->pax ?? 1,
        ]);

        return response()->json([
            'status'  => 'ok',
            'message' => 'Check-in berhasil!',
            'guest'   => [
                'name'  => $guest->guest_name,
                'group' => $guest->group_name,
                'pax'   => $guest->pax,
            ],
        ]);
    }

    /* ═══════════════════════════════════════════════════
       EXPORT LAPORAN — VIP extended Excel
    ═══════════════════════════════════════════════════ */

    public function exportReport(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $weddingId = $request->query('wedding_id');
        abort_if(!$weddingId, 400);
        $wedding = $this->vipWedding((int) $weddingId);

        $filename = 'laporan-vip-' . $wedding->slug . '-' . now()->format('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\VipGuestsExport($wedding),
            $filename
        );
    }
}
