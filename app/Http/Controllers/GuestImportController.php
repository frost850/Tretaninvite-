<?php

namespace App\Http\Controllers;

use App\Exports\GuestsExport;
use App\Imports\GuestsImport;
use App\Models\Guest;
use App\Models\Wedding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuestImportController extends Controller
{
    public function index(Request $request): View
    {
        $weddings = Wedding::orderBy('slug')->get();
        $weddingId = old('wedding_id', $request->query('wedding_id'));

        return view('admin.guests.import', compact('weddings', 'weddingId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'wedding_id' => 'required|exists:weddings,id',
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
            'mode' => 'required|in:append,replace,skip_duplicates',
        ]);

        $weddingId = $request->integer('wedding_id');
        $mode = $request->input('mode', 'append');

        $wedding = Wedding::findOrFail($weddingId);

        // Cek batas tamu untuk paket trial/basic sebelum import
        $limit = $wedding->guestLimit();
        if ($limit !== null && $mode !== 'replace') {
            $currentCount = \App\Models\Guest::where('wedding_id', $weddingId)->count();
            if ($currentCount >= $limit) {
                return back()->withInput()->withErrors(['file' => "Batas {$limit} tamu untuk paket ini sudah tercapai (saat ini {$currentCount} tamu). Upgrade paket untuk import lebih banyak."]);
            }
        }

        $countBefore = Guest::where('wedding_id', $weddingId)->count();
        if ($mode === 'replace') {
            Guest::where('wedding_id', $weddingId)->delete();
        }

        try {
            Excel::import(new GuestsImport($weddingId, $mode), $request->file('file'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Guest import failed', [
                'wedding_id' => $weddingId,
                'error'      => $e->getMessage(),
            ]);
            return back()->withInput()->withErrors(['file' => 'Import gagal: Format file tidak valid atau data tidak sesuai template.']);
        }

        $countAfter = Guest::where('wedding_id', $weddingId)->count();
        $imported = $mode === 'replace' ? $countAfter : ($countAfter - $countBefore);

        $message = "Daftar tamu berhasil diimport. {$imported} tamu ditambahkan. Total tamu undangan ini: {$countAfter}.";

        return redirect()->route('admin.guests.index', ['wedding_id' => $weddingId])
            ->with('success', $message);
    }

    /** Download template Excel untuk import tamu */
    public function downloadTemplate(): BinaryFileResponse
    {
        return Excel::download(new \App\Exports\GuestsTemplateExport(), 'template-daftar-tamu.xlsx');
    }
}
