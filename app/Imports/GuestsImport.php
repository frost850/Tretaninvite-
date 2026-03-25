<?php

namespace App\Imports;

use App\Models\Guest;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithLimit;

class GuestsImport implements ToModel, WithHeadingRow, WithValidation, WithLimit
{
    public const MAX_ROWS = 2000;

    public function __construct(
        public int $weddingId,
        public string $mode = 'append' // append | replace | skip_duplicates
    ) {}

    public function limit(): int
    {
        return self::MAX_ROWS;
    }

    /**
     * Normalize header: nama_tamu, nama tamu, Nama Tamu, nama, etc.
     */
    private function findName(array $row): ?string
    {
        $keys = ['nama_tamu', 'nama tamu', 'nama', 'guest_name', 'guest', 'tamu', 'name'];
        foreach ($keys as $key) {
            if (isset($row[$key]) && trim((string) $row[$key]) !== '') {
                return trim((string) $row[$key]);
            }
        }
        // Fallback: kolom pertama atau kedua
        $first = $row[0] ?? $row[1] ?? null;
        if ($first !== null && trim((string) $first) !== '' && !is_numeric(trim((string) $first))) {
            return trim((string) $first);
        }
        if (isset($row[1]) && trim((string) $row[1]) !== '') {
            return trim((string) $row[1]);
        }
        return null;
    }

    /**
     * Cari grup/keluarga dari berbagai kemungkinan header.
     */
    private function findGroup(array $row): ?string
    {
        $keys = ['grup', 'group', 'kelompok', 'keluarga', 'group_name', 'grup_tamu', 'kategori'];
        foreach ($keys as $key) {
            if (isset($row[$key]) && trim((string) $row[$key]) !== '') {
                $val = trim((string) $row[$key]);
                return strlen($val) > 100 ? substr($val, 0, 100) : $val;
            }
        }
        return null;
    }

    /**
     * Cari nomor WA dari berbagai kemungkinan header.
     */
    private function findPhone(array $row): ?string
    {
        $keys = ['no_wa', 'no wa', 'no. wa', 'whatsapp', 'wa', 'phone', 'telepon', 'telp', 'hp', 'no_hp', 'no hp', 'no. hp', 'nomor wa', 'nomor hp'];
        foreach ($keys as $key) {
            if (isset($row[$key]) && trim((string) $row[$key]) !== '') {
                $phone = trim((string) $row[$key]);
                // Normalkan: pastikan diawali 0 atau +62
                $phone = preg_replace('/[^0-9+]/', '', $phone);
                if (strlen($phone) >= 8) {
                    return $phone;
                }
            }
        }
        return null;
    }

    public function model(array $row): ?Guest
    {
        $name = $this->findName($row);
        if (empty($name)) {
            return null;
        }

        $name = Guest::sanitizeName($name);
        if (strlen($name) > 255) {
            $name = substr($name, 0, 255);
        }

        if ($this->mode === 'skip_duplicates') {
            $exists = Guest::where('wedding_id', $this->weddingId)
                ->whereRaw('LOWER(guest_name) = ?', [mb_strtolower($name, 'UTF-8')])
                ->exists();
            if ($exists) {
                return null;
            }
        }

        return new Guest([
            'wedding_id' => $this->weddingId,
            'guest_name' => $name,
            'group_name' => $this->findGroup($row),
            'phone'      => $this->findPhone($row),
            'slug_name'  => null, // akan diisi oleh model creating event
        ]);
    }

    public function rules(): array
    {
        return [
            '*.nama_tamu' => ['nullable', 'string', 'max:255'],
            '*.nama' => ['nullable', 'string', 'max:255'],
            '0' => ['nullable', 'string', 'max:255'],
            '1' => ['nullable', 'string', 'max:255'],
        ];
    }
}
