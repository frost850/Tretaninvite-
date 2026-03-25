<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GuestsTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Budi Santoso',   'Keluarga Budi', '08123456789'],
            ['Ibu Siti',       'Rekan Kerja',   '08234567890'],
            ['Keluarga Ahmad', '',               ''],
        ];
    }

    public function headings(): array
    {
        return ['Nama_Tamu', 'Grup', 'No_WA'];
    }
}
