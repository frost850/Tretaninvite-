<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Services\TemplateRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrialController extends Controller
{
    /** Masa aktif trial (hari) */
    private const TRIAL_DAYS = 1;

    /** Maksimal trial aktif per IP dalam 24 jam */
    private const MAX_TRIALS_PER_IP = 1;

    /** Form coba gratis — pelanggan isi sendiri */
    public function create(string $template): View|RedirectResponse
    {
        $templates = TemplateRegistry::all();

        if (!isset($templates[$template])) {
            return redirect()->route('packages.index')->with('error', 'Template tidak ditemukan.');
        }

        $templateInfo = $templates[$template];
        $category     = $templateInfo['category'] ?? 'wedding';

        // greeting tidak tersedia untuk trial
        if ($category === 'greeting') {
            return redirect()->route('packages.index')->with('error', 'Template ini tidak tersedia untuk percobaan gratis.');
        }

        $view = match ($category) {
            'birthday'   => 'trial.create-birthday',
            default      => 'trial.create-wedding',
        };

        return view($view, compact('template', 'templateInfo', 'category'));
    }

    /** Simpan undangan coba gratis */
    public function store(Request $request): RedirectResponse
    {
        // === HONEYPOT: bot isi field ini, manusia tidak ===
        if ($request->filled('website')) {
            // Diam-diam tolak, seolah sukses
            return redirect()->route('packages.index');
        }

        $templates    = TemplateRegistry::all();
        $template     = $request->input('template');
        $templateInfo = $templates[$template] ?? null;

        if (!$templateInfo) {
            return redirect()->route('packages.index')->with('error', 'Template tidak valid.');
        }

        // === CEK LIMIT PER IP ===
        $ip            = $request->ip();
        $bypassIps     = config('admin.trial_bypass_ips', []);
        $isBypassIp    = in_array($ip, $bypassIps, true);

        if (!$isBypassIp) {
            $activeTrialFromIp = Wedding::where('package', 'trial')
                ->where('creator_ip', $ip)
                ->where('trial_expires_at', '>', now())
                ->exists();

            if ($activeTrialFromIp) {
                return redirect()->route('packages.index')
                    ->with('error', '⚠️ Satu undangan percobaan aktif sudah dibuat dari perangkat ini. Silakan tunggu hingga masa percobaan habis atau upgrade ke paket berbayar.');
            }
        }

        $category = $templateInfo['category'] ?? 'wedding';

        $isBirthday   = $category === 'birthday';

        // Validasi
        $rules = [
            'template'       => 'required|string|in:' . implode(',', array_keys($templates)),
            'bride_name'     => 'required|string|max:100',
            'event_date'     => 'required|date',
            'reception_time' => 'required|string|max:50',
            'location'       => 'required|string|max:255',
            'website'        => 'max:0', // honeypot: harus kosong
        ];

        if ($isBirthday) {
            $rules['bride_age'] = 'nullable|integer|min:1|max:150';
        } else {
            $rules['groom_name'] = 'required|string|max:100';
        }

        $messages = [
            'bride_name.required'     => $isBirthday ? 'Nama yang berulang tahun wajib diisi.' : 'Nama mempelai wanita wajib diisi.',
            'groom_name.required'     => 'Nama mempelai pria wajib diisi.',
            'event_date.required'     => 'Tanggal acara wajib diisi.',
            'reception_time.required' => 'Jam acara wajib diisi.',
            'location.required'       => 'Lokasi acara wajib diisi.',
            'website.max'             => 'Validasi gagal.',
        ];

        $valid = $request->validate($rules, $messages);

        // Hapus honeypot dari data valid
        unset($valid['website']);

        // Auto-generate slug unik
        $base = match (true) {
            $isBirthday   => Str::slug($valid['bride_name'] . '-ultah'),
            default       => Str::slug($valid['bride_name'] . '-' . $valid['groom_name']),
        };

        $slug = $base;
        $attempt = 0;
        while (Wedding::where('slug', $slug)->exists()) {
            $attempt++;
            $slug = $base . '-' . $attempt;
        }

        // Create wedding record
        $data = array_merge($valid, [
            'slug'             => $slug,
            'groom_name'       => $isBirthday ? null : $valid['groom_name'],
            'is_active'        => true,
            'has_gallery'      => false,
            'package'          => 'trial',
            'trial_expires_at' => now()->addDays(self::TRIAL_DAYS),
            'creator_ip'       => $ip,
        ]);

        // Create wedding record inside a transaction to prevent race conditions (M-1)
        try {
            $wedding = DB::transaction(function () use ($data, $ip, $slug, $base, $isBypassIp) {
                // Re-check limit inside the transaction (prevents TOCTOU race condition)
                if (!$isBypassIp) {
                    $alreadyExists = Wedding::where('package', 'trial')
                        ->where('creator_ip', $ip)
                        ->where('trial_expires_at', '>', now())
                        ->lockForUpdate()
                        ->exists();

                    if ($alreadyExists) {
                        throw new \RuntimeException('trial_limit_exceeded');
                    }
                }

                return Wedding::create($data);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'trial_limit_exceeded') {
                return redirect()->route('packages.index')
                    ->with('error', '⚠️ Satu undangan percobaan aktif sudah dibuat dari perangkat ini. Silakan tunggu hingga masa percobaan habis atau upgrade ke paket berbayar.');
            }
            throw $e;
        }

        // Tandai pemilik di session agar bisa mengelola tamu
        session(['trial_manage:' . $wedding->slug => $wedding->id]);

        return redirect()->route('trial.success', $wedding->slug);
    }
    public function success(string $slug): View|RedirectResponse
    {
        $wedding = Wedding::where('slug', $slug)->where('package', 'trial')->firstOrFail();

        return view('trial.success', compact('wedding'));
    }
}
