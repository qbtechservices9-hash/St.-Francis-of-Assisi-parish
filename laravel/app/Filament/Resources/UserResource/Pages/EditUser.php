<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $incomingRoles = array_values(array_filter((array) ($data['roles'] ?? [])));

        if ($this->record->hasRole('super_admin') && ! in_array('super_admin', $incomingRoles, true)) {
            if (User::role('super_admin')->count() <= 1) {
                throw ValidationException::withMessages([
                    'roles' => 'You cannot remove the last super admin role from the system.',
                ]);
            }
        }

        return $data;
    }
}
