<?php

namespace Tests\Unit\Http\Middleware;

use FiftySq\Commerce\Data\Models\Customer;
use FiftySq\Commerce\Http\Middleware\ResolveCustomer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Tests\Fixtures\User;
use Tests\TestCase;

class ResolveSessionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', ResolveCustomer::class])->get('/cart', function () {
            return Response::json([]);
        })->name('cart.test');

        Route::middleware(['api', ResolveCustomer::class])->get('/api/cart', function () {
            return Response::json([]);
        })->name('cart.api.test');
    }

    public function test_creates_new_customer()
    {
        $uuid = $this->mockUuid();

        $response = $this->getJson(route('cart.test'))
            ->assertSessionHas('commerce_customer_id', $uuid);

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseHas('commerce_customers', [
            'uuid' => $uuid,
        ]);
    }

    public function test_uses_session_customer_id()
    {
        $customer = Customer::create();

        $this->withSession(['commerce_customer_id', $customer->uuid])
            ->getJson(route('cart.test'))
            ->assertSessionHas('commerce_customer_id', $customer->uuid);

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseHas('commerce_customers', [
            'uuid' => $customer->uuid,
        ]);
    }

    public function test_uses_auth_user_id_to_find_customer()
    {
        $user = new User(['id' => 1234]);

        $customer = Customer::create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->getJson(route('cart.test'))
            ->assertSessionHas('commerce_customer_id', $customer->uuid);

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseHas('commerce_customers', [
            'uuid' => $customer->uuid,
        ]);
    }

    public function test_uses_auth_user_id_to_create_customer()
    {
        $uuid = $this->mockUuid();
        $user = new User(['id' => 1234]);

        $this->actingAs($user)
            ->getJson(route('cart.test'))
            ->assertSessionHas('commerce_customer_id', $uuid);

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseHas('commerce_customers', [
            'user_id' => $user->id,
            'uuid' => $uuid,
        ]);
    }

    public function test_uses_auth_token_for_new_customer()
    {
        $user = new User(['id' => 1234]);

        $this->actingAs($user, 'api')
            ->getJson(route('cart.api.test'))
            ->assertSessionMissing('commerce_customers');

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseHas('commerce_customers', [
            'user_id' => $user->id,
        ]);
    }

    public function test_uses_auth_token_for_existing_customer()
    {
        $user = new User(['id' => 1234]);

        $customer = Customer::create(['user_id' => $user->id]);

        $this->actingAs($user, 'api')
            ->getJson(route('cart.api.test'))
            ->assertSessionMissing('commerce_customers');

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseHas('commerce_customers', [
            'uuid' => $customer->uuid,
            'user_id' => $user->id,
        ]);
    }

    protected function mockUuid()
    {
        $stringUuid = Str::uuid();

        $uuid = Uuid::fromString($stringUuid);

        $factoryMock = \Mockery::mock(UuidFactory::class.'[uuid4]', [
            'uuid4' => $uuid,
        ]);

        Uuid::setFactory($factoryMock);

        return $stringUuid;
    }
}
