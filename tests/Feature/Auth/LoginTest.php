<?php

namespace Tests\Feature\Auth;

use App\Enums\Roles;
use App\Models\User;
use Database\Factories\UserFactory;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ModeratorSeeder;
use Database\Seeders\PermissionsAndRolesSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
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

    public function test_success_login(): void
    {
        $user = User::factory()->create()->syncRoles(Roles::MODERATOR);
        $resp = $this->post(route('login'), [
            'email' => $user['email'],
            'password' => 'qwerty12'
        ]);
        $this->assertAuthenticatedAs($user);

        $resp = $this->get(route('home'));
        $resp->assertStatus(200)
//            ->assertStatus(302)
            ->assertViewIs('home')
            ->assertSeeText('You are logged in!');

//        $resp = $this->get(route('admin.dashboard'))->assertViewIs('admin.dashboard');
    }
}
