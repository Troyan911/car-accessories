<?php

namespace Feature\Admin;

use App\Enums\Roles;
use App\Models\Product;
use App\Services\Contract\FileStorageServiceContract;
use Database\Seeders\AdminSeeder;
use Database\Seeders\ModeratorSeeder;
use Database\Seeders\PermissionsAndRolesSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery\MockInterface;
use Tests\TestCase;

class ProductsTest extends TestCase
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

    public static function productForms(): array
    {
        return [
            ['admin.products.create', false],
            ['admin.products.edit', true],
        ];
    }

    /**
     * @test
     *
     * @dataProvider productForms
     */
    public function test_product_form_contain_fields(string $route, bool $needParam)
    {
        $this->actingAsRole(Roles::ADMIN);

        $this->get(route($route, $needParam ? Product::factory()->create() : []))
            ->assertStatus(200)
            ->assertViewIs($route)
            ->assertSee('name="title"', false) // Check for the input
            ->assertSee('name="SKU"', false)
            ->assertSee('name="categories[]"', false)
            ->assertSee('name="description"', false)
            ->assertSee('name="price"', false)
            ->assertSee('name="new_price"', false)
            ->assertSee('name="quantity"', false)
            ->assertSee('name="thumbnail"', false);
    }

    /**
     * Routes data provider
     *
     * @return array[]
     */
    public static function productsViewsRoutes(): array
    {
        return [
            ['admin.products.index', false],
            ['admin.products.create', false],
            ['admin.products.edit', true],
        ];
    }

    /**
     * @test
     *
     * @dataProvider productsViewsRoutes
     */
    public function test_products_views_available_for_admin(string $routeName, bool $needParam)
    {
        $this->productsViewAvailableForRole(Roles::ADMIN, $routeName, $needParam);
    }

    /**
     * @test
     *
     * @dataProvider productsViewsRoutes
     */
    public function test_products_views_available_for_moderator(string $routeName, bool $needParam)
    {
        $this->productsViewAvailableForRole(Roles::MODERATOR, $routeName, $needParam);
    }

    /**
     * @return void
     */
    private function productsViewAvailableForRole(Roles $role, string $routeName, bool $needParam)
    {
        $products = Product::factory(2)->create();

        $this->actingAsRole($role)
            ->get($needParam
                ? route($routeName, Product::factory()->create())
                : route($routeName)
            )
            ->assertStatus(200)
            ->assertViewIs($routeName)
            ->assertSeeInOrder($products->pluck('name')->toArray());

        foreach ($products as $product) {
            $product->delete();
        }
    }

    /**
     * @test
     *
     * @dataProvider productsViewsRoutes
     */
    public function test_products_views_not_available_for_customer(string $routeName, bool $needParam)
    {
        $this->actingAsRole(Roles::CUSTOMER);
        $resp = $needParam
            ? $this->get(route($routeName, Product::factory()->create()))
            : $this->get(route($routeName));
        $resp->assertStatus(403);
    }

    public function test_product_create_with_valid_data(): void
    {
        $file = UploadedFile::fake()->image('test_image.png');
        $data = array_merge(
            Product::factory()->make()->toArray(),
            ['thumbnail' => $file]
        );

        $this->mock(
            FileStorageServiceContract::class,
            function (MockInterface $mock) {
                $mock->shouldReceive('upload')
                    ->andReturn('image_uploaded.png');
            }
        );

        $resp = $this->actingAsRole(Roles::ADMIN)
            ->post(route('admin.products.store'), $data)
            ->assertStatus(302)
            ->assertRedirectToRoute('admin.products.index');

        $this->assertDatabaseHas(Product::class, [
            'title' => $data['title'],
            'thumbnail' => 'image_uploaded.png',
        ]);
    }

    public function test_product_create_with_invalid_name(): void
    {
        $data = array_merge(
            Product::factory()->make()->toArray(),
            ['thumbnail' => UploadedFile::fake()->image('test_image.png')]
        );
        $data['title'] = 'q';

        $resp = $this->actingAsRole(Roles::ADMIN)
            ->post(route('admin.products.store'), $data)
            ->assertStatus(302)
//            ->assertRedirectToRoute('admin.products.index') //todo
            ->assertRedirectToRoute('home')
            ->assertSessionHasErrors([
                'title' => 'The title field must be at least 2 characters.',
            ]);

        $this->assertDatabaseMissing(Product::class, [
            'title' => $data['title'],
        ]);
    }

    public function test_product_update_with_valid_data()
    {
        $product = Product::factory()->create();

        $file = UploadedFile::fake()->image('test_image.png');
        $data = array_merge(
            Product::factory()->make()->toArray(),
            ['thumbnail' => $file]
        );

        $data['title'] .= '_new';

        $resp = $this->actingAsRole(Roles::ADMIN)
            ->patch(route('admin.products.update', $product), $data)
            ->assertStatus(302)
            ->assertRedirectToRoute('admin.products.edit', $product);

        $this->assertDatabaseHas(Product::class, [
            'title' => $data['title'],
        ]);
        $product->refresh();
        $this->assertDatabaseHas(Product::class, [
            'title' => $product->title,
        ]);
    }

    public function test_product_update_with_invalid_data()
    {
        $product = Product::factory()->create();

        $file = UploadedFile::fake()->image('test_image.png');
        $data = array_merge(
            Product::factory()->make()->toArray(),
            ['thumbnail' => $file]
        );

        $data['title'] = 'q';

        $resp = $this->actingAsRole(Roles::ADMIN)
            ->put(route('admin.products.update', $product), $data)
            ->assertStatus(302)
            ->assertRedirectToRoute('home')
            ->assertSessionHasErrors([
                'title' => 'The title field must be at least 2 characters.',
            ]);

        $this->assertDatabaseMissing(Product::class, [
            'title' => $data['title'],
        ]);
    }

    public function test_remove_existing_product()
    {
        $product = Product::factory()->create();
        $this->assertDatabaseHas(Product::class, ['id' => $product->id]);

        $this->actingAsRole(Roles::MODERATOR)
            ->delete(route('admin.products.destroy', $product))
            ->assertStatus(302)
            ->assertRedirectToRoute('admin.products.index');
        $this->assertDatabaseMissing(Product::class, ['id' => $product->id]);
    }

    public function test_remove_unexisting_product()
    {
        $product = Product::factory()->create();
        $product->id += 100;
        $this->assertDatabaseMissing(Product::class, ['id' => $product->id]);

        $this->actingAsRole(Roles::MODERATOR)
            ->delete(route('admin.products.destroy', $product))
            ->assertStatus(404);
        $product->refresh();
        $this->assertDatabaseHas(Product::class, ['id' => $product->id]);
    }
}
