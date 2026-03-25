<?php

namespace App\Exports;

use App\Models\Wedding;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class GuestsExport implements FromQuery, WithHeadings, WithMapping
{
    private int $no = 0;

    public function __construct(
        public Wedding $wedding
    ) {}

    public function query()
    {
        return $this->wedding->guests()->orderBy('id');
    }

    public function headings(): array
    {
        return ['No', 'Nama Tamu', 'Grup/Keluarga', 'Kode (slug)', 'No. HP', 'Email', 'Hadir (RSVP)', 'Tanggal Konfirmasi', 'Pax', 'Catatan'];
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
        return [
            $this->no,
            self::esc($row->guest_name),
            self::esc($row->group_name),
            self::esc($row->slug_name),
            self::esc($row->phone),
            self::esc($row->email),
            $row->is_attending === null ? '-' : ($row->is_attending ? 'Hadir' : 'Tidak'),
            $row->replied_at?->format('d/m/Y H:i'),
            $row->pax,
            self::esc($row->notes),
        ];
    }
}
