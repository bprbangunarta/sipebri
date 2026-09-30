<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Audit\AuditLog;
use App\Audit\AuditVerifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Read-only viewer of the audit trail. Nothing here can change an entry; looking at, exporting and
 * verifying the trail are themselves recorded.
 */
class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $this->filters($request);
        $logs = $this->query($filters)->orderByDesc('id')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), [10, 25, 50], true) ? (int) $filters['per_page'] : 25)
            ->withQueryString();

        return Inertia::render('audit-logs/index', [
            'logs' => $logs->through(fn (AuditLog $log): array => [
                'id' => $log->id,
                'at' => $log->created_at->format('Y-m-d H:i:s'),
                'user' => $log->user_name ?? $log->username,
                'username' => $log->username,
                'event' => $log->event,
                'module' => $log->module,
                'action' => $log->action,
                'outcome' => $log->outcome,
                'subject' => $log->subject_label,
                'subject_type' => $log->subject_type ? class_basename($log->subject_type) : null,
                'subject_id' => $log->subject_id,
                'old' => $log->decoded('old_values'),
                'new' => $log->decoded('new_values'),
                'context' => $log->decoded('context'),
                'ip' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'method' => $log->http_method,
                'url' => $log->url,
                'request_id' => $log->request_id,
                'hash' => $log->hash,
            ]),
            'filters' => [
                'search' => $filters['search'] ?? '',
                'module' => $filters['module'] ?? null,
                'outcome' => $filters['outcome'] ?? null,
                'from' => $filters['from'] ?? null,
                'to' => $filters['to'] ?? null,
                'per_page' => $logs->perPage(),
            ],
            'modules' => AuditLog::query()->distinct()->orderBy('module')->pluck('module'),
            'verification' => $request->session()->get('audit_verification'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        Audit::record('audit_logs.exported', 'audit_logs', 'exported', context: ['filters' => array_filter($filters)], label: 'Audit log export');

        return response()->streamDownload(function () use ($filters): void {
            $out = fopen('php://output', 'w') ?: throw new RuntimeException('Cannot open the output stream.');
            fputcsv($out, ['id', 'time', 'user', 'username', 'event', 'module', 'action', 'outcome', 'subject_type', 'subject_id', 'subject', 'old_values', 'new_values', 'context', 'ip', 'method', 'url', 'request_id', 'hash']);

            $this->query($filters)->orderBy('id')->chunkById(500, function ($chunk) use ($out): void {
                foreach ($chunk as $log) {
                    fputcsv($out, array_map($this->csvCell(...), [
                        $log->id, $log->created_at->format('Y-m-d H:i:s.u'), $log->user_name, $log->username, $log->event, $log->module, $log->action, $log->outcome,
                        $log->subject_type, $log->subject_id, $log->subject_label, $log->old_values, $log->new_values, $log->context, $log->ip_address,
                        $log->http_method, $log->url, $log->request_id, $log->hash,
                    ]));
                }
            });

            fclose($out);
        }, 'audit-log-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function verify(AuditVerifier $verifier): RedirectResponse
    {
        $result = $verifier->verify();
        Audit::record('audit_logs.verified', 'audit_logs', 'verified', context: $result, outcome: $result['ok'] ? 'success' : 'failure', label: 'Audit chain verification');

        return back()->with('audit_verification', $result);
    }

    /**
     * @return array<string, mixed>
     */
    private function filters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'module' => ['nullable', 'string', 'max:50'],
            'outcome' => ['nullable', 'in:success,failure,denied'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'per_page' => ['nullable', 'integer'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<AuditLog>
     */
    private function query(array $filters): Builder
    {
        return AuditLog::query()
            ->when($filters['search'] ?? null, function (Builder $q, string $term): void {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn (Builder $w) => $w->where('user_name', 'like', $like)->orWhere('username', 'like', $like)->orWhere('event', 'like', $like)
                    ->orWhere('subject_label', 'like', $like)->orWhere('ip_address', 'like', $like)->orWhere('request_id', 'like', $like));
            })
            ->when($filters['module'] ?? null, fn (Builder $q, string $module) => $q->where('module', $module))
            ->when($filters['outcome'] ?? null, fn (Builder $q, string $outcome) => $q->where('outcome', $outcome))
            ->when($filters['from'] ?? null, fn (Builder $q, string $from) => $q->where('created_at', '>=', $from.' 00:00:00.000000'))
            ->when($filters['to'] ?? null, fn (Builder $q, string $to) => $q->where('created_at', '<=', $to.' 23:59:59.999999'));
    }

    /** Spreadsheet formula characters at the start of a cell are neutralised so an exported value cannot run as a formula. */
    private function csvCell(mixed $value): mixed
    {
        return is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'".$value : $value;
    }
}
