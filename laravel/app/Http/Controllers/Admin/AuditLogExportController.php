<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $user = $request->user();

        abort_unless($user && $user->hasRole('super_admin'), 403);

        $filename = 'audit-logs-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            if (! $handle) {
                return;
            }

            fputcsv($handle, ['timestamp', 'user', 'action', 'model', 'record_id', 'ip_address']);

            AuditLog::query()
                ->with('user')
                ->orderByDesc('id')
                ->chunk(500, function ($rows) use ($handle): void {
                    foreach ($rows as $row) {
                        fputcsv($handle, [
                            optional($row->created_at)->toDateTimeString(),
                            $row->user?->name ?? 'System',
                            $row->action,
                            $row->auditable_type,
                            $row->auditable_id,
                            $row->ip_address,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
