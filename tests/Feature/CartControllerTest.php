<?php

namespace Tests\Feature;

use FiftySq\Commerce\Actions\CreateNewProduct;
use FiftySq\Commerce\Data\Models\Cart;
use FiftySq\Commerce\Data\Models\Customer;
use FiftySq\Commerce\Data\Product as ProductData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Fixtures\User;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->variant = app(CreateNewProduct::class)(
            new ProductData(
                'My Product',
                'blah blah',
                'item',
                500,
            )
        );

        $this->other_variant = app(CreateNewProduct::class)(
            new ProductData(
                'My Other Product',
                'blah blah',
                'item',
                1500,
            )
        );
    }

    public function test_get_cart_items()
    {
        $customer = Customer::create();
        $cart = $customer->cart()->create();

        $cart->items()->createMany([
            [
                'commerce_variant_id' => $this->variant->id,
                'commerce_product_id' => $this->variant->commerce_product_id,
                'quantity' => 4,
                'unit_price_cents' => 500,
            ],
            [
                'commerce_variant_id' => $this->other_variant->id,
                'commerce_product_id' => $this->other_variant->commerce_product_id,
                'quantity' => 1,
                'unit_price_cents' => 1500,
            ],
        ]);

        $this->getJson(route('commerce.cart.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'name' => $this->variant->product->title,
                'quantity' => 4,
                'unit_price_cents' => 500,
            ])
            ->assertJsonFragment([
                'name' => $this->other_variant->product->title,
                'quantity' => 1,
                'unit_price_cents' => 1500,
            ]);

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseCount('commerce_carts', 1);

        $this->assertDatabaseHas('commerce_customers', [
            'user_id' => null,
        ]);

        $this->assertDatabaseHas('commerce_carts', [
            'id' => $cart->id,
            'commerce_customer_id' => $customer->uuid,
        ]);
    }

    public function test_guest_add_new_item_to_cart()
    {
        $this->putJson(route('commerce.cart.add'), [
            'id' => $this->variant->id,
            'quantity' => 2,
            'unit_price' => 500,
        ])
            ->assertOk();

        $customer = Customer::first();
        $cart = Cart::first();

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseCount('commerce_carts', 1);
        $this->assertDatabaseCount('commerce_line_items', 1);

        $this->assertDatabaseHas('commerce_customers', [
            'user_id' => null,
        ]);

        $this->assertDatabaseHas('commerce_carts', [
            'commerce_customer_id' => $customer->uuid,
        ]);

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_cart_id' => $cart->id,
            'commerce_variant_id' => $this->variant->id,
            'quantity' => 2,
            'unit_price_cents' => 500,
        ]);
    }

    public function test_user_add_new_item_to_cart()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.test',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)->putJson(route('commerce.cart.add'), [
            'id' => $this->variant->id,
            'quantity' => 2,
            'unit_price' => 500,
        ])
            ->assertOk();

        $customer = Customer::first();
        $cart = Cart::first();

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseCount('commerce_carts', 1);
        $this->assertDatabaseCount('commerce_line_items', 1);

        $this->assertDatabaseHas('commerce_customers', [
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('commerce_carts', [
            'commerce_customer_id' => $customer->uuid,
        ]);

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_cart_id' => $cart->id,
            'commerce_variant_id' => $this->variant->id,
            'quantity' => 2,
            'unit_price_cents' => 500,
        ]);
    }

    public function test_guest_add_to_cart_then_add_as_auth_user()
    {
        // Add as guest...
        $this->putJson(route('commerce.cart.add'), [
            'id' => $this->variant->id,
            'quantity' => 2,
            'unit_price' => 500,
        ])
            ->assertOk();

        $customer = Customer::first();
        $cart = Cart::first();

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseCount('commerce_carts', 1);
        $this->assertDatabaseCount('commerce_line_items', 1);

        $this->assertDatabaseHas('commerce_customers', [
            'id' => $cart->id,
            'user_id' => null,
        ]);

        $this->assertDatabaseHas('commerce_carts', [
            'commerce_customer_id' => $customer->uuid,
        ]);

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_cart_id' => $cart->id,
            'commerce_variant_id' => $this->variant->id,
            'quantity' => 2,
            'unit_price_cents' => 500,
        ]);

        // Add as auth user...
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.test',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)->putJson(route('commerce.cart.add'), [
            'id' => $this->variant->id,
            'quantity' => 4,
            'unit_price' => 500,
        ])
            ->assertOk();

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseCount('commerce_carts', 1);
        $this->assertDatabaseCount('commerce_line_items', 1);

        $this->assertDatabaseHas('commerce_customers', [
            'id' => $cart->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('commerce_carts', [
            'commerce_customer_id' => $customer->uuid,
        ]);

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_cart_id' => $cart->id,
            'commerce_variant_id' => $this->variant->id,
            'quantity' => 6,
            'unit_price_cents' => 500,
        ]);
    }

    public function test_reduce_cart_quantity()
    {
        $customer = Customer::create();
        $cart = $customer->cart()->create();

        $items = $cart->items()->createMany([
            [
                'commerce_variant_id' => $this->variant->id,
                'commerce_product_id' => $this->variant->commerce_product_id,
                'quantity' => 4,
                'unit_price_cents' => 500,
            ],
            [
                'commerce_variant_id' => $this->other_variant->id,
                'commerce_product_id' => $this->other_variant->commerce_product_id,
                'quantity' => 1,
                'unit_price_cents' => 1500,
            ],
        ]);

        $this->putJson(route('commerce.cart.remove'), [
            'id' => $this->variant->id,
            'quantity' => 1,
        ])
            ->assertOk();

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseCount('commerce_carts', 1);

        $this->assertDatabaseHas('commerce_carts', [
            'id' => $cart->id,
            'commerce_customer_id' => $customer->uuid,
        ]);

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_variant_id' => $this->variant->id,
            'commerce_product_id' => $this->variant->commerce_product_id,
            'quantity' => 3,
            'unit_price_cents' => 500,
        ]);
    }

    public function test_reduce_cart_quantity_and_remove()
    {
        $customer = Customer::create();
        $cart = $customer->cart()->create();

        $items = $cart->items()->createMany([
            [
                'commerce_variant_id' => $this->variant->id,
                'commerce_product_id' => $this->variant->commerce_product_id,
                'quantity' => 4,
                'unit_price_cents' => 500,
            ],
            [
                'commerce_variant_id' => $this->other_variant->id,
                'commerce_product_id' => $this->other_variant->commerce_product_id,
                'quantity' => 1,
                'unit_price_cents' => 1500,
            ],
        ]);

        $this->putJson(route('commerce.cart.remove'), [
            'id' => $this->other_variant->id,
            'quantity' => 1,
        ])
            ->assertOk();

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseCount('commerce_carts', 1);

        $this->assertDatabaseHas('commerce_carts', [
            'id' => $cart->id,
            'commerce_customer_id' => $customer->uuid,
        ]);

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_variant_id' => $this->variant->id,
            'commerce_product_id' => $this->variant->commerce_product_id,
            'quantity' => 4,
            'unit_price_cents' => 500,
        ]);

        $this->assertDatabaseMissing('commerce_line_items', [
            'commerce_variant_id' => $this->other_variant->id,
        ]);
    }

    public function test_reduce_cart_quantity_for_item_not_in_cart()
    {
        $customer = Customer::create();
        $cart = $customer->cart()->create();

        $items = $cart->items()->createMany([
            [
                'commerce_variant_id' => $this->variant->id,
                'commerce_product_id' => $this->variant->commerce_product_id,
                'quantity' => 4,
                'unit_price_cents' => 500,
            ],
        ]);

        $this->putJson(route('commerce.cart.remove'), [
            'id' => $this->other_variant->id,
            'quantity' => 1,
        ])
            ->assertOk();

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseCount('commerce_carts', 1);

        $this->assertDatabaseHas('commerce_carts', [
            'id' => $cart->id,
            'commerce_customer_id' => $customer->uuid,
        ]);

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_variant_id' => $this->variant->id,
            'commerce_product_id' => $this->variant->commerce_product_id,
            'quantity' => 4,
            'unit_price_cents' => 500,
        ]);

        $this->assertDatabaseMissing('commerce_line_items', [
            'commerce_variant_id' => $this->other_variant->id,
        ]);
    }

    public function test_clear_cart()
    {
        $customer = Customer::create();
        $cart = $customer->cart()->create();

        $items = $cart->items()->createMany([
            [
                'commerce_variant_id' => $this->variant->id,
                'commerce_product_id' => $this->variant->commerce_product_id,
                'quantity' => 4,
                'unit_price_cents' => 500,
            ],
            [
                'commerce_variant_id' => $this->other_variant->id,
                'commerce_product_id' => $this->other_variant->commerce_product_id,
                'quantity' => 1,
                'unit_price_cents' => 1500,
            ],
        ]);

        $this->delete(route('commerce.cart.clear'))
            ->assertOk();

        $this->assertDatabaseCount('commerce_customers', 1);
        $this->assertDatabaseCount('commerce_carts', 1);
        $this->assertDatabaseCount('commerce_line_items', 0);

        $this->assertDatabaseHas('commerce_carts', [
            'id' => $cart->id,
            'commerce_customer_id' => $customer->uuid,
        ]);

        $this->assertDatabaseMissing('commerce_line_items', [
            'commerce_variant_id' => $this->variant->id,
        ]);

        $this->assertDatabaseMissing('commerce_line_items', [
            'commerce_variant_id' => $this->other_variant->id,
        ]);
    }
}
