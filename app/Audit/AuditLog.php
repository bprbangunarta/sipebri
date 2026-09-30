<?php

namespace App\Audit;

use Illuminate\Database\Eloquent\Attributes\Fillable;
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
