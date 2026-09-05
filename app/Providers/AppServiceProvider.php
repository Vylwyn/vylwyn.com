<?php

declare(strict_types=1);

namespace App\Providers;

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
        $this->pinCanonicalUrl();
    }

    /**
     * Generate every URL from APP_URL rather than the incoming request host.
     *
     * url()->current() normally mirrors whatever host the visitor used, so a
     * request to www.vylwyn.com emits a canonical tag pointing at www — the
     * exact duplicate-content signal the .htaccess redirect exists to prevent.
     * Pinning the root means canonical, og:url and the sitemap always agree on
     * one hostname, redirect or no redirect.
     *
     * Local only skips this so Herd keeps working on portfolio.test.
     */
    private function pinCanonicalUrl(): void
    {
        if ($this->app->environment('local', 'testing')) {
            return;
        }

        URL::forceRootUrl(config('app.url'));
        URL::forceScheme('https');
    }
}
