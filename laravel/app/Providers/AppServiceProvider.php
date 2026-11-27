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
        if (app()->environment('production') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Gate::before(function (User $user) {
            return $user->hasRole('super_admin') ? true : null;
        });

        Event::observe(ModelAuditObserver::class);
        GalleryItem::observe(ModelAuditObserver::class);
        Ministry::observe(ModelAuditObserver::class);
        Page::observe(ModelAuditObserver::class);
        SiteSetting::observe(ModelAuditObserver::class);

        View::composer('*', function ($view) {
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

                $view->with('siteSettings', $settings);
                $view->with('navMinistries', $navMinistries);
                $view->with('siteLogoUrl', $siteLogoUrl);
            } catch (\Throwable $e) {
                // Database or URL generator not ready. Ignore.
            }
        });
    }
}
