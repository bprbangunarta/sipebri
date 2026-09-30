<?php

namespace App\Audit;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Writes the audit trail. Every entry says who did what to which record, when, from where, with what result
 * and (for changes) the values before and after. Secrets are never written: see `REDACTED`.
 */
class Audit
{
    /** Attribute names whose values are replaced by a marker, wherever they appear. */
    public const REDACTED = ['password', 'current_password', 'password_confirmation', 'remember_token', 'mfa_secret', 'mfa_recovery_codes', 'token', 'secret', 'otp'];

    public const MARKER = '[redacted]';

    private static bool $enabled = true;

    /** @var array<string, mixed> */
    private static array $context = [];

    private static ?string $actorLabel = null;

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     * @param  array<string, mixed>  $context
     */
    public static function record(
        string $event,
        string $module,
        string $action,
        ?Model $subject = null,
        array $old = [],
        array $new = [],
        array $context = [],
        string $outcome = 'success',
        ?string $label = null,
        ?User $actor = null,
    ): ?AuditLog {
        if (! self::$enabled) {
            return null;
        }

        $actor ??= Auth::user();
        $request = app()->runningInConsole() && ! app()->runningUnitTests() ? null : request();

        $row = [
            'created_at' => now()->format('Y-m-d H:i:s.u'),
            'request_id' => self::requestId(),
            'user_id' => $actor?->getAuthIdentifier(),
            'user_name' => $actor !== null ? $actor->name : self::$actorLabel,
            'username' => $actor?->username,
            'event' => $event,
            'module' => $module,
            'action' => $action,
            'outcome' => $outcome,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'subject_label' => $label ?? ($subject && method_exists($subject, 'auditLabel') ? $subject->auditLabel() : null),
            'old_values' => $old === [] ? null : self::encode(self::redact($old)),
            'new_values' => $new === [] ? null : self::encode(self::redact($new)),
            'context' => ($merged = [...self::$context, ...self::redact($context)]) === [] ? null : self::encode($merged),
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? Str::limit((string) $request->userAgent(), 250, '') : null,
            'http_method' => $request?->method(),
            'url' => $request ? Str::limit($request->fullUrl(), 490, '') : null,
        ];

        return DB::transaction(function () use ($row): AuditLog {
            $previous = AuditLog::query()->orderByDesc('id')->lockForUpdate()->value('hash');

            return AuditLog::query()->create([...$row, 'previous_hash' => $previous, 'hash' => self::hash($row, $previous)]);
        });
    }

    /**
     * Run a callback without writing audit entries (seeders and other non-user work).
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function withoutAuditing(callable $callback): mixed
    {
        $previous = self::$enabled;
        self::$enabled = false;

        try {
            return $callback();
        } finally {
            self::$enabled = $previous;
        }
    }

    /**
     * Run a callback with extra context on every entry and a label used when nobody is signed in
     * (e.g. the Codex sync that runs while a person is still signing in).
     *
     * @template T
     *
     * @param  array<string, mixed>  $context
     * @param  callable(): T  $callback
     * @return T
     */
    public static function withContext(array $context, string $actorLabel, callable $callback): mixed
    {
        [$previousContext, $previousLabel] = [self::$context, self::$actorLabel];
        self::$context = [...self::$context, ...$context];
        self::$actorLabel = $actorLabel;

        try {
            return $callback();
        } finally {
            [self::$context, self::$actorLabel] = [$previousContext, $previousLabel];
        }
    }

    /**
     * The keyed hash stored on each row: chains the row to the one before it.
     *
     * @param  array<string, mixed>  $row  the columns as stored (before `previous_hash` / `hash`)
     */
    public static function hash(array $row, ?string $previous): string
    {
        $content = implode("\x1F", [
            $previous ?? '', $row['created_at'], $row['request_id'], (string) $row['user_id'], (string) $row['user_name'], (string) $row['username'],
            $row['event'], $row['module'], $row['action'], $row['outcome'], (string) $row['subject_type'], (string) $row['subject_id'],
            (string) $row['subject_label'], (string) $row['old_values'], (string) $row['new_values'], (string) $row['context'],
            (string) $row['ip_address'], (string) $row['user_agent'], (string) $row['http_method'], (string) $row['url'],
        ]);

        return hash_hmac('sha256', $content, (string) config('app.key'));
    }

    /**
     * Replace secret values, at any depth.
     *
     * @param  array<int|string, mixed>  $values
     * @return array<int|string, mixed>
     */
    public static function redact(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_string($key) && in_array($key, self::REDACTED, true)) {
                $values[$key] = self::MARKER;
            } elseif (is_array($value)) {
                $values[$key] = self::redact($value);
            }
        }

        return $values;
    }

    /**
     * @param  array<int|string, mixed>  $values
     */
    private static function encode(array $values): string
    {
        return (string) json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    private static function requestId(): string
    {
        $request = request();

        if (! $request->attributes->has('request_id')) {
            $request->attributes->set('request_id', (string) Str::uuid());
        }

        return $request->attributes->get('request_id');
    }
}
