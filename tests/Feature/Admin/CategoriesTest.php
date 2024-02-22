<?php

namespace Tests\Feature\Admin;

use App\Enums\Roles;
use App\Http\Requests\Categories\CreateCategoryRequest;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CategoryProductSeeder;
use Database\Seeders\ModeratorSeeder;
use Database\Seeders\PermissionsAndRolesSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoriesTest extends TestCase
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

//        $this->seed(CategoryProductSeeder::class);
    }

    public function test_healthcheck(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_categories_visible_for_role_moderator()
    {
        $categories = Category::factory(2)->create();
        $resp = $this->actingAsRole(Roles::MODERATOR)
            ->get(route('admin.categories.index'));

        $resp->assertStatus(200);
        $resp->assertViewIs('admin.categories.index');
        $resp->assertSeeInOrder($categories->pluck('name')->toArray());
    }

    public function test_categories_visible_for_role_admin()
    {
        $categories = Category::factory(2)->create();
        $resp = $this->actingAsRole(Roles::ADMIN)
            ->get(route('admin.categories.index'));

        $resp->assertStatus(200);
        $resp->assertViewIs('admin.categories.index');
        $resp->assertSeeInOrder($categories->pluck('name')->toArray());
    }

    public function test_categories_does_not_visible_for_role_customer()
    {
        $categories = Category::factory(2)->create();
        $resp = $this->actingAsRole(Roles::CUSTOMER)
            ->get(route('admin.categories.index'));

        $resp->assertStatus(403);
    }

    public function test_category_create_with_valid_data()
    {
        $data = Category::factory()->make()->toArray();
        $resp = $this->actingAsRole(Roles::MODERATOR)
            ->post(route('admin.categories.store'), $data);

        $resp->assertStatus(302);
        $resp->assertRedirectToRoute('admin.categories.index');
        $this->assertDatabaseHas(Category::class, ['name' => $data['name']]);
    }

    public function test_category_create_with_invalid_data()
    {
        $data = ['name' => 'a'];

        $resp = $this->actingAsRole(Roles::MODERATOR)
            ->post(route('admin.categories.store'), $data);

        $resp->assertStatus(302);
        $resp->assertSessionHasErrors(
            ['name' => (new CreateCategoryRequest)->messages()['name.min']]
            //todo doesn't work
            //['name' => app(CreateCategoryRequest::class)->messages()['name.min']]

        );
        $this->assertDatabaseMissing(Category::class, ['name' => $data['name']]);
    }

    //todo negative, view check, validation
    public function test_category_update_with_valid_data()
    {
        $category = Category::factory()->create();
        $category->name = 'new_' . $category->name;
        $parent = Category::factory()->create();

        $resp = $this->actingAsRole(Roles::MODERATOR)
            ->put(route('admin.categories.update', $category), [
                'name' => $category->name,
                'parent_id' => $parent->id
            ]);

        $resp->assertStatus(302);
        $resp->assertRedirectToRoute('admin.categories.index');
        $this->assertDatabaseHas(Category::class, ['parent_id' => $parent->id]);
        $category->refresh();
        $this->assertEquals($category->parent_id, $parent->id);
    }

    public function test_remove_category() {
        $category = Category::factory()->create();
        $this->assertDatabaseHas(Category::class, ['id' => $category->id]);

        $resp = $this->actingAsRole(Roles::MODERATOR)
            ->delete(route('admin.categories.destroy', $category));
        $resp->assertStatus(302);
        $resp->assertRedirectToRoute('admin.categories.index');
        $this->assertDatabaseMissing(Category::class, ['id' => $category->id]);
    }


    protected function getUser(Roles $role): User
    {
        return User::role($role->value)->firstOrFail();
    }

    protected function actingAsRole(Roles $role) {
        return $this->actingAs($this->getUser($role));
    }

}
