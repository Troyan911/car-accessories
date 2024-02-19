<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin2@admin.com');

        if (! User::where('email', $adminEmail)->exists()) {
            (User::factory()->withEmail($adminEmail)->create())->syncRoles(Roles::ADMIN->value);
        }
    }
}
