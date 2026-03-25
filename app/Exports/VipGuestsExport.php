<?php

namespace App\Exports;

use App\Models\Wedding;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VipGuestsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    private int $no = 0;

    public function __construct(public Wedding $wedding) {}

    public function query()
    {
        return $this->wedding->guests()->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Tamu', 'Grup/Keluarga', 'Kode Tamu', 'No. HP', 'Email',
            'QR Link', 'Hadir (RSVP)', 'Tgl Konfirmasi', 'Pax',
            'Pertama Buka', 'Jml Buka', 'Check-In Venue', 'Catatan',
        ];
    }

    /** P7-2: Cegah formula injection — prefix nilai yang diawali operator formula */
    private static function esc(?string $v): ?string
    {
        if ($v === null) {
            return null;
        }
        return in_array($v[0] ?? '', ['=', '+', '-', '@', "\t", "\r"]) ? "'" . $v : $v;
    }

    public function map($row): array
    {
        $this->no++;

        $guestUrl = url("/{$this->wedding->slug}?to=" . \Illuminate\Support\Str::slug($row->guest_name));

        return [
            $this->no,
            self::esc($row->guest_name),
            self::esc($row->group_name),
            self::esc($row->slug_name),
            self::esc($row->phone),
            self::esc($row->email),
            $guestUrl,
            $row->is_attending === null ? 'Belum' : ($row->is_attending ? '✔ Hadir' : '✘ Tidak'),
            $row->replied_at?->format('d/m/Y H:i'),
            $row->pax,
            $row->first_opened_at?->format('d/m/Y H:i'),
            $row->open_count ?? 0,
            $row->checked_in_at?->format('d/m/Y H:i') ?? '-',
            self::esc($row->notes),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
