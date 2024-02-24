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
use Mockery;
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

    public function test_create_product(): void
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
            ->post(route('admin.products.store'), $data);

        $resp->assertStatus(302);
        $resp->assertRedirectToRoute('admin.products.index');
        $this->assertDatabaseHas(Product::class, [
            'title' => $data['title'],
            'thumbnail' => 'image_uploaded.png'
        ]);
    }


    /**
     * Routes data provider
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
     * @dataProvider productsViewsRoutes
     */
    public function test_products_views_available_for_admin(string $routeName, bool $needParam)
    {
        $this->productsViewAvailableForRole(Roles::ADMIN, $routeName, $needParam);
    }

    /**
     * @test
     * @dataProvider productsViewsRoutes
     */
    public function test_products_views_available_for_moderator(string $routeName, bool $needParam)
    {
        $this->productsViewAvailableForRole(Roles::MODERATOR, $routeName, $needParam);
    }

    /**
     * @param Roles $role
     * @param string $routeName
     * @param bool $needParam
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

        foreach ($products as $product){
            $product->delete();
        }
    }

    /**
     * @test
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
}
