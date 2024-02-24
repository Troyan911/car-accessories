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
        $email = env('ADMIN_EMAIL', 'admin@admin.com');

        if (! User::where('email', $email)->exists()) {
            (User::factory()->withEmail($email)->create())->syncRoles(Roles::ADMIN->value);
        }
    }
}
