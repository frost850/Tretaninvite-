<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use App\Services\TemplateRegistry;
use Illuminate\View\View;

/**
 * Menangani halaman preview template — publik, tanpa autentikasi.
 *
 * Route: GET /preview/{template}  →  PreviewController::show()
 *
 * Sebelumnya logika ini berada di WeddingController::preview() bersama
 * semua metode CRUD pernikahan.  Controller ini memisahkannya agar
 * WeddingController hanya berisi operasi CRUD pernikahan.
 */
class PreviewController extends Controller
{
    /** Tampilkan preview template dengan data contoh. */
    public function show(string $template): View
    {
        $templates = TemplateRegistry::all();

        if (!isset($templates[$template])) {
            abort(404);
        }

        $info           = $templates[$template];
        $category       = $info['category'] ?? 'wedding';
        $isBirthday     = $category === 'birthday';
        $isGreeting     = $category === 'greeting';
        $isVipOnly      = $info['vip_only'] ?? false;

        $wedding    = $this->demoWedding($template, $isBirthday, $isGreeting);
        $demoPhotos = $isBirthday ? $this->demoBirthdayPhotos() : $this->demoPhotos();

        $viewData = [
            'wedding'    => $wedding,
            'guest'      => null,
            'rsvps'      => collect(),
            'isPreview'  => true,
            'demoPhotos' => $demoPhotos,
        ];

        // Template VIP sudah self-contained — tidak perlu data demo generik
        if (!$isVipOnly) {
            $viewData['demo'] = match ($category) {
                'birthday'   => $this->demoBirthdayData(),
                'greeting'   => $this->demoGreetingData(),
                default      => $this->demoWeddingData(),
            };
        }

        return view(TemplateRegistry::viewPath($template), $viewData);
    }

    /* ══════════════════════════════════════════════════════════════════════
       Demo data helpers — hanya dipakai untuk preview, bukan produksi
    ══════════════════════════════════════════════════════════════════════ */

    /** Buat model Wedding palsu untuk keperluan preview. */
    private function demoWedding(string $template, bool $isBirthday, bool $isGreeting): Wedding
    {
        if ($isGreeting) {
            $w = new Wedding([
                'slug'         => 'demo-preview-greeting',
                'bride_name'   => 'Aisyah Azzahra',
                'bride_age'    => 25,
                'groom_name'   => 'Keluarga Besar Ahmad',
                'opening_text' => "Selamat ulang tahun yang ke-25! 🎂\n\nSemoga hari istimewamu dipenuhi senyum dan tawa. Terima kasih telah menjadi bagian indah dalam hidup kami. Semoga selalu diberikan kesehatan, kebahagiaan, dan keberkahan di setiap langkah hidupmu.\n\nWith love 💖",
                'template'     => $template,
                'has_gallery'  => false,
            ]);
            $w->id = 1;
            return $w;
        }

        if ($isBirthday) {
            $w = new Wedding([
                'slug'               => 'demo-preview-birthday',
                'bride_name'         => 'Aisyah Azzahra',
                'bride_age'          => 7,
                'bride_parent'       => 'Bapak Andi & Ibu Siti',
                'groom_name'         => null,
                'event_date'         => now()->addMonths(1)->startOfMonth()->addDays(20),
                'reception_time'     => '14:00',
                'location'           => 'Taman Impian Jaya Ancol, Jakarta Utara',
                'map_link'           => 'https://maps.google.com/?q=Ancol+Jakarta',
                'map_embed'          => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.8977771869817!2d106.83796431471785!3d-6.127384095542957!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6a1dda2f5f8c1d%3A0x3e8e3f9d6a5b5c7a!2sAncol!5e0!3m2!1sen!2sid!4v1234567890" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                'dresscode'          => 'Casual & Colorful',
                'template'           => $template,
            ]);
            $w->id = 1;
            return $w;
        }

        // Wedding default
        $w = new Wedding([
            'slug'       => 'demo-preview',
            'bride_name' => 'Siti Nurhaliza',
            'groom_name' => 'Ahmad Dhani',
            'event_date' => now()->addMonths(2)->startOfMonth()->addDays(14),
            'location'   => 'Ballroom Grand Hyatt Jakarta, Jl. M.H. Thamrin No. 1',
            'map_link'   => 'https://maps.google.com/?q=Grand+Hyatt+Jakarta',
            'template'   => $template,
        ]);
        $w->id = 1;
        return $w;
    }

    /** Data generik untuk preview undangan pernikahan. */
    private function demoWeddingData(): array
    {
        return [
            'quote'           => 'Bismillahirrahmanirrahim',
            'invitation_text' => 'Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk hadir di acara pernikahan kami.',
            'events'          => [
                ['name' => 'Akad Nikah', 'time' => '08:00 WIB', 'place' => 'Kediaman mempelai wanita'],
                ['name' => 'Resepsi',    'time' => '11:00 WIB', 'place' => 'Ballroom Grand Hyatt Jakarta'],
            ],
        ];
    }

    /** Data generik untuk preview undangan birthday. */
    private function demoBirthdayData(): array
    {
        return [
            'quote'         => 'Happy Birthday! 🎂🎉',
            'invitation_text' => 'Mari rayakan hari istimewa ini bersama kami! Kehadiran Anda akan membuat pesta semakin meriah dan penuh kebahagiaan.',
            'party_theme'   => 'Princess & Unicorn Party',
            'special_notes' => 'Harap konfirmasi kehadiran paling lambat 3 hari sebelum acara. Terima kasih! 💕',
        ];
    }

    /** Data generik untuk preview greeting card. */
    private function demoGreetingData(): array
    {
        return [
            'quote'   => 'Happy Birthday! 🎂🎉',
            'message' => 'Semoga harimu selalu dipenuhi senyum, tawa, dan kebahagiaan.',
        ];
    }

    /** Foto-foto demo untuk galeri undangan pernikahan (SVG lokal). */
    private function demoPhotos(): array
    {
        return [
            asset('demo/w1.svg'),
            asset('demo/w2.svg'),
            asset('demo/w3.svg'),
            asset('demo/w4.svg'),
            asset('demo/w5.svg'),
            asset('demo/w6.svg'),
        ];
    }

    /** Foto-foto demo untuk galeri undangan birthday (SVG lokal). */
    private function demoBirthdayPhotos(): array
    {
        return [
            asset('demo/bday1.svg'),
            asset('demo/bday2.svg'),
            asset('demo/bday3.svg'),
            asset('demo/bday4.svg'),
            asset('demo/bday5.svg'),
            asset('demo/bday6.svg'),
        ];
    }
}
