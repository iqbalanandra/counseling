<?php

namespace App\Presentation\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('audit_log.view');

        $logs = AuditLog::with('user')
            ->when($request->query('module'), fn ($q, $v) => $q->where('module', $v))
            ->when($request->query('action'), fn ($q, $v) => $q->where('action', $v))
            ->when($request->query('search'), fn ($q, $v) => $q->where(function ($sq) use ($v) {
                $sq->where('user_name', 'like', "%{$v}%")
                    ->orWhere('description', 'like', "%{$v}%");
            }))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('AuditLogs/Index', [
            'logs' => $logs->through(fn ($log) => [
                'id' => $log->id,
                'user_name' => $log->user_name,
                'action' => $log->action,
                'module' => $log->module,
                'description' => $log->description,
                'resource_id' => $log->resource_id,
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at?->toDateTimeString(),
            ]),
            'filters' => $request->only('module', 'action', 'search'),
        ]);
    }
}
