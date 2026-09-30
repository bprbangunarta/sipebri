<?php

namespace App\Support;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Targeted notifications: only users who hold the relevant permission receive them,
 * and the actor is never notified about their own action.
 */
class Notify
{
    public static function toPermission(string $permission, string $title, string $module, ?string $body = null, ?string $url = null, string $level = 'info'): int
    {
        $actor = Auth::user();

        $recipients = User::query()->permission($permission)
            ->when($actor, fn ($q) => $q->whereKeyNot($actor->getKey()))
            ->get();

        return self::send($recipients->all(), $title, $module, $body, $url, $level);
    }

    public static function toUser(User $user, string $title, string $module, ?string $body = null, ?string $url = null, string $level = 'info'): void
    {
        self::send([$user], $title, $module, $body, $url, $level);
    }

    /**
     * @param  array<int, User>  $recipients
     */
    private static function send(array $recipients, string $title, string $module, ?string $body, ?string $url, string $level): int
    {
        $now = now();

        AppNotification::query()->insert(array_map(fn (User $user): array => [
            'user_id' => $user->id,
            'actor_id' => Auth::id(),
            'title' => $title,
            'body' => $body,
            'module' => $module,
            'level' => $level,
            'url' => $url,
            'created_at' => $now,
            'updated_at' => $now,
        ], $recipients));

        return count($recipients);
    }
}
