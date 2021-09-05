<?php

namespace Tests;

use FiftySq\Commerce\CommerceServiceProvider;
use FiftySq\Commerce\Generators\OrderIdGenerator;
use FiftySq\Commerce\Generators\SkuGenerator;
use Illuminate\Container\Container;
use Mockery;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Fixtures\Product;
use Tests\Fixtures\ProductResource;
use Tests\Fixtures\User;

abstract class TestCase extends BaseTestCase
{
    use ProvidesFixtures;

    public function setUp() : void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
        $this->artisan('migrate', ['--database' => 'testing']);

        $this->loadMigrationsFrom(__DIR__.'/../src/database/migrations');
        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Illuminate\Session\Middleware\StartSession');

        $app['migrator']->path(__DIR__.'/../database/migrations');

        $app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        $app['config']->set('queue.default', 'sync');
        $app['config']->set('session.driver', 'array');

        $this->setupCommerce($app);
    }

    protected function getPackageProviders($app)
    {
        return [CommerceServiceProvider::class];
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    protected function setupCommerce(Container $app)
    {
        $app['config']->set('commerce.models.customers', User::class);
        $app['config']->set('commerce.table_prefix', 'commerce');

        $app['commerce']->useSellableModel(Product::class);
        $app['commerce']->renderProductsWith(ProductResource::class);
        $app['commerce']->generateSkusUsing(SkuGenerator::class);
        $app['commerce']->generateOrderIds(OrderIdGenerator::class);
    }
}
