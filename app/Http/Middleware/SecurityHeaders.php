<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Generate per-request nonce BEFORE rendering views so templates can access it via app('csp-nonce')
        $nonce = base64_encode(random_bytes(16));
        app()->instance('csp-nonce', $nonce);

        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->is('admin', 'admin/*')) {
            // Admin routes: 'unsafe-inline' is required because the admin views use many
            // inline onclick/onsubmit event handlers. Nonces are kept for future migration.
            // TODO: gradually convert inline handlers to addEventListener, then restore
            //       strict nonce-only mode by replacing 'unsafe-inline' with 'nonce-{$nonce}'.
            $response->headers->set('Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com data:; " .
                "img-src 'self' data: blob: https:; " .
                "frame-src 'none'; " .
                "connect-src 'self'; " .
                "object-src 'none'; " .
                "base-uri 'self';"
            );
        } else {
            // Public invitation routes: unsafe-inline + unsafe-eval needed for Alpine.js v3 (new Function) and Tailwind Play CDN
            $response->headers->set('Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com https://www.youtube.com https://s.ytimg.com https://www.googletagmanager.com; " .
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                "font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net https://cdn.jsdelivr.net https://cdnjs.cloudflare.com data:; " .
                "img-src 'self' data: blob: https:; " .
                "media-src 'self' https: blob:; " .
                "frame-src 'self' https://www.google.com https://maps.google.com https://www.youtube.com https://www.youtube-nocookie.com https://player.vimeo.com; " .
                "connect-src 'self' https://www.youtube.com https://*.youtube.com; " .
                "object-src 'none'; " .
                "base-uri 'self';"
            );
        }

        // HSTS: hanya aktif di production (HTTPS)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
