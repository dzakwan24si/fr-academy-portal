<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach essential security headers.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Remove PHP version disclosure from header (Resolves O-01 partial)
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
        }

        $response = $next($request);

        // Remove from response object headers if set by framework / web server
        $response->headers->remove('X-Powered-By');

        // 2. Content Security Policy (Resolves F-03)
        // Accommodates Vite assets, Google Fonts, Cloudflare Turnstile, Alpine/AOS CDNs, UI Avatars, and Google Maps iframe
        $cspDirectives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://unpkg.com https://challenges.cloudflare.com https://cdnjs.cloudflare.com https://cdn.tailwindcss.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com",
            "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' https://challenges.cloudflare.com",
            "frame-src 'self' https://www.google.com https://maps.google.com https://challenges.cloudflare.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self' https://challenges.cloudflare.com",
        ];

        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

        // 3. Essential Security Headers (Pentest best practice)
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
