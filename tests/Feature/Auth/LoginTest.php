<?php

namespace Tests\Feature\Auth;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ModeratorSeeder;
use Database\Seeders\PermissionsAndRolesSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->errCredsDidntMatch = 'These credentials do not match our records.';
        $this->youAreLoggedIn = 'You are logged in!';
    }

    /**
     * fieldsList data provider
     * @return array[]
     */
    public static function fieldsList(): array
    {
        return [
            ['email'],
            ['password'],
        ];
    }

    /**
     * @test
     * @dataProvider fieldsList
     */
    public function test_check_fields_exists(string $fieldName)
    {
        $this->get(route('login'))
            ->assertStatus(200)
            ->assertViewIs('auth.login')
            ->assertSee('name="' . $fieldName .'"', false); // Check for the input
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
        $resp->assertStatus(302)
            ->assertRedirectToRoute('home');

        $resp = $this->assertAuthenticatedAs($user)
            ->assertDatabaseHas(User::class, ['email' => $user['email']])
            ->get(route('home'));

//        $resp->dump();

        $resp->assertStatus(200)
            ->assertViewIs('home')
            ->assertSeeText($this->youAreLoggedIn);
    }

    public function test_failed_login_incorrect_email(): void
    {
        $user = User::factory()->create()->syncRoles(Roles::MODERATOR);
        $user['email'] .= 12;

        $resp = $this->post(route('login'), [
            'email' => $user['email'],
            'password' => 'qwerty12'
        ])
            ->assertStatus(302)
//            ->assertRedirectToRoute('login') //todo
            ->assertRedirectToRoute('home')
            ->assertSessionHasErrors([
                'email' => $this->errCredsDidntMatch
            ]);

//        $resp->dump();
//        dd($resp);

        $this->assertDatabaseMissing(User::class, ['email' => $user['email']]);
    }

    public function test_failed_login_incorrect_password(): void
    {
        $user = User::factory()->create()->syncRoles(Roles::MODERATOR);

        $resp = $this->post(route('login'), [
            'email' => $user['email'],
            'password' => 'qwerty456'
        ])
            ->assertStatus(302)
//            ->assertRedirectToRoute('login'); //todo
            ->assertRedirectToRoute('home')
            ->assertSessionHasErrors([
                'email' => $this->errCredsDidntMatch
            ]);
        $this->assertDatabaseHas(User::class, ['email' => $user['email']]);


    }

    /**
     * userRoles data provider
     * @return array[]
     */
    public static function userRoles(): array
    {
        return [
            [Roles::ADMIN, true],
            [Roles::MODERATOR, true],
            [Roles::CUSTOMER, false],
        ];
    }

    /**
     * @test
     * @dataProvider userRoles
     */
    public function test_admin_available_for_roles(Roles $role, bool $isAvailable)
    {
        $resp = $this->actingAsRole($role)
            ->get(route('admin.dashboard'))
            ->assertStatus($isAvailable ? 200 : 403);

        !$isAvailable ?? $resp->assertViewIs('admin.dashboard');
    }
}
