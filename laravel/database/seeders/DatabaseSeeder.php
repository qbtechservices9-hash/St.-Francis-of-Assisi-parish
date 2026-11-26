<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('super_admin');
        Role::findOrCreate('content_admin');
        Role::findOrCreate('ministry_admin');

        $superAdmin = User::query()->updateOrCreate(
            ['email' => 'superadmin@stfrancis.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@12345'),
            ]
        );

        $superAdmin->syncRoles(['super_admin']);

        $firstUser = User::query()->whereKeyNot($superAdmin->id)->first();

        if ($firstUser && !$firstUser->hasAnyRole(['super_admin', 'content_admin', 'ministry_admin'])) {
            $firstUser->assignRole('content_admin');
        }
    }
}