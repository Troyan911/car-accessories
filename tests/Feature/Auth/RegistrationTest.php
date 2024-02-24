<?php

namespace Tests\Feature\Auth;

use App\Enums\Roles;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ModeratorSeeder;
use Database\Seeders\PermissionsAndRolesSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function afterRefreshingDatabase()
    {
        $this->seed(PermissionsAndRolesSeeder::class);
        $this->seed(UsersSeeder::class);
        $this->seed(ModeratorSeeder::class);
        $this->seed(AdminSeeder::class);
    }

    public function test_success_registration(): void
    {
        $user = User::factory()->withPassword('qwerty12')->create()->syncRoles(Roles::MODERATOR);
//dd($user);
//        $userData = $user->toArray();
//        $userData['password'] = 'qwerty12';
//        $userData['password-confirm'] = 'qwerty12';

//        dd($userData);
        $resp = $this->post(route('register'), $user);

//        dd($resp);
        $resp->assertStatus(302);
//        $this->assertAuthenticatedAs($user);
//        $this->assertDatabaseHas(User::class, [
//            'name' => $userData['name'],
//            'email' => $userData['email'],
//        ]);

        $resp = $this->get(route('home'));
        $resp
            //->assertStatus(200)
//            ->assertStatus(302)
            ->assertViewIs('home')
            ->assertSeeText('You are logged in!');
    }
}
