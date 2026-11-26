<?php

namespace App\Filament\Pages;

use Filament\Pages\Page as FilamentPage;

class PermissionsMatrixPage extends FilamentPage
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Permissions Matrix';

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.permissions-matrix';

    public array $matrix = [];

    public function mount(): void
    {
        $this->matrix = [
            ['feature' => 'Dashboard Access', 'super_admin' => true, 'content_admin' => true, 'ministry_admin' => true],
            ['feature' => 'User & Role Management', 'super_admin' => true, 'content_admin' => false, 'ministry_admin' => false],
            ['feature' => 'Site Settings', 'super_admin' => true, 'content_admin' => false, 'ministry_admin' => false],
            ['feature' => 'Audit Logs', 'super_admin' => true, 'content_admin' => false, 'ministry_admin' => false],
            ['feature' => 'Edit Home/About Pages', 'super_admin' => true, 'content_admin' => true, 'ministry_admin' => false],
            ['feature' => 'Approve/Reject Events', 'super_admin' => true, 'content_admin' => true, 'ministry_admin' => false],
            ['feature' => 'Approve/Reject Gallery', 'super_admin' => true, 'content_admin' => true, 'ministry_admin' => false],
            ['feature' => 'Create/Edit Own Ministry Events', 'super_admin' => true, 'content_admin' => true, 'ministry_admin' => true],
            ['feature' => 'Create/Edit Own Ministry Gallery', 'super_admin' => true, 'content_admin' => true, 'ministry_admin' => true],
            ['feature' => 'Manage Ministries', 'super_admin' => true, 'content_admin' => false, 'ministry_admin' => true],
        ];
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }
}
