<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\Guestbook;
use App\Models\Order;
use App\Models\User;
use App\Models\Wedding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: Seeder hanya boleh dijalankan di environment non-production
        if (app()->isProduction()) {
            $this->command->warn('⚠️  DatabaseSeeder diblokir di environment production.');
            return;
        }

        // ─── USERS ───────────────────────────────────────────────────────────
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@demo.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('password'),
                'role'     => 'super_admin',
                'is_active' => true,
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name'     => 'Admin Demo',
                'password' => bcrypt('password'),
                'role'     => 'admin',
                'is_active' => true,
                'added_by' => $superAdmin->id,
            ]
        );

        // ─── WEDDINGS ────────────────────────────────────────────────────────

        // 1. Classic – Basic – Aktif
        $w1 = Wedding::firstOrCreate(['slug' => 'andi-siti'], [
            'bride_name'       => 'Siti',
            'bride_fullname'   => 'Siti Rahayu',
            'bride_father'     => 'Bapak Rahmat',
            'bride_mother'     => 'Ibu Aminah',
            'bride_gender'     => 'female',
            'groom_name'       => 'Andi',
            'groom_fullname'   => 'Andi Prasetyo',
            'groom_father'     => 'Bapak Sutopo',
            'groom_mother'     => 'Ibu Wati',
            'event_date'       => now()->addWeeks(4),
            'akad_date'        => now()->addWeeks(4),
            'akad_time'        => '08:00',
            'akad_location'    => 'Masjid Al-Ikhlas, Jakarta Selatan',
            'reception_date'   => now()->addWeeks(4),
            'reception_time'   => '11:00',
            'reception_location' => 'Gedung Sasana Krida, Jakarta Selatan',
            'location'         => 'Jakarta',
            'map_link'         => 'https://maps.google.com',
            'dresscode'        => 'Formal – Putih & Navy',
            'opening_text'     => 'Dengan memohon rahmat dan ridho Allah SWT, kami mengundang Bapak/Ibu/Saudara/i untuk hadir di pernikahan kami.',
            'is_active'        => true,
            'template'         => 'classic',
            'package'          => 'basic',
            'trial_expires_at' => now()->addDays(30),
            'guestbook_enabled' => false,
        ]);

        // 2. Floral – Premium – Aktif
        $w2 = Wedding::firstOrCreate(['slug' => 'budi-dewi'], [
            'bride_name'       => 'Dewi',
            'bride_fullname'   => 'Dewi Lestari',
            'bride_father'     => 'Bapak Santoso',
            'bride_mother'     => 'Ibu Mulyati',
            'bride_gender'     => 'female',
            'groom_name'       => 'Budi',
            'groom_fullname'   => 'Budi Santoso',
            'groom_father'     => 'Bapak Haryono',
            'groom_mother'     => 'Ibu Sari',
            'event_date'       => now()->addWeeks(6),
            'akad_date'        => now()->addWeeks(6),
            'akad_time'        => '09:00',
            'akad_location'    => 'Masjid Agung, Bandung',
            'reception_date'   => now()->addWeeks(6),
            'reception_time'   => '12:00',
            'reception_location' => 'Hotel Grand Preanger, Bandung',
            'location'         => 'Bandung',
            'map_link'         => 'https://maps.google.com',
            'dresscode'        => 'Semi-Formal – Pink & Gold',
            'opening_text'     => 'Dengan penuh rasa syukur, kami mengundang kehadiran Bapak/Ibu untuk turut berbahagia bersama kami.',
            'is_active'        => true,
            'template'         => 'floral',
            'package'          => 'premium',
            'has_gallery'      => true,
            'trial_expires_at' => now()->addDays(60),
            'guestbook_enabled' => true,
        ]);

        // 3. VIP Royal – VIP – Aktif
        $w3 = Wedding::firstOrCreate(['slug' => 'rizky-maya'], [
            'bride_name'       => 'Maya',
            'bride_fullname'   => 'Maya Sari Putri',
            'bride_father'     => 'Bapak Irwan',
            'bride_mother'     => 'Ibu Lina',
            'bride_gender'     => 'female',
            'groom_name'       => 'Rizky',
            'groom_fullname'   => 'Rizky Firmansyah',
            'groom_father'     => 'Bapak Ridwan',
            'groom_mother'     => 'Ibu Fatimah',
            'event_date'       => now()->addWeeks(8),
            'akad_date'        => now()->addWeeks(8),
            'akad_time'        => '07:30',
            'akad_location'    => 'Masjid Istiqlal, Jakarta',
            'reception_date'   => now()->addWeeks(8),
            'reception_time'   => '13:00',
            'reception_location' => 'Ballroom Hotel Mulia, Jakarta',
            'location'         => 'Jakarta',
            'map_link'         => 'https://maps.google.com',
            'dresscode'        => 'Formal – Royal Blue & Gold',
            'opening_text'     => 'Bismillahirrahmanirrahim. Dengan segala kerendahan hati dan penuh kebahagiaan, kami mengundang Bapak/Ibu.',
            'is_active'        => true,
            'template'         => 'vip-royal',
            'package'          => 'vip',
            'has_gallery'      => true,
            'vip_password'     => bcrypt('1234'),
            'guestbook_enabled' => true,
            'trial_expires_at' => now()->addDays(90),
            'notify_email'     => 'rizky@demo.com',
        ]);

        // 4. Modern – Basic – Aktif
        $w4 = Wedding::firstOrCreate(['slug' => 'fajar-indah'], [
            'bride_name'       => 'Indah',
            'bride_fullname'   => 'Indah Permata',
            'bride_gender'     => 'female',
            'groom_name'       => 'Fajar',
            'groom_fullname'   => 'Fajar Nugroho',
            'event_date'       => now()->addWeeks(3),
            'reception_date'   => now()->addWeeks(3),
            'reception_time'   => '10:00',
            'reception_location' => 'Gedung Serbaguna, Surabaya',
            'location'         => 'Surabaya',
            'map_link'         => 'https://maps.google.com',
            'is_active'        => true,
            'template'         => 'modern',
            'package'          => 'basic',
            'trial_expires_at' => now()->addDays(30),
            'guestbook_enabled' => false,
        ]);

        // 5. Birthday Fun – Basic
        $w5 = Wedding::firstOrCreate(['slug' => 'ulang-tahun-rina'], [
            'bride_name'       => 'Rina Agustina',
            'bride_fullname'   => 'Rina Agustina',
            'bride_gender'     => 'female',
            'groom_name'       => null,
            'event_date'       => now()->addWeeks(2),
            'reception_date'   => now()->addWeeks(2),
            'reception_time'   => '15:00',
            'reception_location' => 'Villa Paradise, Bogor',
            'location'         => 'Bogor',
            'map_link'         => 'https://maps.google.com',
            'opening_text'     => 'Horeee! Rina mau ulang tahun! Yuk dateng dan rayain bareng yaa~',
            'is_active'        => true,
            'template'         => 'birthday-fun',
            'package'          => 'basic',
            'trial_expires_at' => now()->addDays(30),
            'guestbook_enabled' => false,
        ]);

        // 6. Trial – akan kadaluarsa besok
        $w6 = Wedding::firstOrCreate(['slug' => 'demo-trial'], [
            'bride_name'       => 'Cinta',
            'bride_gender'     => 'female',
            'groom_name'       => 'Reza',
            'event_date'       => now()->addDays(10),
            'location'         => 'Yogyakarta',
            'map_link'         => 'https://maps.google.com',
            'is_active'        => true,
            'template'         => 'minimal',
            'package'          => 'trial',
            'trial_expires_at' => now()->addDays(1),
            'guestbook_enabled' => false,
        ]);

        // ─── GUESTS ──────────────────────────────────────────────────────────
        $this->seedGuests($w1, [
            ['Farhan', 'Keluarga', true, 2],
            ['Ibu Siti Rahayu', 'Keluarga', true, 4],
            ['Bapak Ahmad Santoso', 'Keluarga', true, 3],
            ['Rudi Hartono', 'Teman', true, 1],
            ['Dewi Permatasari', 'Teman', true, 2],
            ['Keluarga Bapak Sobari', 'Rekan Kerja', false, null],
            ['Pak Hendra & Keluarga', 'Rekan Kerja', null, null],
            ['Bu Yuni', 'Tetangga', null, null],
        ]);

        $this->seedGuests($w2, [
            ['Keluarga Bapak Sugiarto', 'Keluarga', true, 5],
            ['Ibu Kartini', 'Keluarga', true, 3],
            ['Arif Budiman', 'Teman Kuliah', true, 2],
            ['Maria Susanti', 'Teman Kuliah', true, 1],
            ['Pak Dodi & Ibu', 'Rekan Kerja', true, 2],
            ['Grup Arisan RT 05', 'Tetangga', false, null],
            ['Keluarga Pakde Suryo', 'Keluarga', null, null],
            ['Teman SMA Budi', 'Teman', null, null],
            ['Pak Camat & Ibu', 'Tamu Undangan', null, null],
        ]);

        $this->seedGuests($w3, [
            ['YM. Prof. Dr. Hidayat, M.Sc.', 'VIP', true, 2],
            ['Bapak Gubernur', 'VIP', true, 3],
            ['Keluarga Besar Firmansyah', 'Keluarga', true, 10],
            ['Ibu Direktur Utama', 'Rekan Kerja VIP', true, 2],
            ['Duta Besar', 'VIP', false, null],
            ['Delegasi Perusahaan', 'Rekan Kerja', null, null],
            ['Keluarga Maya', 'Keluarga', null, null],
        ]);

        $this->seedGuests($w4, [
            ['Keluarga Bu Sari', 'Keluarga', true, 4],
            ['Pak Joko & Ibu', 'Tetangga', true, 2],
            ['Teman Sekolah Fajar', 'Teman', null, null],
            ['Rekan Kantor', 'Rekan Kerja', null, null],
        ]);

        $this->seedGuests($w5, [
            ['Sahabat Rina - Grup A', 'Teman', true, 5],
            ['Keluarga Dekat', 'Keluarga', true, 8],
            ['Teman Kerja', 'Rekan Kerja', true, 3],
            ['Best Friend 4ever', 'Teman', null, null],
        ]);

        $this->seedGuests($w6, [
            ['Tamu Coba', 'Teman', null, null],
            ['Keluarga Demo', 'Keluarga', null, null],
        ]);

        // ─── GUESTBOOK ENTRIES ───────────────────────────────────────────────
        $this->seedGuestbook($w2, [
            ['Arif Budiman', 'Semoga langgeng dan bahagia selalu ya! 🎉'],
            ['Maria Susanti', 'Selamat menempuh hidup baru, semoga menjadi keluarga sakinah mawaddah warahmah 💕'],
            ['Pak Dodi', 'Barakallahu lakuma wa baraka alaikuma wa jama\'a bainakuma fi khair. Aamiin!'],
        ]);

        $this->seedGuestbook($w3, [
            ['Prof. Dr. Hidayat', 'Selamat berbahagia, semoga keluarga baru ini diberkahi Allah SWT 🌹'],
            ['Delegasi Perusahaan', 'Congratulations on your special day! Wishing you both a lifetime of happiness.'],
        ]);

        // ─── ORDERS ──────────────────────────────────────────────────────────
        Order::firstOrCreate(['customer_email' => 'hendra@email.com', 'template' => 'classic'], [
            'template'       => 'classic',
            'package'        => 'basic',
            'bride_name'     => 'Lina',
            'groom_name'     => 'Hendra',
            'event_date'     => now()->addWeeks(5),
            'location'       => 'Jakarta',
            'customer_name'  => 'Hendra Wijaya',
            'customer_phone' => '08121234567',
            'customer_email' => 'hendra@email.com',
            'notes'          => 'Mohon dibuatkan sesegera mungkin, terima kasih.',
            'status'         => 'selesai',
            'payment_status' => 'lunas',
            'expires_at'     => now()->addDays(30),
            'wedding_id'     => $w1->id,
        ]);

        Order::firstOrCreate(['customer_email' => 'sari.cantik@email.com', 'template' => 'floral'], [
            'template'       => 'floral',
            'package'        => 'premium',
            'bride_name'     => 'Sari',
            'groom_name'     => 'Dodi',
            'event_date'     => now()->addWeeks(7),
            'location'       => 'Surabaya',
            'customer_name'  => 'Sari Cantik',
            'customer_phone' => '08129876543',
            'customer_email' => 'sari.cantik@email.com',
            'notes'          => 'Tolong tambahkan foto couple kami.',
            'status'         => 'diproses',
            'payment_status' => 'menunggu_konfirmasi',
            'expires_at'     => now()->addDays(7),
        ]);

        Order::firstOrCreate(['customer_email' => 'agus.setiawan@email.com', 'template' => 'vip-royal'], [
            'template'       => 'vip-royal',
            'package'        => 'vip',
            'bride_name'     => 'Fitri',
            'groom_name'     => 'Agus',
            'event_date'     => now()->addWeeks(10),
            'location'       => 'Bali',
            'customer_name'  => 'Agus Setiawan',
            'customer_phone' => '081355667788',
            'customer_email' => 'agus.setiawan@email.com',
            'notes'          => 'Kami ingin template VIP dengan password undangan.',
            'status'         => 'selesai',
            'payment_status' => 'lunas',
            'expires_at'     => now()->addDays(90),
            'wedding_id'     => $w3->id,
        ]);


        $this->command->info('✅ Demo seeding selesai!');
        $this->command->info('');
        $this->command->info('  Super Admin : superadmin@demo.com / password');
        $this->command->info('  Admin       : admin@demo.com / password');
        $this->command->info('');
        $this->command->info('  Wedding slugs yang dibuat:');
        $this->command->info('    /andi-siti         → Classic   (basic)');
        $this->command->info('    /budi-dewi         → Floral    (premium, gallery, guestbook)');
        $this->command->info('    /rizky-maya        → VIP Royal (vip, password: 1234)');
        $this->command->info('    /fajar-indah       → Modern    (basic)');
        $this->command->info('    /ulang-tahun-rina  → Birthday Fun (basic)');
        $this->command->info('    /demo-trial        → Minimal   (trial, expires +1 day)');
    }

    // ─── HELPERS ─────────────────────────────────────────────────────────────

    private function seedGuests(Wedding $wedding, array $guestList): void
    {
        foreach ($guestList as [$name, $group, $attending, $pax]) {
            $slug = Guest::generateSlugName($wedding->id);
            Guest::firstOrCreate(
                ['wedding_id' => $wedding->id, 'guest_name' => $name],
                [
                    'group_name'   => $group,
                    'slug_name'    => $slug,
                    'is_attending' => $attending,
                    'pax'          => $pax,
                    'replied_at'   => $attending !== null ? now()->subHours(rand(1, 72)) : null,
                    'open_count'   => $attending !== null ? rand(1, 5) : 0,
                    'first_opened_at' => $attending !== null ? now()->subDays(rand(1, 7)) : null,
                ]
            );
        }
    }

    private function seedGuestbook(Wedding $wedding, array $entries): void
    {
        foreach ($entries as [$name, $message]) {
            Guestbook::firstOrCreate(
                ['wedding_id' => $wedding->id, 'name' => $name],
                ['message' => $message, 'created_at' => now()->subHours(rand(1, 48))]
            );
        }
    }
}
