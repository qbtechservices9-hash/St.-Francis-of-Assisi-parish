<?php

namespace App\Providers;

use App\Support\MediaStorage;
use App\Models\Event;
use App\Models\GalleryItem;
use App\Models\Ministry;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\User;
use App\Observers\ModelAuditObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function (User $user) {
            return $user->hasRole('super_admin') ? true : null;
        });

        Event::observe(ModelAuditObserver::class);
        GalleryItem::observe(ModelAuditObserver::class);
        Ministry::observe(ModelAuditObserver::class);
        Page::observe(ModelAuditObserver::class);
        SiteSetting::observe(ModelAuditObserver::class);

        try {
            $settings = Cache::remember('site_settings', now()->addHours(6), function () {
                return SiteSetting::query()->pluck('value', 'key')->toArray();
            });

            $navMinistries = Cache::remember('nav_ministries', now()->addHours(6), function () {
                return Ministry::active()
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug']);
            });

            $logoValue = $settings['logo'] ?? null;
            $siteLogoUrl = null;

            if (is_string($logoValue) && $logoValue !== '') {
                $siteLogoUrl = MediaStorage::url($logoValue);
            }

            if (! $siteLogoUrl) {
                $siteLogoUrl = asset('images/logo.jpg');
            }

            View::share('siteSettings', $settings);
            View::share('navMinistries', $navMinistries);
            View::share('siteLogoUrl', $siteLogoUrl);
        } catch (\Throwable $e) {
            // Database not ready (e.g., before migrations). Ignore.
        }
    }
}
