<?php

namespace App\Http\Controllers;

use App\Services\TemplateRegistry;
use Illuminate\View\View;

class PackageController extends Controller
{
    /**
     * Halaman untuk pelanggan: lihat paket (template) yang tersedia.
     * Pelanggan hanya memilih paket; yang buat undangan & import tamu adalah admin.
     */
    public function index(): View
    {
        $packages = TemplateRegistry::all();

        return view('packages.index', compact('packages'));
    }
}
