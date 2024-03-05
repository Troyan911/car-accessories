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


    /**
     * @var array|mixed[]
     */
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

    /**
     * incorrectData data provider
     * @return array[]
     */
    public static function incorrectRegisterData(): array
    {
        $errNameMax50Chars = 'The name field must not be greater than 50 characters.';
        $errSurNameMax50Chars = 'The surname field must not be greater than 50 characters.';

        $errEmailMax255Chars = 'The email field must not be greater than 255 characters.';
        $errEmailValid = 'The email field must be a valid email address.';       //.@i

        $errPhoneLen = 'The phone field must not be greater than 15 characters.';
        $errBirthDate = 'The birthdate field must be a date before or equal to -18 years.';

        $errPasswordMinLength = 'The password field must be at least 8 characters.';
        $errPasswordDoesntMatch = 'The password field confirmation does not match.';

        $errEmailUnique = 'The email has already been taken.';
        $errPhoneUnique = 'The phone has already been taken.';

//            'name' => ['required', 'string', 'max:50'],
//            'surname' => ['required', 'string', 'max:50'],
//            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
//            'phone' => ['required', 'string', 'max:15', 'unique:'.User::class], //new Phone],
//            'birthdate' => ['required', 'date', 'before_or_equal:-18 years'],
//            'password' => ['required', 'confirmed', Password::defaults()],

        return [
//            ['name', fake()->regexify('[A-Z0-9]{55}'), $errNameMax50Chars],
//            ['surname', fake()->regexify('[A-Z0-9]{55}'), $errSurNameMax50Chars],
//
//            ['email', fake()->regexify('[A-Z0-9]{255}') . "@co.uk", $errEmailMax255Chars],
//            ['email', ".@i", $errEmailValid],
//
//            ['phone', "+" . fake()->regexify('[0-9]{20}'), $errPhoneLen],
//            ['birthdate', fake()->dateTimeBetween('-17 years', '-15 years')->format('Y-m-d'), $errBirthDate],
//
//            ['password', fake()->password(5,7), $errPasswordMinLength],

//            ['password_confirmation', fake()->text(7), $errPasswordDoesntMatch],

            ['email', null, $errEmailUnique, true],
            ['phone', null, $errPhoneUnique, true],
        ];
    }

    /**
     * @test
     * @dataProvider incorrectRegisterData
     */
    public function test_fail_registration(string $field, string|null $value, string $errorMessage, bool $checkExists): void
    {
        if ($checkExists) {
            $existingUser = app()->make(User::class)->factory()->create()->toArray();
        }

        $user = User::factory()->make()->toArray();
        $user['password'] = 'testPass';
        $user['password_confirmation'] = 'testPass';
        $user[$field] = !$checkExists ? $value : $existingUser[$field];

//        dd($user, $existingUser);

        $resp = $this->post(route('register'), $user)
            ->assertStatus(302)

//        dd($user, $field, $errorMessage, $resp);

//        ->assertRedirectToRoute('register') //todo redirect
            ->assertSessionHasErrors([
                $field => $errorMessage
            ]);

        if(!$checkExists) {
            $this->assertDatabaseMissing(User::class, ['email' => $user['email']]);
        }
    }


    public function test_success_registration(): void
    {
        $user = User::factory()
            ->make()
            ->toArray();

        $user['password'] = 'qwerty12';
        $user['password_confirmation'] = 'qwerty12';

        $resp = $this->post(route('register'), $user);
        $resp->assertStatus(302);

        $resp = $this->get(route('home'));
        $resp->assertStatus(200)
            ->assertViewIs('home')
            ->assertSeeText('You are logged in!');

        $loginedUser = User::where('email', $user['email'])->get();

        $this->assertDatabaseHas(User::class, [
            'name' => $user['name'],
            'email' => $user['email'],
        ]);
    }

}
