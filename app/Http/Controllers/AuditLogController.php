<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
class AuditLogController extends Controller
{
    /** GET /admin/security — security monitor, super admin only */
    public function security(): View
    {
        if (!session('admin_is_super')) {
            abort(403);
        }

        $suspectActions = ['login_failed', 'login_blocked'];

        $totalFailed = AdminAuditLog::whereIn('action', $suspectActions)->count();
        $failed24h   = AdminAuditLog::whereIn('action', $suspectActions)
                           ->where('created_at', '>=', now()->subHours(24))->count();
        $failed7d    = AdminAuditLog::whereIn('action', $suspectActions)
                           ->where('created_at', '>=', now()->subDays(7))->count();
        $uniqueIps   = AdminAuditLog::whereIn('action', $suspectActions)
                           ->whereNotNull('ip_address')
                           ->distinct('ip_address')->count('ip_address');

        // Group by IP — sorted by fail count desc
        $byIp = AdminAuditLog::whereIn('action', $suspectActions)
                    ->selectRaw('ip_address, count(*) as total, max(created_at) as last_seen, count(distinct user_agent) as device_count')
                    ->groupBy('ip_address')
                    ->orderByDesc('total')
                    ->limit(50)
                    ->get();

        // Recent 100 failed/blocked attempts
        $recent = AdminAuditLog::whereIn('action', $suspectActions)
                      ->orderByDesc('created_at')
                      ->limit(100)
                      ->get();

        return view('admin.security', compact('totalFailed', 'failed24h', 'failed7d', 'uniqueIps', 'byIp', 'recent'));
    }

    /** GET /admin/audit-log — hanya super admin */
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        if (!session('admin_is_super')) {
            abort(403);
        }

        $q        = $request->query('q');
        $actor    = $request->query('actor');
        $action   = $request->query('action');
        $fromDate = $request->query('from');
        $toDate   = $request->query('to');

        $query = AdminAuditLog::query()->orderByDesc('created_at');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('actor_email', 'like', "%{$q}%")
                    ->orWhere('action', 'like', "%{$q}%")
                    ->orWhere('target_type', 'like', "%{$q}%")
                    ->orWhere('target_id', 'like', "%{$q}%")
                    ->orWhere('ip_address', 'like', "%{$q}%");
            });
        }

        if ($actor) {
            $query->where('actor_type', $actor);
        }

        if ($action) {
            $query->where('action', $action);
        }

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Distinct action list for filter dropdown
        $actions = AdminAuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.audit-log', compact('logs', 'actions', 'q', 'actor', 'action', 'fromDate', 'toDate'));
    }
}
