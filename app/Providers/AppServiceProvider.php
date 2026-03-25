<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS di production (hosting Rumahweb / shared hosting)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Rate limiter: greeting reaction — 20 req/menit per IP (wajar untuk klik normal, cegah bot)
        RateLimiter::for('gc-react', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });

        // Rate limiter: trial store — 2x per jam, kecuali localhost (untuk testing)
        RateLimiter::for('trial-store', function (Request $request) {
            if (in_array($request->ip(), ['127.0.0.1', '::1'], true)) {
                return Limit::none();
            }
            return Limit::perHour(2)->by($request->ip());
        });

        // @nonce directive — outputs the CSP nonce attribute for inline <script> and <style> tags
        // Usage in Blade: <script @nonce> or <style @nonce>
        Blade::directive('nonce', function () {
            return '<?php echo \'nonce="\' . e(app()->bound("csp-nonce") ? app("csp-nonce") : "") . \'"\'; ?>';
        });
    }
}
