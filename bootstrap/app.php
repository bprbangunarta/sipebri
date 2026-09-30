<?php

use App\Audit\Audit;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo(fn () => route('home'));
        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Inside the app 403 / 404 / 419 render an Inertia page with the admin layout, so a signed-in person
        // can always log out. Every other status (500, 503 maintenance, 429, ...) uses the themed Blade views
        // in resources/views/errors, which also work when the framework cannot boot the app (maintenance mode).
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $status = $response->getStatusCode();

            if ($status === 403 && $request->user() !== null) {
                try {
                    Audit::record('access.denied', 'access', 'denied', context: ['route' => $request->route()?->getName(), 'reason' => $exception->getMessage()], outcome: 'denied', label: $request->method().' /'.$request->path());
                } catch (Throwable $auditFailure) {
                    report($auditFailure);
                }
            }

            if (! in_array($status, [403, 404, 419], true) || $request->expectsJson()) {
                return $response;
            }

            return Inertia::render('error', [
                'status' => $status,
                'message' => $exception instanceof HttpExceptionInterface ? $exception->getMessage() : '',
            ])->toResponse($request)->setStatusCode($status);
        });
    })->create();
