<?php

namespace App\Services;

/**
 * Sumber kebenaran tunggal untuk semua template undangan, birthday, & greeting.
 *
 * Sebelumnya definisi ini tersebar di WeddingController::getTemplateOptions().
 * Sekarang dipusatkan di sini agar setiap controller kategori bisa mengakses
 * tanpa saling bergantung satu sama lain.
 *
 * Penggunaan:
 *   TemplateRegistry::all()               → semua template (array)
 *   TemplateRegistry::byCategory('wedding')   → hanya wedding
 *   TemplateRegistry::find('classic')     → satu template, atau null
 *   TemplateRegistry::viewPath('classic') → 'invitation.templates.wedding.classic'
 */
class TemplateRegistry
{
    /**
     * Semua template yang tersedia, dikelompokkan menurut key (slug template).
     *
     * Setiap entry memiliki:
     *   label, description, color, preview (Tailwind classes), preview_image,
     *   category (wedding|birthday|greeting), icon, vip_only? (bool)
     */
    public static function all(): array
    {
        return [
            // ─── Wedding ─────────────────────────────────────────────────────
            'vip-royal' => [
                'label'         => 'VIP Royal',
                'description'   => 'Sinematik emas & navy, semua fitur VIP — eksklusif',
                'color'         => 'yellow',
                'preview'       => 'from-yellow-950 to-indigo-950 border-yellow-700',
                'preview_image' => 'images/templates/vip-royal.svg',
                'category'      => 'wedding',
                'icon'          => '♛',
                'vip_only'      => true,
            ],
            'vip-patisserie' => [
                'label'         => 'VIP Patisserie',
                'description'   => 'Pastel floral & patisserie, semua fitur VIP — eksklusif',
                'color'         => 'pink',
                'preview'       => 'from-pink-100 to-purple-100 border-pink-300',
                'preview_image' => 'images/templates/vip-patisserie.svg',
                'category'      => 'wedding',
                'icon'          => '🌸',
                'vip_only'      => true,
            ],
            'premium-patisserie' => [
                'label'         => 'Premium Patisserie',
                'description'   => 'Pastel floral & patisserie, fitur premium lengkap',
                'color'         => 'pink',
                'preview'       => 'from-pink-50 to-purple-50 border-pink-200',
                'preview_image' => 'images/templates/premium-patisserie.svg',
                'category'      => 'wedding',
                'icon'          => '🌷',
            ],
            'basic-patisserie' => [
                'label'         => 'Basic Patisserie',
                'description'   => 'Pastel floral sederhana, fitur esensial',
                'color'         => 'pink',
                'preview'       => 'from-pink-50 to-white border-pink-100',
                'preview_image' => 'images/templates/basic-patisserie.svg',
                'category'      => 'wedding',
                'icon'          => '✿',
            ],
            'classic' => [
                'label'         => 'Classic',
                'description'   => 'Emas & serif, nuansa tradisional elegan',
                'color'         => 'amber',
                'preview'       => 'from-amber-50 to-stone-100 border-amber-200',
                'preview_image' => 'images/templates/classic.svg',
                'category'      => 'wedding',
                'icon'          => '💒',
            ],
            'minimal' => [
                'label'         => 'Minimal',
                'description'   => 'Putih bersih, typography modern',
                'color'         => 'stone',
                'preview'       => 'from-stone-50 to-white border-stone-200',
                'preview_image' => 'images/templates/minimal.svg',
                'category'      => 'wedding',
                'icon'          => '✦',
            ],
            'floral' => [
                'label'         => 'Floral',
                'description'   => 'Pink & bunga, romantis',
                'color'         => 'pink',
                'preview'       => 'from-pink-50 to-pink-100 border-pink-200',
                'preview_image' => 'images/templates/floral.svg',
                'category'      => 'wedding',
                'icon'          => '🌸',
            ],
            'garden' => [
                'label'         => 'Garden',
                'description'   => 'Hijau alami, segar',
                'color'         => 'emerald',
                'preview'       => 'from-emerald-50 to-green-100 border-emerald-200',
                'preview_image' => 'images/templates/garden.svg',
                'category'      => 'wedding',
                'icon'          => '🌿',
            ],
            'modern' => [
                'label'         => 'Modern',
                'description'   => 'Gelap & elegan',
                'color'         => 'slate',
                'preview'       => 'from-slate-800 to-slate-900 border-slate-600',
                'preview_image' => 'images/templates/modern.svg',
                'category'      => 'wedding',
                'icon'          => '◆',
            ],
            'luxury' => [
                'label'         => 'Luxury 3D',
                'description'   => 'Emas-gelap mewah, efek 3D & bintang',
                'color'         => 'yellow',
                'preview'       => 'from-yellow-950 to-stone-950 border-yellow-800',
                'preview_image' => 'images/templates/luxury.svg',
                'category'      => 'wedding',
                'icon'          => '◈',
            ],
            'cerah' => [
                'label'         => 'Cerah 3D',
                'description'   => 'Colorful & ceria, efek 3D bubbly',
                'color'         => 'pink',
                'preview'       => 'from-pink-100 to-sky-100 border-pink-300',
                'preview_image' => 'images/templates/cerah.svg',
                'category'      => 'wedding',
                'icon'          => '🌸',
            ],
            'dark-romance' => [
                'label'         => 'Dark Romance',
                'description'   => 'Hitam-merah dramatis, nuansa gelap romantis',
                'color'         => 'red',
                'preview'       => 'from-red-950 to-stone-950 border-red-900',
                'preview_image' => 'images/templates/dark-romance.svg',
                'category'      => 'wedding',
                'icon'          => '🌹',
            ],

            // ─── Birthday ────────────────────────────────────────────────────
            'birthday-fun' => [
                'label'         => 'Birthday Fun',
                'description'   => 'Colorful & interaktif, pesta ulang tahun ceria',
                'color'         => 'pink',
                'preview'       => 'from-pink-100 to-yellow-100 border-pink-300',
                'preview_image' => 'images/templates/birthday-fun.svg',
                'category'      => 'birthday',
                'icon'          => '🎂',
            ],
            'birthday-patisserie' => [
                'label'         => 'Birthday Patisserie',
                'description'   => 'Elegant tea party, pastel colors & 3D effects',
                'color'         => 'rose',
                'preview'       => 'from-rose-100 to-purple-100 border-rose-200',
                'preview_image' => 'images/templates/birthday-patisserie.svg',
                'category'      => 'birthday',
                'icon'          => '🍰',
            ],
            'birthday-gatsby' => [
                'label'         => 'Birthday Gatsby',
                'description'   => 'Art Deco & Gatsby hitam-emas, mewah dan dramatis',
                'color'         => 'yellow',
                'preview'       => 'from-stone-900 to-black border-yellow-700',
                'preview_image' => 'images/templates/birthday-gatsby.svg',
                'category'      => 'birthday',
                'icon'          => '🥂',
            ],

            // ─── Greeting Card ───────────────────────────────────────────────
            'greeting-birthday' => [
                'label'         => 'Greeting Card Birthday',
                'description'   => 'Kartu ucapan ulang tahun digital, personal & penuh kasih',
                'color'         => 'violet',
                'preview'       => 'from-violet-100 to-pink-100 border-violet-300',
                'preview_image' => 'images/templates/greeting-birthday.svg',
                'category'      => 'greeting',
                'icon'          => '💌',
            ],
            // ─── Anniversary ─────────────────────────────────────────────────
            'anniversary-classic' => [
                'label'         => 'Anniversary Classic',
                'description'   => 'Elegan & romantis — perayaan hari jadi pernikahan penuh kenangan',
                'color'         => 'rose',
                'preview'       => 'from-rose-50 to-pink-100 border-rose-300',
                'preview_image' => 'images/templates/anniversary-classic.svg',
                'category'      => 'anniversary',
                'icon'          => '�',
            ],
            'anniversary-golden' => [
                'label'         => 'Anniversary Golden',
                'description'   => 'Kemewahan emas — untuk perayaan pernikahan emas & perak yang berkilau',
                'color'         => 'yellow',
                'preview'       => 'from-yellow-50 to-amber-100 border-yellow-300',
                'preview_image' => 'images/templates/anniversary-golden.svg',
                'category'      => 'anniversary',
                'icon'          => '💕',
            ],
        ];
    }

    /** Semua template untuk satu kategori. */
    public static function byCategory(string $category): array
    {
        return array_filter(
            static::all(),
            fn($info) => ($info['category'] ?? '') === $category
        );
    }

    /** Satu template berdasarkan key, atau null jika tidak ditemukan. */
    public static function find(string $template): ?array
    {
        return static::all()[$template] ?? null;
    }

    /**
     * Nama Blade view untuk template tertentu.
     * Contoh: 'classic' → 'invitation.templates.wedding.classic'
     */
    public static function viewPath(string $template): string
    {
        $category = static::all()[$template]['category'] ?? 'wedding';

        return 'invitation.templates.' . $category . '.' . $template;
    }
}
