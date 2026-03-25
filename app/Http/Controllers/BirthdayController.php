<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Services\AuditLogger;
use App\Services\TemplateRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BirthdayController extends Controller
{
    /** Hanya template dengan category = 'birthday' */
    private static function getBirthdayTemplates(): array
    {
        return TemplateRegistry::byCategory('birthday');
    }

    /** Halaman create: form isi data birthday */
    public function createForm(string $template): View|RedirectResponse
    {
        $templates = self::getBirthdayTemplates();

        if (!isset($templates[$template])) {
            return redirect()->route('admin.weddings.create')
                ->with('error', 'Template birthday tidak ditemukan.');
        }

        $orderId = request('order_id');
        $order   = $orderId ? \App\Models\Order::find($orderId) : null;
        $package = $order->package ?? 'basic';

        return view('admin.birthdays.create', [
            'template'     => $template,
            'templateInfo' => $templates[$template],
            'orderId'      => $orderId,
            'package'      => $package,
        ]);
    }

    /** Simpan undangan birthday baru */
    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'slug'              => 'required|string|max:100|unique:weddings,slug|regex:/^[a-z0-9\-]+$/',
            'template'          => 'required|string|in:' . implode(',', array_keys(self::getBirthdayTemplates())),
            // Yang berulang tahun
            'bride_name'        => 'required|string|max:100',
            'bride_age'         => 'required|integer|min:1|max:150',
            'bride_gender'      => 'nullable|in:female,male',
            'bride_parent'      => 'nullable|string|max:255',
            'bride_bank'        => 'nullable|string|max:100',
            'bride_norek'       => 'nullable|string|max:100',
            // Kontak
            'bride_wa'          => 'nullable|string|max:30',
            // Detail pesta
            'event_date'        => 'required|date',
            'reception_time'    => 'required|string|max:50',
            'location'          => 'required|string|max:255',
            'reception_location'=> 'nullable|string|max:255',
            'map_link'          => 'nullable|url|max:500',
            'map_embed'         => 'nullable|string|max:2000',
            // Tema
            'dresscode'         => 'nullable|string|max:100',
            'opening_text'      => 'nullable|string|max:1000',
            'music_url'         => 'nullable|url|max:500',
        ], [
            'slug.regex' => 'Slug hanya huruf kecil, angka, dan strip (contoh: birthday-aisyah).',
        ]);

        // Sanitasi map_embed: hanya izinkan <iframe> Google Maps (cegah XSS)
        $valid['map_embed'] = WeddingController::sanitizeMapEmbed($valid['map_embed'] ?? null);

        // Birthday tidak punya mempelai pria
        $valid['groom_name'] = null;
        $valid['is_active']  = true;
        $valid['has_gallery'] = false;

        // Ambil paket & masa aktif dari order terkait (jika ada)
        $orderId = $request->input('order_id');
        $linkedOrder = $orderId ? \App\Models\Order::find($orderId) : null;
        if ($linkedOrder) {
            $valid['package']          = $linkedOrder->package ?? 'basic';
            $valid['trial_expires_at'] = now()->addDays(\App\Models\Wedding::expiryDays($valid['package']));
        }
        if (!isset($valid['trial_expires_at'])) {
            $valid['package']          = $valid['package'] ?? 'basic';
            $valid['trial_expires_at'] = now()->addDays(\App\Models\Wedding::expiryDays($valid['package']));
        }

        $wedding = Wedding::create($valid);

        // Link order ke wedding yang baru dibuat
        if ($linkedOrder) {
            $linkedOrder->update(['wedding_id' => $wedding->id]);
        }

        return redirect()->route('admin.birthdays.edit', $wedding->id)
            ->with('success', 'Undangan birthday berhasil dibuat!');
    }

    /** Halaman edit undangan birthday */
    public function edit(Wedding $birthday): View
    {
        $birthday->load('gallery');
        $templates    = TemplateRegistry::all();
        $template     = $birthday->template ?? 'birthday-fun';
        $templateInfo = $templates[$template] ?? ['label' => 'Birthday', 'category' => 'birthday', 'icon' => '🎂'];

        $order = \App\Models\Order::where('wedding_id', $birthday->id)
            ->latest()->first();

        return view('admin.birthdays.edit', [
            'w'            => $birthday,
            'template'     => $template,
            'templateInfo' => $templateInfo,
            'order'        => $order,
        ]);
    }

    /** Update undangan birthday */
    public function update(Request $request, Wedding $birthday): RedirectResponse
    {
        $valid = $request->validate([
            // Yang berulang tahun
            'bride_name'        => 'required|string|max:100',
            'bride_age'         => 'required|integer|min:1|max:150',
            'bride_gender'      => 'nullable|in:female,male',
            'bride_parent'      => 'nullable|string|max:255',
            'bride_bank'        => 'nullable|string|max:100',
            'bride_norek'       => 'nullable|string|max:100',
            // Kontak
            'bride_wa'          => 'nullable|string|max:30',
            // Detail pesta
            'event_date'        => 'required|date',
            'reception_time'    => 'required|string|max:50',
            'location'          => 'required|string|max:255',
            'reception_location'=> 'nullable|string|max:255',
            'map_link'          => 'nullable|url|max:500',
            'map_embed'         => 'nullable|string|max:2000',
            // Tema
            'dresscode'         => 'nullable|string|max:100',
            'opening_text'      => 'nullable|string|max:1000',
            'music_url'         => 'nullable|url|max:500',
            'has_gallery'       => 'nullable|boolean',
        ]);

        $valid['map_embed']   = WeddingController::sanitizeMapEmbed($valid['map_embed'] ?? null);
        $valid['has_gallery'] = $request->has('has_gallery');
        $birthday->update($valid);

        return redirect()->route('admin.birthdays.edit', $birthday->id)
            ->with('success', 'Undangan birthday berhasil diperbarui.');
    }

    /** Hapus undangan birthday */
    public function destroy(Wedding $birthday): mixed
    {
        // P7-3: hanya super admin yang boleh hapus undangan
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin yang dapat menghapus undangan.');

        $name = $birthday->bride_name;
        $birthday->delete();

        AuditLogger::log('birthday_trashed', 'wedding', $birthday->id, ['slug' => $birthday->slug]);

        if (request()->wantsJson()) {
            return response()->json(['message' => "Undangan birthday {$name} dipindahkan ke Recycle Bin."]);
        }

        return redirect()->route('admin.weddings.index')
            ->with('success', 'Undangan birthday dipindahkan ke Recycle Bin.');
    }
}
