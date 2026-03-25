<?php

namespace App\Http\Controllers;

use App\Exports\GuestsExport;
use App\Models\Guest;
use App\Models\Wedding;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GuestController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $weddingId = $request->query('wedding_id');
        if (!$weddingId) {
            return redirect()->route('admin.weddings.index')->with('error', 'Pilih undangan terlebih dahulu.');
        }

        $wedding = Wedding::findOrFail($weddingId);
        $guests = $wedding->guests()->orderBy('guest_name')->paginate(25);

        return view('admin.guests.index', compact('wedding', 'guests'));
    }

    public function create(Request $request): View
    {
        $wedding = Wedding::findOrFail($request->query('wedding_id'));

        return view('admin.guests.create', compact('wedding'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'wedding_id' => 'required|exists:weddings,id',
            'guest_name' => 'required|string|max:255',
            'group_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $wedding = Wedding::findOrFail($request->integer('wedding_id'));

        // Cek batas tamu untuk paket trial
        $limit = $wedding->guestLimit();
        if ($limit !== null && $wedding->guests()->count() >= $limit) {
            return back()->withInput()->withErrors(['guest_name' => "Batas {$limit} tamu untuk paket percobaan/basic sudah tercapai. Upgrade paket untuk menambah lebih banyak tamu."]);
        }

        $guest = new Guest([
            'wedding_id' => $request->integer('wedding_id'),
            'guest_name' => Guest::sanitizeName($request->guest_name),
            'group_name' => $request->group_name ?: null,
            'phone' => $request->phone ?: null,
            'email' => $request->email ?: null,
            'notes' => $request->notes ?: null,
        ]);
        $guest->save();

        return redirect()->route('admin.guests.index', ['wedding_id' => $request->integer('wedding_id')])
            ->with('success', 'Tamu berhasil ditambahkan.');
    }

    public function edit(Guest $guest): View
    {
        $guest->load('wedding');

        return view('admin.guests.edit', compact('guest'));
    }

    public function update(Request $request, Guest $guest): RedirectResponse
    {
        $request->validate([
            'guest_name' => 'required|string|max:255',
            'group_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $guest->guest_name = Guest::sanitizeName($request->guest_name);
        $guest->group_name = $request->group_name ?: null;
        $guest->phone = $request->phone ?: null;
        $guest->email = $request->email ?: null;
        $guest->notes = $request->notes ?: null;
        $guest->save();

        return redirect()->route('admin.guests.index', ['wedding_id' => $guest->wedding_id])
            ->with('success', 'Data tamu berhasil diperbarui.');
    }

    public function destroy(Guest $guest): mixed
    {
        $weddingId = $guest->wedding_id;
        $name = $guest->name;
        $guest->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => "Tamu {$name} berhasil dihapus."]);
        }

        return redirect()->route('admin.guests.index', ['wedding_id' => $weddingId])
            ->with('success', 'Tamu berhasil dihapus.');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $wedding = Wedding::findOrFail($request->query('wedding_id'));
        $filename = 'daftar-tamu-' . $wedding->slug . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new GuestsExport($wedding), $filename);
    }
}
