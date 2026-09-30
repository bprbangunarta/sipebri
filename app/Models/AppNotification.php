<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * In-app notification (bell). One row per recipient.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $actor_id
 * @property string $title
 * @property string|null $body
 * @property string $module
 * @property string $level
 * @property string|null $url
 * @property Carbon|null $read_at
 */
#[Fillable(['user_id', 'actor_id', 'title', 'body', 'module', 'level', 'url', 'read_at'])]
class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
