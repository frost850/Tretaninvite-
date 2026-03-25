<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Order;
use App\Models\Wedding;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today      = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $lastMonthStart = now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd   = now()->subMonth()->endOfMonth()->toDateString();

        // ── Statistik Utama ──────────────────────────────────────────
        $ordersToday     = Order::whereDate('created_at', $today)->count();
        $ordersMonth     = Order::whereDate('created_at', '>=', $monthStart)->count();
        $pendingConfirm  = Order::where('payment_status', 'menunggu_konfirmasi')->count();
        $pendingNew      = Order::where('status', 'baru')->count();
        $rejectedCount   = Order::where('payment_status', 'ditolak')->count();
        $stuckCount      = Order::where('payment_status', 'belum_bayar')
                               ->whereNull('payment_proof')
                               ->where('created_at', '<=', now()->subHours(48))
                               ->count();
        $totalWeddings   = Wedding::count();
        $activeWeddings  = Wedding::where('is_active', true)->count();
        $totalGuests     = Guest::count();
        $guestsRsvp      = Guest::whereNotNull('replied_at')->count();
        $guestsAttending = Guest::where('is_attending', true)->count();
        $checkedInToday  = Guest::whereDate('checked_in_at', $today)->count();

        // ── Pendapatan — hanya super-admin ──────────────────────────
        $isSuperAdmin = session('admin_is_super', false);
        $calcRevenue = fn($orders) => $orders->sum(fn($o) => $o->packageAmount());

        $revenueMonth = $isSuperAdmin ? $calcRevenue(
            Order::where('payment_status', 'lunas')
                ->whereDate('created_at', '>=', $monthStart)->get()
        ) : null;
        $revenueLastMonth = $isSuperAdmin ? $calcRevenue(
            Order::where('payment_status', 'lunas')
                ->whereDate('created_at', '>=', $lastMonthStart)
                ->whereDate('created_at', '<=', $lastMonthEnd)->get()
        ) : null;
        $revenueToday = $isSuperAdmin ? $calcRevenue(
            Order::where('payment_status', 'lunas')
                ->whereDate('created_at', $today)->get()
        ) : null;
        $revenueGrowth = ($isSuperAdmin && $revenueLastMonth > 0)
            ? round((($revenueMonth - $revenueLastMonth) / $revenueLastMonth) * 100)
            : null;

        // ── Chart: pesanan 7 hari terakhir ───────────────────────────
        $chartDays = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'label' => $date->translatedFormat('D'),
                'date'  => $date->toDateString(),
                'count' => Order::whereDate('created_at', $date->toDateString())->count(),
            ];
        });
        $chartMax = max(1, $chartDays->max('count'));

        // ── Distribusi paket undangan aktif ───────────────────────────
        $packageStats = [
            'vip'     => Wedding::where('package', 'vip')->where('is_active', true)->count(),
            'premium' => Wedding::where('package', 'premium')->where('is_active', true)->count(),
            'basic'   => Wedding::where('package', 'basic')->where('is_active', true)->count(),
            'trial'   => Wedding::where('package', 'trial')->where('is_active', true)->count(),
        ];

        // ── Pesanan butuh aksi ────────────────────────────────────────
        $actionOrders = Order::with('wedding')
            ->where(function ($q) {
                $q->where('payment_status', 'menunggu_konfirmasi')
                  ->orWhere('payment_status', 'ditolak')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'baru')->whereNull('wedding_id');
                  });
            })
            ->latest()
            ->limit(8)
            ->get();

        // ── Pesanan terbengkalai (belum bayar > 48 jam tanpa bukti) ──
        $stuckOrders = Order::with('wedding')
            ->where('payment_status', 'belum_bayar')
            ->whereNull('payment_proof')
            ->where('created_at', '<=', now()->subHours(48))
            ->latest()
            ->limit(5)
            ->get();

        // ── Undangan hampir/sudah expired (7 hari ke depan) — hanya non-trial ──
        $expiringSoon = Wedding::whereNotNull('trial_expires_at')
            ->where('package', '!=', 'trial')
            ->where('trial_expires_at', '<=', now()->addDays(7))
            ->orderBy('trial_expires_at')
            ->limit(10)
            ->get();

        // ── Distribusi jenis undangan ─────────────────────────────────────────────
        $jenisCount = [
            'birthday'   => Wedding::where('template', 'like', 'birthday%')->count(),
            'greeting'   => Wedding::where('template', 'like', 'greeting%')->count(),
        ];
        $jenisCount['wedding'] = max(0, $totalWeddings - $jenisCount['birthday'] - $jenisCount['greeting']);
        $jenisActive = [
            'birthday'   => Wedding::where('template', 'like', 'birthday%')->where('is_active', true)->count(),
            'greeting'   => Wedding::where('template', 'like', 'greeting%')->where('is_active', true)->count(),
        ];
        $jenisActive['wedding'] = max(0, $activeWeddings - $jenisActive['birthday'] - $jenisActive['greeting']);
        $jenisExpired = [
            'birthday'   => Wedding::where('template', 'like', 'birthday%')->whereNotNull('trial_expires_at')->where('trial_expires_at', '<', now())->count(),
            'greeting'   => Wedding::where('template', 'like', 'greeting%')->whereNotNull('trial_expires_at')->where('trial_expires_at', '<', now())->count(),
        ];
        $totalExpired = Wedding::whereNotNull('trial_expires_at')->where('trial_expires_at', '<', now())->count();
        $jenisExpired['wedding'] = max(0, $totalExpired - $jenisExpired['birthday'] - $jenisExpired['greeting']);
        $jenisTrashed = [
            'birthday'   => Wedding::onlyTrashed()->where('template', 'like', 'birthday%')->count(),
            'greeting'   => Wedding::onlyTrashed()->where('template', 'like', 'greeting%')->count(),
        ];
        $totalTrashed = Wedding::onlyTrashed()->count();
        $jenisTrashed['wedding'] = max(0, $totalTrashed - $jenisTrashed['birthday'] - $jenisTrashed['greeting']);

        // ── Chart: pendapatan lunas 7 hari terakhir (super-admin only) ───────
        $revenueChartDays = $isSuperAdmin ? collect(range(6, 0))->map(function ($daysAgo) use ($calcRevenue) {
            $date = now()->subDays($daysAgo);
            return [
                'label'  => $date->translatedFormat('D'),
                'date'   => $date->toDateString(),
                'amount' => $calcRevenue(
                    Order::where('payment_status', 'lunas')
                        ->whereDate('created_at', $date->toDateString())
                        ->get()
                ),
            ];
        }) : null;
        $revenueChartMax = $revenueChartDays ? max(1, $revenueChartDays->max('amount')) : 1;

        // ── Alert: QRIS ───────────────────────────────────────────────
        $qrisMissing = !Storage::disk('public')->exists('qris.png');

        // ── Undangan terbaru ──────────────────────────────────────────
        $recentWeddings = Wedding::latest()->limit(6)->get();

        // ── Undangan mendatang (30 hari ke depan) ─────────────────────
        $upcomingWeddings = Wedding::whereNotNull('event_date')
            ->whereDate('event_date', '>=', now()->toDateString())
            ->whereDate('event_date', '<=', now()->addDays(30)->toDateString())
            ->where('is_active', true)
            ->orderBy('event_date')
            ->limit(6)
            ->get();

        // ── VIP — list untuk shortcut panel ──────────────────────────
        $vipWeddings = Wedding::where('package', 'vip')
            ->where('is_active', true)
            ->latest()
            ->limit(8)
            ->get();

        // ── Premium — list untuk shortcut panel ──────────────────────
        $premiumWeddings = Wedding::where('package', 'premium')
            ->where('is_active', true)
            ->latest()
            ->limit(10)
            ->get();

        // ── RSVP terbaru lintas semua undangan ───────────────────────
        $recentRsvps = Guest::with('wedding')
            ->whereNotNull('replied_at')
            ->latest('replied_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'ordersToday', 'ordersMonth', 'pendingConfirm', 'pendingNew',
            'rejectedCount', 'stuckCount', 'stuckOrders',
            'totalWeddings', 'activeWeddings',
            'totalGuests', 'guestsRsvp', 'guestsAttending',
            'checkedInToday',
            'revenueMonth', 'revenueLastMonth', 'revenueToday', 'revenueGrowth',
            'isSuperAdmin',
            'chartDays', 'chartMax',
            'packageStats',
            'jenisCount', 'jenisActive', 'jenisExpired', 'jenisTrashed',
            'revenueChartDays', 'revenueChartMax',
            'actionOrders',
            'expiringSoon',
            'qrisMissing',
            'recentWeddings',
            'upcomingWeddings',
            'recentRsvps',
            'vipWeddings',
            'premiumWeddings',
        ));
    }

    public function statistik(): View
    {
        // ── Jenis undangan (dari prefix template) ─────────────────────
        $allWeddings = Wedding::get(['template', 'package', 'is_active']);
        $jenisCount = [
            'wedding'    => $allWeddings->filter(fn($w) => !str_starts_with($w->template ?? '', 'birthday') && !str_starts_with($w->template ?? '', 'greeting'))->count(),
            'birthday'   => $allWeddings->filter(fn($w) => str_starts_with($w->template ?? '', 'birthday'))->count(),
            'greeting'   => $allWeddings->filter(fn($w) => str_starts_with($w->template ?? '', 'greeting'))->count(),
        ];
        $jenisActive = [
            'wedding'    => $allWeddings->filter(fn($w) => $w->is_active && !str_starts_with($w->template ?? '', 'birthday') && !str_starts_with($w->template ?? '', 'greeting'))->count(),
            'birthday'   => $allWeddings->filter(fn($w) => $w->is_active && str_starts_with($w->template ?? '', 'birthday'))->count(),
            'greeting'   => $allWeddings->filter(fn($w) => $w->is_active && str_starts_with($w->template ?? '', 'greeting'))->count(),
        ];
        $totalUndangan = array_sum($jenisCount);

        // ── Distribusi paket ─────────────────────────────────────────
        $paketCount = [
            'vip'     => $allWeddings->where('package', 'vip')->count(),
            'premium' => $allWeddings->where('package', 'premium')->count(),
            'basic'   => $allWeddings->where('package', 'basic')->count(),
            'trial'   => $allWeddings->where('package', 'trial')->count(),
        ];
        $paketActive = [
            'vip'     => $allWeddings->where('package', 'vip')->where('is_active', true)->count(),
            'premium' => $allWeddings->where('package', 'premium')->where('is_active', true)->count(),
            'basic'   => $allWeddings->where('package', 'basic')->where('is_active', true)->count(),
            'trial'   => $allWeddings->where('package', 'trial')->where('is_active', true)->count(),
        ];

        // ── Status pembayaran all-time ────────────────────────────────
        $price    = fn($o) => $o->packageAmount();
        $allOrders = Order::with('wedding')->get();
        $payStatus = [
            'lunas'                => $allOrders->where('payment_status', 'lunas')->count(),
            'menunggu_konfirmasi'  => $allOrders->where('payment_status', 'menunggu_konfirmasi')->count(),
            'belum_bayar'          => $allOrders->where('payment_status', 'belum_bayar')->count(),
            'ditolak'              => $allOrders->where('payment_status', 'ditolak')->count(),
        ];
        $totalOrders  = $allOrders->count();
        $allLunas     = $allOrders->where('payment_status', 'lunas');
        $totalRevenue = $allLunas->sum($price);

        // Revenue per paket (lunas only) ─────────────────────────────
        $revenueByPaket = ['vip' => 0, 'premium' => 0, 'basic' => 0, 'trial' => 0];
        foreach ($allLunas as $o) {
            $key = $o->package ?? 'basic';
            if (array_key_exists($key, $revenueByPaket)) {
                $revenueByPaket[$key] += $price($o);
            }
        }

        // Revenue per jenis (via wedding template) ───────────────────
        $revenueByJenis = ['wedding' => 0, 'birthday' => 0, 'greeting' => 0];
        foreach ($allLunas as $o) {
            $t = optional($o->wedding)->template ?? '';
            if (str_starts_with($t, 'birthday'))        $revenueByJenis['birthday'] += $price($o);
            elseif (str_starts_with($t, 'greeting'))    $revenueByJenis['greeting'] += $price($o);
            else                                         $revenueByJenis['wedding'] += $price($o);
        }

        // ── Statistik tamu ───────────────────────────────────────────
        $totalGuests     = Guest::count();
        $guestsRsvp      = Guest::whereNotNull('replied_at')->count();
        $guestsAttending = Guest::where('is_attending', true)->count();
        $guestsDeclined  = Guest::where('is_attending', false)->whereNotNull('replied_at')->count();
        $guestsPending   = $totalGuests - $guestsRsvp;
        $guestsCheckedIn = Guest::whereNotNull('checked_in_at')->count();

        // ── Pendapatan 12 bulan ──────────────────────────────────────
        $monthly = collect(range(11, 0))->map(function ($mAgo) use ($price) {
            $start  = now()->subMonths($mAgo)->startOfMonth();
            $end    = now()->subMonths($mAgo)->endOfMonth();
            $orders = Order::whereBetween('created_at', [$start, $end])->get();
            $lunas  = $orders->where('payment_status', 'lunas');
            return [
                'label'       => $start->translatedFormat('M'),
                'year'        => $start->format('Y'),
                'count'       => $orders->count(),
                'revenue'     => $lunas->sum($price),
                'isThisMonth' => $mAgo === 0,
            ];
        });
        $monthlyMaxRevenue = max(1, $monthly->max('revenue'));
        $monthlyMaxCount   = max(1, $monthly->max('count'));

        return view('admin.statistik', compact(
            'jenisCount', 'jenisActive', 'totalUndangan',
            'paketCount', 'paketActive',
            'payStatus', 'totalOrders', 'totalRevenue',
            'revenueByPaket', 'revenueByJenis',
            'totalGuests', 'guestsRsvp', 'guestsAttending', 'guestsDeclined', 'guestsPending', 'guestsCheckedIn',
            'monthly', 'monthlyMaxRevenue', 'monthlyMaxCount',
        ));
    }

    public function trackRecord(): View
    {
        $price = fn($o) => $o->packageAmount();

        // ── 12 bulan terakhir ─────────────────────────────────────────
        $monthly = collect(range(11, 0))->map(function ($mAgo) use ($price) {
            $start = now()->subMonths($mAgo)->startOfMonth();
            $end   = now()->subMonths($mAgo)->endOfMonth();
            $orders = Order::whereBetween('created_at', [$start, $end])->get();
            $lunas  = $orders->where('payment_status', 'lunas');
            return [
                'label'    => $start->translatedFormat('M Y'),
                'total'    => $orders->count(),
                'lunas'    => $lunas->count(),
                'pending'  => $orders->where('payment_status', 'menunggu_konfirmasi')->count(),
                'belum'    => $orders->where('payment_status', 'belum_bayar')->count(),
                'ditolak'  => $orders->where('payment_status', 'ditolak')->count(),
                'revenue'  => $lunas->sum($price),
                'isThisMonth' => $mAgo === 0,
            ];
        });

        // ── All-time summary ──────────────────────────────────────────
        $allOrders   = Order::get();
        $allLunas    = $allOrders->where('payment_status', 'lunas');
        $totalRevenue   = $allLunas->sum($price);
        $totalOrders    = $allOrders->count();
        $totalLunas     = $allLunas->count();
        $totalPending   = $allOrders->where('payment_status', 'menunggu_konfirmasi')->count();
        $totalBelum     = $allOrders->where('payment_status', 'belum_bayar')->count();
        $totalDitolak   = $allOrders->where('payment_status', 'ditolak')->count();

        // ── Breakdown per paket (all-time lunas) ─────────────────────
        $byPackage = [
            'vip'     => ['count' => 0, 'revenue' => 0],
            'premium' => ['count' => 0, 'revenue' => 0],
            'basic'   => ['count' => 0, 'revenue' => 0],
            'trial'   => ['count' => 0, 'revenue' => 0],
        ];
        foreach ($allLunas as $o) {
            $key = $o->package ?? 'basic';
            $byPackage[$key]['count']++;
            $byPackage[$key]['revenue'] += $price($o);
        }

        // ── Tabel semua order lunas ───────────────────────────────────
        $lunasOrders = Order::with('wedding')
            ->where('payment_status', 'lunas')
            ->latest()
            ->paginate(30);

        return view('admin.track-record', compact(
            'monthly', 'totalRevenue', 'totalOrders',
            'totalLunas', 'totalPending', 'totalBelum', 'totalDitolak',
            'byPackage', 'lunasOrders',
        ));
    }}