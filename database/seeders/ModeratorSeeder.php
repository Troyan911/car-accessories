<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;

class ModeratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('MODERATOR_EMAIL', 'moderator@admin.com');

        if (! User::where('email', $email)->exists()) {
            (User::factory()->withEmail($email)->create())->syncRoles(Roles::MODERATOR->value);
        }
    }
}
