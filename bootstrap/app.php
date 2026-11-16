<?php

use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectUsersTo(fn () => auth()->user()?->getDefaultLandingUrl() ?? '/admin');
        $middleware->append(SecurityHeadersMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Jika user yang sudah login mengakses halaman tanpa izin (misal: dashboard saat tidak punya akses)
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            if (auth()->check()) {
                $user = auth()->user();
                $landingUrl = $user->getDefaultLandingUrl();

                if ($request->url() !== $landingUrl && $request->fullUrl() !== $landingUrl) {
                    return redirect($landingUrl)->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut. Dialihkan ke halaman sesuai hak akses Anda.');
                }
            }

            return null;
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 403 && ! $request->is('api/*') && ! $request->expectsJson()) {
                if (auth()->check()) {
                    $user = auth()->user();
                    $landingUrl = $user->getDefaultLandingUrl();

                    if ($request->url() !== $landingUrl && $request->fullUrl() !== $landingUrl) {
                        return redirect($landingUrl)->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut. Dialihkan ke halaman sesuai hak akses Anda.');
                    }
                }
            }

            return null;
        });
    })->create();
