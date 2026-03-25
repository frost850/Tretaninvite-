<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Services\AuditLogger;
use App\Services\TemplateRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnniversaryController extends Controller
{
    /** Hanya template dengan category = 'anniversary' */
    private static function getAnniversaryTemplates(): array
    {
        return TemplateRegistry::byCategory('anniversary');
    }

    /** Halaman create: coming soon — template belum tersedia */
    public function createForm(string $template): View|RedirectResponse
    {
        return redirect()->route('admin.weddings.create')
            ->with('info', '� Undangan Anniversary segera hadir! Template ini masih dalam pengerjaan.');
    }

    /** Simpan undangan anniversary baru */
    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'slug'               => 'required|string|max:100|unique:weddings,slug|regex:/^[a-z0-9\-]+$/',
            'template'           => 'required|string|in:' . implode(',', array_keys(self::getAnniversaryTemplates())),
            // Pasangan
            'bride_name'         => 'required|string|max:100',
            'groom_name'         => 'required|string|max:100',
            'bride_age'          => 'nullable|integer|min:1|max:100', // tahun anniversary
            // Detail perayaan
            'event_date'         => 'required|date',
            'reception_time'     => 'required|string|max:80',
            'location'           => 'required|string|max:255',
            'reception_location' => 'nullable|string|max:255',
            'map_link'           => 'nullable|url|max:500',
            'map_embed'          => 'nullable|string|max:2000',
            // Tambahan
            'opening_text'       => 'nullable|string|max:1500',
            'music_url'          => 'nullable|url|max:500',
            'dresscode'          => 'nullable|string|max:100',
        ], [
            'slug.regex' => 'Slug hanya huruf kecil, angka, dan strip (contoh: anniversary-budi-ani-25th).',
            'bride_name.required' => 'Nama pasangan pertama wajib diisi.',
            'groom_name.required' => 'Nama pasangan kedua wajib diisi.',
        ]);

        // Sanitasi map_embed: hanya izinkan <iframe> Google Maps (cegah XSS)
        $valid['map_embed'] = WeddingController::sanitizeMapEmbed($valid['map_embed'] ?? null);

        $valid['is_active']  = true;
        $valid['has_gallery'] = false;

        // Ambil paket & masa aktif dari order terkait (jika ada)
        $orderId     = $request->input('order_id');
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

        AuditLogger::log('anniversary_created', 'wedding', $wedding->id, ['slug' => $wedding->slug]);

        return redirect()->route('admin.anniversaries.edit', $wedding->id)
            ->with('success', 'Undangan anniversary berhasil dibuat!');
    }

    /** Halaman edit undangan anniversary */
    public function edit(Wedding $anniversary): View
    {
        $anniversary->load('gallery');
        $templates    = TemplateRegistry::all();
        $template     = $anniversary->template ?? 'anniversary-classic';
        $templateInfo = $templates[$template] ?? ['label' => 'Anniversary', 'category' => 'anniversary', 'icon' => '�'];

        $order = \App\Models\Order::where('wedding_id', $anniversary->id)
            ->latest()->first();

        return view('admin.anniversaries.edit', [
            'w'            => $anniversary,
            'template'     => $template,
            'templateInfo' => $templateInfo,
            'order'        => $order,
        ]);
    }

    /** Update undangan anniversary */
    public function update(Request $request, Wedding $anniversary): RedirectResponse
    {
        $valid = $request->validate([
            'bride_name'         => 'required|string|max:100',
            'groom_name'         => 'required|string|max:100',
            'bride_age'          => 'nullable|integer|min:1|max:100',
            'event_date'         => 'required|date',
            'reception_time'     => 'required|string|max:80',
            'location'           => 'required|string|max:255',
            'reception_location' => 'nullable|string|max:255',
            'map_link'           => 'nullable|url|max:500',
            'map_embed'          => 'nullable|string|max:2000',
            'opening_text'       => 'nullable|string|max:1500',
            'music_url'          => 'nullable|url|max:500',
            'dresscode'          => 'nullable|string|max:100',
            'has_gallery'        => 'nullable|boolean',
        ]);

        $valid['map_embed']   = WeddingController::sanitizeMapEmbed($valid['map_embed'] ?? null);
        $valid['has_gallery'] = $request->has('has_gallery');

        $anniversary->update($valid);

        AuditLogger::log('anniversary_updated', 'wedding', $anniversary->id, ['slug' => $anniversary->slug]);

        return redirect()->route('admin.anniversaries.edit', $anniversary->id)
            ->with('success', 'Undangan anniversary berhasil diperbarui.');
    }

    /** Hapus undangan anniversary */
    public function destroy(Wedding $anniversary): mixed
    {
        abort_unless(session('admin_is_super'), 403, 'Hanya Super Admin yang dapat menghapus undangan.');

        $name = $anniversary->bride_name . ' & ' . $anniversary->groom_name;
        $anniversary->delete();

        AuditLogger::log('anniversary_trashed', 'wedding', $anniversary->id, ['slug' => $anniversary->slug]);

        if (request()->wantsJson()) {
            return response()->json(['message' => "Undangan anniversary {$name} dipindahkan ke Recycle Bin."]);
        }

        return redirect()->route('admin.weddings.index')
            ->with('success', 'Undangan anniversary dipindahkan ke Recycle Bin.');
    }
}
