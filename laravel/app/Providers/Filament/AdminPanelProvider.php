<?php

namespace App\Providers\Filament;

use App\Support\MediaStorage;
use App\Filament\Widgets\AdminActivityChartWidget;
use App\Filament\Widgets\AdminApprovalQueueWidget;
use App\Filament\Widgets\AdminHeaderWidget;
use App\Filament\Widgets\AdminMySubmissionsWidget;
use App\Filament\Widgets\AdminQuickActionsWidget;
use App\Filament\Widgets\AdminRecentActivityWidget;
use App\Filament\Widgets\AdminStatsWidget;
use App\Models\SiteSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    private ?string $resolvedLogoUrl = null;

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->default()
            ->path('admin')
            ->brandName('St. Francis Of Assisi')
            ->brandLogo(fn () => $this->resolveLogoUrl())
            ->brandLogoHeight('2.25rem')
            ->favicon(fn () => $this->resolveLogoUrl())
            ->colors([
                'primary' => Color::hex('#6B2C2C'),
                'amber' => Color::hex('#D4AF37'),
                'gray' => Color::Zinc,
            ])
            ->font('Source Sans 3')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->maxContentWidth(Width::SevenExtraLarge)
            ->sidebarWidth('16rem')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Management',
                'Content',
                'Settings',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                AdminHeaderWidget::class,
                AdminApprovalQueueWidget::class,
                AdminMySubmissionsWidget::class,
                AdminStatsWidget::class,
                AdminActivityChartWidget::class,
                AdminQuickActionsWidget::class,
                AdminRecentActivityWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    private function resolveLogoUrl(): string
    {
        if ($this->resolvedLogoUrl !== null) {
            return $this->resolvedLogoUrl;
        }

        try {
            $logoValue = SiteSetting::getValue('logo');
        } catch (\Throwable $e) {
            $logoValue = null;
        }

        if (is_string($logoValue) && $logoValue !== '') {
            return $this->resolvedLogoUrl = MediaStorage::url($logoValue) ?? asset('images/logo.jpg');
        }

        return $this->resolvedLogoUrl = asset('images/logo.jpg');
    }
}
