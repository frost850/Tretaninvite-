<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Wedding;
use App\Services\AuditLogger;
use App\Services\TemplateRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class GreetingController extends Controller
{
    /** Hanya template dengan category = 'greeting' */
    private static function getGreetingTemplates(): array
    {
        return TemplateRegistry::byCategory('greeting');
    }

    /** Halaman create: form isi data kartu ucapan */
    public function createForm(string $template): View|RedirectResponse
    {
        $templates = self::getGreetingTemplates();

        if (!isset($templates[$template])) {
            return redirect()->route('admin.weddings.create')
                ->with('error', 'Template kartu ucapan tidak ditemukan.');
        }

        $orderId = request('order_id');
        $order   = $orderId ? \App\Models\Order::find($orderId) : null;
        $package = $order->package ?? 'basic';

        return view('admin.greetings.create', [
            'template'     => $template,
            'templateInfo' => $templates[$template],
            'orderId'      => $orderId,
            'package'      => $package,
        ]);
    }

    /** Simpan kartu ucapan baru */
    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'slug'         => 'required|string|max:100|unique:weddings,slug|regex:/^[a-z0-9\-]+$/',
            'template'     => 'required|string|in:' . implode(',', array_keys(self::getGreetingTemplates())),
            // Penerima kartu
            'bride_name'   => 'required|string|max:100',
            'bride_age'    => 'nullable|integer|min:1|max:150',
            // Pengirim kartu
            'groom_name'   => 'nullable|string|max:100',
            // Pesan ucapan
            'opening_text' => 'nullable|string|max:2000',
            // Musik
            'music_url'    => 'nullable|url|max:500',
        ], [
            'slug.regex' => 'Slug hanya huruf kecil, angka, dan strip (contoh: ucapan-aisyah).',
        ]);

        $valid['is_active']   = true;
        $valid['has_gallery'] = false;

        // Kartu ucapan tidak memiliki field event
        $valid['event_date']         = null;
        $valid['reception_time']     = null;
        $valid['location']           = null;
        $valid['reception_location'] = null;

        // Ambil paket & masa aktif dari order terkait (jika ada)
        $orderId     = $request->input('order_id');
        $linkedOrder = $orderId ? \App\Models\Order::find($orderId) : null;
        if ($linkedOrder) {
            $valid['package']          = $linkedOrder->package ?? 'basic';
            $valid['trial_expires_at'] = now()->addDays(Wedding::expiryDays($valid['package']));
        }
        if (!isset($valid['trial_expires_at'])) {
            $valid['package']          = $valid['package'] ?? 'basic';
            $valid['trial_expires_at'] = now()->addDays(Wedding::expiryDays($valid['package']));
        }

        $greeting = Wedding::create($valid);

        if ($linkedOrder) {
            $linkedOrder->update(['wedding_id' => $greeting->id]);
        }

        return redirect()->route('admin.greetings.edit', $greeting->id)
            ->with('success', 'Kartu ucapan berhasil dibuat!');
    }

    /** Halaman edit kartu ucapan */
    public function edit(Wedding $greeting): View
    {
        $templates    = TemplateRegistry::all();
        $template     = $greeting->template ?? 'greeting-birthday';
        $templateInfo = $templates[$template] ?? ['label' => 'Greeting Card', 'category' => 'greeting', 'icon' => '💌'];

        return view('admin.greetings.edit', [
            'w'            => $greeting,
            'template'     => $template,
            'templateInfo' => $templateInfo,
        ]);
    }

    /** Update kartu ucapan */
    public function update(Request $request, Wedding $greeting): RedirectResponse
    {
        $valid = $request->validate([
            'bride_name'   => 'required|string|max:100',
            'bride_age'    => 'nullable|integer|min:1|max:150',
            'groom_name'   => 'nullable|string|max:100',
            'opening_text' => 'nullable|string|max:2000',
            'music_url'    => 'nullable|url|max:500',
        ]);

        $greeting->update($valid);

        return redirect()->route('admin.greetings.edit', $greeting->id)
            ->with('success', 'Kartu ucapan berhasil diperbarui.');
    }

    /** Hapus kartu ucapan */
    public function destroy(Wedding $greeting): mixed
    {
        // P7-3: hanya super admin yang boleh hapus undangan
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin yang dapat menghapus undangan.');

        $name = $greeting->bride_name;
        $greeting->delete();

        AuditLogger::log('greeting_trashed', 'wedding', $greeting->id, ['slug' => $greeting->slug]);

        if (request()->wantsJson()) {
            return response()->json(['message' => "Kartu ucapan {$name} dipindahkan ke Recycle Bin."]);
        }

        return redirect()->route('admin.weddings.index')
            ->with('success', 'Kartu ucapan dipindahkan ke Recycle Bin.');
    }

    /* ══════════════════════════════════════════════
       AJAX ENDPOINTS (public, no auth)
    ══════════════════════════════════════════════ */

    private function findGreeting(string $slug): ?Wedding
    {
        return Wedding::where('slug', $slug)
            ->where('is_active', true)
            ->whereRaw("template LIKE 'greeting%'")
            ->first();
    }

    /** GET /{slug}/gc/reactions — ambil jumlah reaksi + reaksi milik user ini */
    public function getReactions(string $slug): JsonResponse
    {
        $greeting = $this->findGreeting($slug);
        if (!$greeting) return response()->json(['error' => 'not found'], 404);

        $key      = "gc_reactions_{$greeting->id}";
        $reactions = Cache::get($key, []);

        // Baca cookie reaksi user ini
        $myCookie   = request()->cookie("gc_my_{$greeting->id}") ?? '[]';
        $myReactions = json_decode($myCookie, true) ?? [];

        return response()->json([
            'reactions'   => $reactions,
            'my_reactions'=> $myReactions,
        ]);
    }

    /** POST /{slug}/gc/react — tambah/hapus satu reaksi */
    public function addReaction(Request $request, string $slug): JsonResponse
    {
        $greeting = $this->findGreeting($slug);
        if (!$greeting) return response()->json(['error' => 'not found'], 404);

        $allowed  = ['❤️', '🎂', '🎉', '🎊', '🥳'];
        $emoji    = $request->input('emoji');
        $action   = $request->input('action', 'add'); // 'add' | 'remove'

        if (!in_array($emoji, $allowed)) {
            return response()->json(['error' => 'invalid emoji'], 422);
        }

        // Cooldown per IP+slug+emoji: max 1 toggle per 2 detik (cegah spam klik cepat)
        $cooldownKey = 'gc_react_cd_' . md5($request->ip() . '|' . $greeting->id . '|' . $emoji);
        if (Cache::has($cooldownKey)) {
            return response()->json(['error' => 'too fast'], 429);
        }
        Cache::put($cooldownKey, 1, now()->addSeconds(2));

        $key      = "gc_reactions_{$greeting->id}";
        $reactions = Cache::get($key, []);
        $myCookie  = $request->cookie("gc_my_{$greeting->id}") ?? '[]';
        $myReactions = json_decode($myCookie, true) ?? [];

        if ($action === 'add') {
            $reactions[$emoji]  = ($reactions[$emoji] ?? 0) + 1;
            if (!in_array($emoji, $myReactions)) $myReactions[] = $emoji;
        } else {
            $reactions[$emoji]  = max(0, ($reactions[$emoji] ?? 1) - 1);
            $myReactions = array_values(array_filter($myReactions, fn($e) => $e !== $emoji));
        }

        Cache::put($key, $reactions, now()->addDays(365));
        $count = $reactions[$emoji] ?? 0;

        return response()->json(['count' => $count, 'my_reactions' => $myReactions])
            ->cookie("gc_my_{$greeting->id}", json_encode($myReactions), 60 * 24 * 365);
    }

    /** POST /{slug}/gc/wish — kirim ucapan balik */
    public function storeWish(Request $request, string $slug): JsonResponse
    {
        $greeting = $this->findGreeting($slug);
        if (!$greeting) return response()->json(['error' => 'not found'], 404);

        $valid = $request->validate([
            'name'    => 'required|string|max:80',
            'message' => 'required|string|max:500',
        ]);

        $wish = Guest::create([
            'wedding_id'  => $greeting->id,
            'guest_name'  => strip_tags($valid['name']),    // BH-3: cegah stored XSS
            'notes'       => strip_tags($valid['message']), // BH-3: cegah stored XSS
            'group_name'  => 'wish',        // penanda: ini ucapan balik (bukan tamu biasa)
            'replied_at'  => now(),
        ]);

        return response()->json([
            'ok'   => true,
            'wish' => [
                'name'    => $wish->guest_name,
                'message' => $wish->notes,
                'time'    => $wish->replied_at->diffForHumans(),
            ],
        ], 201);
    }

    /** GET /{slug}/gc/wishes — ambil daftar ucapan balik */
    public function getWishes(string $slug): JsonResponse
    {
        $greeting = $this->findGreeting($slug);
        if (!$greeting) return response()->json(['error' => 'not found'], 404);

        $wishes = Guest::where('wedding_id', $greeting->id)
            ->where('group_name', 'wish')
            ->orderByDesc('replied_at')
            ->take(30)
            ->get(['guest_name', 'notes', 'replied_at'])
            ->map(fn($w) => [
                'name'    => $w->guest_name,
                'message' => $w->notes,
                'time'    => $w->replied_at ? $w->replied_at->diffForHumans() : '',
            ]);

        return response()->json(['wishes' => $wishes]);
    }

    /** GET /{slug}/gc/gallery — ambil foto galeri (lazy load) */
    public function getGallery(string $slug): JsonResponse
    {
        $greeting = $this->findGreeting($slug);
        if (!$greeting) return response()->json(['photos' => []]);

        $photos = [];
        if ($greeting->has_gallery && $greeting->gallery) {
            $photos = $greeting->gallery
                ->map(fn($g) => ['url' => asset('storage/' . $g->path)])
                ->values()
                ->toArray();
        }

        return response()->json(['photos' => $photos]);
    }
}
