<?php

namespace Tests\Feature\Admin;

use App\Enums\Roles;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Models\Category;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CategoryProductSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ModeratorSeeder;
use Database\Seeders\PermissionsAndRolesSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return void
     */
    protected function afterRefreshingDatabase()
    {
        $this->seed(DatabaseSeeder::class);
//        $this->seed(UsersSeeder::class);
//        $this->seed(ModeratorSeeder::class);
//        $this->seed(AdminSeeder::class);
//        $this->seed(CategoryProductSeeder::class);

//todo failed 6 tests
//        $this->withoutExceptionHandling();
    }

    /**
     * Routes data provider
     * @return array[]
     */
    public static function categoriesViewsRoutes(): array
    {
        return [
            ['admin.categories.index', false],
            ['admin.categories.create', false],
            ['admin.categories.edit', true],
        ];
    }

    /**
     * @test
     * @dataProvider categoriesViewsRoutes
     */
    public function test_categories_views_available_for_admin(string $routeName, bool $needParam)
    {
        $this->categoryViewAvailableForRole(Roles::ADMIN, $routeName, $needParam);
    }

    /**
     * @test
     * @dataProvider categoriesViewsRoutes
     */
    public function test_categories_views_available_for_moderator(string $routeName, bool $needParam)
    {
        $this->categoryViewAvailableForRole(Roles::MODERATOR, $routeName, $needParam);
    }

    /**
     * @param Roles $role
     * @param string $routeName
     * @param bool $needParam
     * @return void
     */
    private function categoryViewAvailableForRole(Roles $role, string $routeName, bool $needParam)
    {
        $categories = Category::factory(2)->create();

        $this->actingAsRole($role)
            ->get($needParam
                ? route($routeName, Category::factory()->create())
                : route($routeName)
            )
            ->assertStatus(200)
            ->assertViewIs($routeName)
            ->assertSeeInOrder($categories->pluck('name')->toArray());
    }

    /**
     * @test
     * @dataProvider categoriesViewsRoutes
     */
    public function test_categories_views_not_available_for_customer(string $routeName, bool $needParam)
    {
        $this->actingAsRole(Roles::CUSTOMER);
        $resp = $needParam
            ? $this->get(route($routeName, Category::factory()->create()))
            : $this->get(route($routeName));
        $resp->assertStatus(403);
    }

    /**
     * @test
     * @return void
     */
    public function test_category_create_with_valid_data()
    {
//        $category = Category::factory()->create();
        $category = Category::factory()->make();
        $this->actingAsRole(Roles::MODERATOR)
//            ->post(route('admin.categories.store', $category))
            ->post(route('admin.categories.store'), $category->toArray())
            ->assertStatus(302)
            ->assertRedirectToRoute('admin.categories.index');
//            ->assertViewIs('admin.categories.index');
        $this->assertDatabaseHas(Category::class, ['name' => $category->name]);
    }

    /**
     * @return void
     */
    public function test_category_create_with_invalid_data()
    {
        $data = ['name' => 'a', 'parent_id' => 0];
        $this->actingAsRole(Roles::MODERATOR)
            ->post(route('admin.categories.store'), $data)
            ->assertStatus(302)
            ->assertSessionHasErrors([
                    'name' => (new CreateCategoryRequest)->messages()['name.min'],
                    'parent_id' => 'The selected parent id is invalid.'
                ]
            //todo doesn't work
            //['name' => app(CreateCategoryRequest::class)->messages()['name.min']]
            );
        $this->assertDatabaseMissing(Category::class, $data);
    }

    public function test_category_update_with_valid_data()
    {
        $category = Category::factory()->create();

        $category->name .= '_new';
        $parent = Category::factory()->create();

        $this->actingAsRole(Roles::MODERATOR)
            ->put(route('admin.categories.update', $category), [
                'name' => $category->name,
                'parent_id' => $parent->id
            ])
            ->assertStatus(302)
            ->assertRedirectToRoute('admin.categories.index');
        $this->assertDatabaseHas(Category::class, [
            'name' => $category->name,
            'parent_id' => $parent->id
        ]);
        $category->refresh();
        $this->assertEquals($category->parent_id, $parent->id);
    }

    public function test_category_update_with_invalid_data()
    {
        $parent = Category::factory()->create();
        $category = Category::factory()->set('parent_id', $parent->id)->create();

        $resp = $this->actingAsRole(Roles::MODERATOR)
            ->put(route('admin.categories.update', $category), [
                'name' => 'n',
                'parent_id' => 0
            ]);

        $resp->assertStatus(302);
//        $resp->assertRedirectToRoute('admin.categories.index');
        $resp->assertSessionHasErrors([
                'name' => 'The name field must be at least 2 characters.',
                'parent_id' => 'The selected parent id is invalid.'
            ]
        );

        $this->assertDatabaseHas(Category::class, [
            'name' => $category->name,
            'parent_id' => $category->parent_id
        ]);
        $this->assertEquals($category->parent_id, $parent->id);
    }

    public function test_remove_existing_category()
    {
        $category = Category::factory()->create();
        $this->assertDatabaseHas(Category::class, ['id' => $category->id]);

        $this->actingAsRole(Roles::MODERATOR)
            ->delete(route('admin.categories.destroy', $category))
            ->assertStatus(302)
            ->assertRedirectToRoute('admin.categories.index');
        $this->assertDatabaseMissing(Category::class, ['id' => $category->id]);
    }

    public function test_remove_unexisting_category()
    {
        $category = Category::factory()->create();
        $category->id = 0;
        $this->assertDatabaseMissing(Category::class, ['id' => $category->id]);

        $this->actingAsRole(Roles::MODERATOR)
            ->delete(route('admin.categories.destroy', $category))
            ->assertStatus(404);
        $category->refresh();
        $this->assertDatabaseHas(Category::class, ['id' => $category->id]);
    }
}
