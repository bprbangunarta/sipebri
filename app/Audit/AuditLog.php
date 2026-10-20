<?php

namespace App\Audit;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use LogicException;

/**
 * One entry of the audit trail. Rows are written once and never changed or removed by the application.
 *
 * @property int $id
 * @property Carbon $created_at
 * @property string $request_id
 * @property int|null $user_id
 * @property string|null $user_name
 * @property string|null $username
 * @property string $event
 * @property string $module
 * @property string $action
 * @property string $outcome
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $subject_label
 * @property string|null $old_values JSON exactly as hashed
 * @property string|null $new_values JSON exactly as hashed
 * @property string|null $context JSON exactly as hashed
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $http_method
 * @property string|null $url
 * @property string|null $previous_hash
 * @property string $hash
 */
#[Fillable([
    'created_at', 'request_id', 'user_id', 'user_name', 'username', 'event', 'module', 'action', 'outcome', 'subject_type', 'subject_id',
    'subject_label', 'old_values', 'new_values', 'context', 'ip_address', 'user_agent', 'http_method', 'url', 'previous_hash', 'hash',
])]
class AuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'audit_logs';

    /** Microseconds are part of the hashed content, so they must survive storage. */
    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('The audit trail is append-only: entries cannot be changed.'));
        static::deleting(fn () => throw new LogicException('The audit trail is append-only: entries cannot be deleted.'));
    }

    /**
     * The entries the audit screen and its export show for these (already validated) filters.
     *
     * @param  Builder<AuditLog>  $query
     * @param  array<string, mixed>  $filters
     */
    #[Scope]
    protected function matching(Builder $query, array $filters): void
    {
        $query
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

    /** Whether this person opened the audit screen within the last given minutes. */
    public static function openedBy(int $userId, int $minutes): bool
    {
        return static::query()->where('event', 'audit_logs.viewed')->where('user_id', $userId)
            ->where('created_at', '>', now()->subMinutes($minutes)->format('Y-m-d H:i:s.u'))->exists();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function decoded(string $column): ?array
    {
        $value = $this->getAttribute($column);

        return is_string($value) ? json_decode($value, true) : null;
    }

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }
}
