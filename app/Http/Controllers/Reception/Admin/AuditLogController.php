<?php

namespace App\Http\Controllers\Reception\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string)$request->get('q', ''));
        $action   = $request->get('action');
        $severity = $request->get('severity');
        $from     = $request->get('from');
        $to       = $request->get('to');

        $logs = AuditLog::query()
            ->when($action, fn($x) => $x->where('action', $action))
            ->when($severity, fn($x) => $x->where('severity', $severity))
            ->when($from, fn($x) => $x->whereDate('created_at', '>=', $from))
            ->when($to, fn($x) => $x->whereDate('created_at', '<=', $to))
            ->when($q !== '', function ($x) use ($q) {
                $x->where(function ($sub) use ($q) {
                    $sub->where('action', 'like', "%{$q}%")
                        ->orWhere('entity_type', 'like', "%{$q}%")
                        ->orWhere('entity_id', 'like', "%{$q}%")
                        ->orWhere('actor_name', 'like', "%{$q}%")
                        ->orWhere('actor_email', 'like', "%{$q}%")
                        ->orWhere('ip', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('reception.admin.logs.index', [
            'logs' => $logs,
            'actions' => $actions,
            'q' => $q,
            'action' => $action,
            'severity' => $severity,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
