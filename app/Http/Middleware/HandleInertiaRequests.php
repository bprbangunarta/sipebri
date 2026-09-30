<?php

namespace App\Http\Middleware;

use App\Models\AppNotification;
use App\Security\TwoFactor\TwoFactor;
use App\Support\Navigation;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user() ? [
                    ...$request->user()->only(['id', 'name', 'email']),
                    'role' => $request->user()->getRoleNames()->first(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name')->values(),
                ] : null,
            ],
            'security' => fn () => $request->user() ? app(TwoFactor::class)->summary($request->user()) : null,
            'navigation' => fn () => $request->user() ? Navigation::for($request->user()) : [],
            'notifications' => fn () => $request->user() ? [
                'unread' => AppNotification::query()->where('user_id', $request->user()->id)->whereNull('read_at')->count(),
                'items' => AppNotification::query()->where('user_id', $request->user()->id)->latest('id')->limit(10)
                    ->get(['id', 'title', 'body', 'level', 'url', 'read_at', 'created_at'])
                    ->map(fn (AppNotification $n): array => [...$n->only(['id', 'title', 'body', 'level', 'url']), 'read' => $n->read_at !== null, 'time' => $n->created_at->diffForHumans()]),
            ] : ['unread' => 0, 'items' => []],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
