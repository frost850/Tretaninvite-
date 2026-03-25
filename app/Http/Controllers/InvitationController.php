<?php

namespace App\Http\Controllers;

use App\Mail\RsvpNotification;
use App\Models\Guestbook;
use App\Models\Guest;
use App\Models\Wedding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TemplateRegistry;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * Tampilkan undangan. Cek password VIP sebelum render.
     */
    public function show(Request $request, string $slug)
    {
        $wedding = Wedding::where('slug', $slug)
            ->where('is_active', true)
            ->with('galleries')
            ->firstOrFail();

        // Trial expired → halaman expired (tidak bisa dilanjutkan)
        if ($wedding->isTrial() && $wedding->isExpired()) {
            return view('invitation.expired', compact('wedding'));
        }

        // Non-trial expired → mode arsip (bisa dilihat, RSVP/guestbook dinonaktifkan)
        $isArchived = $wedding->isArchived();

        // ── VIP: Password protection ──────────────────────────────────────────
        if ($wedding->vip_password) {
            $sessionKey = 'vip_unlocked_' . $wedding->id;
            if (!session($sessionKey)) {
                return view('invitation.password', compact('wedding'));
            }
        }

        $guest = null;

        if (!str_starts_with($wedding->template ?? '', 'greeting')) {
            $to = $request->query('to');

            if ($to !== null && $to !== '') {
                $to = trim($to);
                $guest = Guest::where('wedding_id', $wedding->id)
                    ->where(function ($q) use ($to) {
                        $q->where('slug_name', $to)
                            ->orWhereRaw('LOWER(guest_name) = ?', [mb_strtolower($to, 'UTF-8')]);
                    })
                    ->first();

                if (!$guest) {
                    $guest = Guest::where('wedding_id', $wedding->id)
                        ->get()
                        ->first(fn($g) => Str::slug($g->guest_name) === strtolower($to));
                }

                if ($guest) {
                    DB::table('guests')
                        ->where('id', $guest->id)
                        ->update([
                            'first_opened_at' => $guest->first_opened_at ?? now(),
                            'open_count'      => $guest->open_count + 1,
                        ]);
                    $guest->refresh();
                }
            }
        }

        $template = $wedding->template ?? 'classic';
        $viewName = TemplateRegistry::viewPath($template);
        if (!view()->exists($viewName)) {
            $viewName = TemplateRegistry::viewPath('classic');
        }

        $isGreeting = str_starts_with($template, 'greeting');
        $rsvps = $isGreeting
            ? collect()
            : Guest::where('wedding_id', $wedding->id)
                ->whereNotNull('replied_at')
                ->whereNotNull('notes')
                ->where('notes', '!=', '')
                ->orderBy('replied_at', 'desc')
                ->limit(20)
                ->get();

        // ── VIP: Guestbook entries ─────────────────────────────────────────────
        $guestbookEntries = $wedding->hasGuestbookAccess()
            ? Guestbook::where('wedding_id', $wedding->id)
                ->where('is_approved', true)
                ->orderByDesc('created_at')
                ->limit(50)
                ->get()
            : collect();

        return view($viewName, [
            'wedding'          => $wedding,
            'guest'            => $guest,
            'rsvps'            => $rsvps,
            'guestbookEntries' => $guestbookEntries,
            'isArchived'       => $isArchived,
        ]);
    }

    /**
     * Verifikasi password VIP — simpan di session lalu redirect ke undangan.
     */
    public function unlock(Request $request, string $slug)
    {
        $wedding = Wedding::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if (!$wedding->vip_password) {
            return redirect('/' . $slug . ($request->query('to') ? '?to=' . $request->query('to') : ''));
        }

        $stored   = (string) $wedding->vip_password;
        $input    = (string) $request->input('password', '');
        $isHashed = str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon');
        $valid    = $isHashed
            ? \Illuminate\Support\Facades\Hash::check($input, $stored)
            : hash_equals($stored, $input);

        if ($valid) {
            // Re-hash plaintext passwords on the fly so old records get upgraded silently
            if (!$isHashed) {
                $wedding->update(['vip_password' => bcrypt($input)]);
            }
            session(['vip_unlocked_' . $wedding->id => true]);
            $to = $request->input('to');
            return redirect('/' . $slug . ($to ? '?to=' . rawurlencode($to) : ''));
        }

        return back()->with('error', 'Password salah. Silakan coba lagi.');
    }

    /**
     * Store RSVP — kirim notif email jika VIP dan notify_email tersedia.
     */
    public function storeRsvp(Request $request, string $slug)
    {
        $wedding = Wedding::where('slug', $slug)
            ->where('is_active', true)
            ->with('galleries')
            ->firstOrFail();

        if ($wedding->isExpired()) {
            $msg = $wedding->isArchived()
                ? 'Masa aktif undangan sudah habis. RSVP tidak lagi tersedia.'
                : 'Undangan sudah tidak aktif.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect('/' . $slug)->with('error', $msg);
        }

        // Normalize field aliases used by different templates
        // VIP/patisserie templates: name→guest_name, vr_hadir→attendance, pesan→message, jumlah→guests_count
        $merge = [];
        if (!$request->has('guest_name') && $request->has('name')) {
            $merge['guest_name'] = $request->input('name');
        }
        if (!$request->has('attendance') && $request->has('vr_hadir')) {
            $merge['attendance'] = $request->input('vr_hadir');
        }
        if (!$request->has('message') && $request->has('pesan')) {
            $merge['message'] = $request->input('pesan');
        }
        if (!$request->has('guests_count') && $request->has('jumlah')) {
            $merge['guests_count'] = $request->input('jumlah');
        }
        if (!empty($merge)) {
            $request->merge($merge);
        }

        $validatedData = $request->validate([
            'guest_name'   => 'required|string|max:255',
            'phone'        => ['nullable', 'string', 'max:20', 'regex:/^[0-9\+\-\s\(\)]+$/'],
            'attendance'   => 'required|in:hadir,mungkin,tidak_hadir',
            'guests_count' => 'nullable|integer|min:1|max:20',
            'message'      => 'nullable|string|max:1000',
        ]);

        $guestName = trim($validatedData['guest_name']);
        $guest = Guest::where('wedding_id', $wedding->id)
            ->where(function ($q) use ($guestName) {
                $q->where('slug_name', $guestName)
                    ->orWhereRaw('LOWER(guest_name) = ?', [mb_strtolower($guestName, 'UTF-8')]);
            })
            ->first();

        if (!$guest) {
            // Cek batas tamu sebelum membuat tamu baru dari RSVP
            $limit = $wedding->guestLimit();
            if ($limit !== null && $wedding->guests()->count() >= $limit) {
                return back()->withErrors(['guest_name' => 'Daftar tamu sudah penuh.'])->withInput();
            }
            $guest = Guest::create([
                'wedding_id' => $wedding->id,
                'guest_name' => $guestName,
                'phone'      => $validatedData['phone'] ?? null,
                'slug_name'  => Str::slug($guestName),
            ]);
        } else {
            if (!empty($validatedData['phone'])) {
                $guest->phone = $validatedData['phone'];
            }
        }

        $guest->is_attending = $validatedData['attendance'] === 'hadir';
        $guest->replied_at   = now();
        $guest->pax = $validatedData['attendance'] !== 'tidak_hadir' && !empty($validatedData['guests_count'])
            ? (int) $validatedData['guests_count']
            : null;

        if (!empty($validatedData['message'])) {
            // BH-7: Batasi ukuran total notes agar tidak tumbuh tak terbatas (DoS via DB row bloat)
            $combined     = ($guest->notes ? $guest->notes . "\n" : '') . '[RSVP] ' . $validatedData['message'];
            $guest->notes = mb_substr($combined, 0, 5000);
        }

        $guest->save();

        // ── VIP: Kirim notif email setelah RSVP ──────────────────────────────
        if ($wedding->isVip() && $wedding->notify_email) {
            try {
                Mail::to($wedding->notify_email)->queue(new RsvpNotification($wedding, $guest));
            } catch (\Throwable) {
                // Gagal kirim email tidak boleh hentikan flow utama
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih, konfirmasi Anda telah tercatat.',
                'rsvp'    => [
                    'guest_name' => $guest->guest_name,
                    'attendance' => $validatedData['attendance'],
                    'message'    => $validatedData['message'] ?? '',
                ],
            ]);
        }

        $url = url('/' . $wedding->slug . ($guest ? '?to=' . rawurlencode(\Illuminate\Support\Str::slug($guest->guest_name)) : ''));
        return redirect($url)->with('rsvp_success', 'Terima kasih, konfirmasi Anda telah tercatat.');
    }

    /**
     * Store guestbook entry (VIP: tamu tulis ucapan di halaman undangan).
     */
    public function storeGuestbook(Request $request, string $slug)
    {
        $wedding = Wedding::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // VIP only: guestbook_enabled harus diaktifkan
        $allowed = $wedding->isVip() && ($wedding->guestbook_enabled ?? false);
        abort_unless($allowed, 403);
        abort_if($wedding->isArchived(), 410, 'Guestbook ditutup — masa aktif undangan sudah habis.');

        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'message' => 'required|string|max:500',
        ]);

        $entry = Guestbook::create([
            'wedding_id' => $wedding->id,
            'name'       => strip_tags($data['name']),     // M-2: cegah stored XSS
            'message'    => strip_tags($data['message']),  // M-2: cegah stored XSS
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok'    => true,
                'entry' => [
                    'id'         => $entry->id,
                    'name'       => $entry->name,
                    'message'    => $entry->message,
                    'created_at' => $entry->created_at,
                ],
            ]);
        }

        return back()->with('guestbook_success', 'Ucapan berhasil dikirim!');
    }

    /**
     * Fetch guestbook entries for real-time polling (JSON).
     */
    public function fetchGuestbook(Request $request, string $slug): \Illuminate\Http\JsonResponse
    {
        $wedding = Wedding::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $allowed = $wedding->isVip() && ($wedding->guestbook_enabled ?? false);
        abort_unless($allowed, 403);

        $after   = max(0, (int) $request->query('after', 0));
        $entries = $wedding->guestbook()
            ->where('is_approved', true)
            ->when($after > 0, fn ($q) => $q->where('id', '>', $after))
            ->orderByDesc('created_at')
            ->take(50)
            ->get(['id', 'name', 'message', 'created_at']);

        return response()->json($entries);
    }
}
