<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AdminQuickActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-quick-actions-widget';

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']);
    }
}
