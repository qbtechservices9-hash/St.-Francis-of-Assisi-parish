<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AdminHeaderWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-header-widget';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected function getViewData(): array
    {
        $user = Auth::user();

        return [
            'userName' => $user?->name ?? 'Administrator',
        ];
    }
}
