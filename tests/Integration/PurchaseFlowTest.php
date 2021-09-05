<?php

namespace Tests\Integration;

use FiftySq\Commerce\Channels\PrintfulChannel;
use FiftySq\Commerce\Channels\Webhooks\OrderUpdated;
use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Models\Address;
use FiftySq\Commerce\Data\Models\Customer;
use FiftySq\Commerce\Data\Models\Discount;
use FiftySq\Commerce\Data\Models\Order as OrderModel;
use FiftySq\Commerce\Data\Models\PendingOrder;
use FiftySq\Commerce\Data\Models\Variant;
use FiftySq\Commerce\Data\Order;
use FiftySq\Commerce\Payments\Actions\CompleteCheckout;
use FiftySq\Commerce\Payments\Webhooks\CheckoutSessionCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Stripe\Event;
use Stripe\StripeClient;
use Tests\Fixtures\User;
use Tests\TestCase;

class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    protected User $user;
    protected Discount $discount;

    protected string $printfulKey = 'MY-UNIQUE-KEY-FOR-PRINTFUL';

    protected string $stripeKey = 'pktest-MY-UNIQUE-KEY-FOR-STRIPE';
    protected string $stripeWebhookToken = 'pktest-MY-WEBHOOK-TOKEN';

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->customer = $this->setupCustomer();
        $this->user = $this->customer->user;

        $this->discount = Discount::create([
            'code' => 'LAUNCH2021',
            'type' => Discount::TYPE_PERCENTAGE,
            'amount' => 15,
        ]);

        Commerce::checkoutView(function () {
            return 'This is the checkout';
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('commerce.channels.default', 'printful');
        $app['config']->set('commerce.channels.drivers.printful.key', $this->printfulKey);

        $app['config']->set('commerce.gateways.default', 'stripe');
        $app['config']->set('commerce.gateways.drivers.stripe.key', $this->stripeKey);
        $app['config']->set('commerce.gateways.drivers.stripe.webhook_token', $this->stripeWebhookToken);

        parent::getEnvironmentSetUp($app);
    }

    public function testCartToOrderToCheckoutToFulfillment()
    {
        $variant1 = $this->createProduct('Clown Wig', 1200);
        $variant2 = $this->createProduct('Clown Nose', 650);

        $this->actingAs($this->user);

        // 1. Add products to cart

        $this->putJson(route('commerce.cart.add'), [
            'id' => $variant1->id,
            'quantity' => 1,
        ])->assertOk();

        $this->assertDatabaseHas('commerce_carts', [
            'commerce_customer_id' => $this->customer->uuid,
            'commerce_order_id' => null,
        ]);

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_variant_id' => $variant1->id,
            'commerce_product_id' => $variant1->commerce_product_id,
            'quantity' => 1,
            'unit_price_cents' => $variant1->unit_price_cents,
        ]);

        $this->putJson(route('commerce.cart.add'), [
            'id' => $variant2->id,
            'quantity' => 2,
            'unit_price' => $variant2->unit_price_cents,
        ])->assertOk();

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_variant_id' => $variant2->id,
            'commerce_product_id' => $variant2->commerce_product_id,
            'quantity' => 2,
            'unit_price_cents' => $variant2->unit_price_cents,
        ]);

        // 2. Get cart

        $this->getJson(route('commerce.cart.index'))
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Clown Nose',
                'quantity' => 2,
            ])
            ->assertJsonFragment([
                'name' => 'Clown Wig',
                'quantity' => 1,
            ]);

        // 3. Update pending order with shipping and billing addresses

        $this->postJson(route('commerce.addresses.store'), [
            'type' => Address::TYPE_BILLING,
            'is_default' => true,
            'address1' => '123 Maple Street',
            'city' => 'Townsville',
            'region' => 'AL',
            'postal_code' => 35801,
            'country' => 'US',
        ])->assertOk();

        $this->postJson(route('commerce.addresses.store'), [
            'type' => Address::TYPE_SHIPPING,
            'same_as_billing' => true,
        ])->assertOk();

        $billingAddress = $this->customer->addresses()->where('type', Address::TYPE_BILLING)->first();
        $shippingAddress = $this->customer->addresses()->where('type', Address::TYPE_SHIPPING)->first();

        $this->assertDatabaseHas('commerce_addresses', [
            'id' => $billingAddress->id,
            'commerce_customer_id' => $this->customer->id,
            'type' => Address::TYPE_BILLING,
            'is_default' => true,
            'address1' => '123 Maple Street',
            'city' => 'Townsville',
            'region' => 'AL',
            'postal_code' => 35801,
            'country' => 'US',
        ]);

        $this->assertDatabaseHas('commerce_addresses', [
            'id' => $shippingAddress->id,
            'commerce_customer_id' => $this->customer->id,
            'type' => Address::TYPE_SHIPPING,
            'is_default' => true,
            'address1' => '123 Maple Street',
            'city' => 'Townsville',
            'region' => 'AL',
            'postal_code' => 35801,
            'country' => 'US',
        ]);

        // 4. Preview order costs with taxes, shipping
        // TODO: This should provide the pending order and items to the view.

        $this->get(route('commerce.checkout.index'))
            ->assertSee('This is the checkout');

        $pendingOrder = PendingOrder::query()->first();

        // 5. Update quantity of an item.

        $this->putJson(route('commerce.checkout.update'), [
            'line_items' => [
                [
                    'id' => $variant2->id,
                    'quantity' => 3,
                ],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('commerce_line_items', [
            'commerce_variant_id' => $variant2->id,
            'commerce_product_id' => $variant2->commerce_product_id,
            'quantity' => 3,
            'unit_price_cents' => $variant2->unit_price_cents,
        ]);

        // 6. Update with billing and shipping addresses

        $this->putJson(route('commerce.checkout.update'), [
            'billing_address_id' => $billingAddress->id,
            'shipping_address_id' => $shippingAddress->id,
        ])->assertOk();

        $this->assertDatabaseHas('commerce_carts', [
            'commerce_billing_address_id' => $billingAddress->id,
            'commerce_shipping_address_id' => $shippingAddress->id,
        ]);

        // 7. Preview updated order costs with taxes, shipping
        // TODO: This should provide the pending order and items to the view.

//        $this->get(route('commerce.checkout.index'))
//            ->assertSee('This is the checkout');

        // 8. Apply a discount (coupon code)

        $this->putJson(route('commerce.checkout.update'), [
            'coupon_code' => 'LAUNCH2021',
        ])->assertOk();

        $this->assertDatabaseHas('commerce_discount_order', [
            'commerce_discount_id' => $this->discount->id,
            'commerce_order_id' => $pendingOrder->id,
        ]);

        // 9. Preview updated order costs with taxes, shipping, plus discount
        // TODO: This should provide the pending order and items to the view.

//        $this->get(route('commerce.checkout.index'))
//            ->assertSee('This is the checkout');

        // 10. Create a Stripe Checkout

        Auth::logout();

        $this->createStripeCheckoutMock();

        $this->postJson(route('commerce.checkout.session.store'), [
            'coupon' => $pendingOrder->discounts->first()->code,
        ])
            ->assertOk()
            ->assertJson(['id' => 'cs_test_iCNZun5b8oNK1Ux6wWvVR6E4XYvF7eZ77qZG5mtdeheywi0aHOMVMcLx']);

        // 11. Receive successful payment

        // Acting as Stripe... we will respond with a successful payment
        // that will trigger order placement in Printful.s

        $this->createPrintfulOrderMock();

        $action = new CompleteCheckout();

        $webhook = new CheckoutSessionCompleted(
            Event::CHECKOUT_SESSION_COMPLETED,
            'stripe',
            'cs_test_iCNZun5b8oNK1Ux6wWvVR6E4XYvF7eZ77qZG5mtdeheywi0aHOMVMcLx',
            $pendingOrder->getTotal(),
            'paid',
        );

        $webhook->handle($action);

        $this->assertDatabaseHas('commerce_orders', [
            'gateway' => 'stripe',
            'gateway_checkout_id' => 'cs_test_iCNZun5b8oNK1Ux6wWvVR6E4XYvF7eZ77qZG5mtdeheywi0aHOMVMcLx',
            'payment_status' => 'paid',
            'channel' => 'printful',
            'channel_order_id' => 'abc-1234',
            'order_status' => OrderModel::STATUS_PENDING,
        ]);

        // 12. Receive order confirmation
        // Acting as Printful...

        $webhook = new OrderUpdated(
            'order_updated',
            'printful',
            'abc-1234',
            OrderModel::STATUS_FULFILLED,
        );

        $webhook->handle();

        $this->assertDatabaseHas('commerce_orders', [
            'gateway' => 'stripe',
            'gateway_checkout_id' => 'cs_test_iCNZun5b8oNK1Ux6wWvVR6E4XYvF7eZ77qZG5mtdeheywi0aHOMVMcLx',
            'payment_status' => 'paid',
            'channel' => 'printful',
            'channel_order_id' => 'abc-1234',
            'order_status' => OrderModel::STATUS_FULFILLED,
        ]);

        // 13. Return final order details

        $order = OrderModel::first();

        $this->actingAs($this->user);

        $this->getJson(route('commerce.orders.show', $order->id))
            ->assertOk()
            ->assertJson([
                'id' => $order->id,
                'public_order_id' => $order->public_order_id,
                'gateway' => 'stripe',
                'gateway_checkout_id' => 'cs_test_iCNZun5b8oNK1Ux6wWvVR6E4XYvF7eZ77qZG5mtdeheywi0aHOMVMcLx',
                'payment_status' => 'paid',
                'channel' => 'printful',
                'channel_order_id' => 'abc-1234',
                'order_status' => OrderModel::STATUS_FULFILLED,
            ]);
    }

    protected function createProduct($title, $unit_price_cents): Variant
    {
        $external_id = Str::random(6);

        $product = new \Tests\Fixtures\Product([
            'title' => $title,
            'is_enabled' => true,
            'is_shippable' => true,
            'is_taxable' => true,
            'channel' => 'printful',
            'external_channel_id' => $external_id,
        ]);

        $product->save();

        $variant = $product->variants()->create([
            'unit_price_cents' => $unit_price_cents,
            'min_quantity' => 1,
            'external_channel_id' => $external_id.'-'.Str::random(1),
        ]);

        return $variant;
    }

    protected function createStripeCheckoutMock()
    {
        $this->partialMock(StripeClient::class, function ($mock) {
            $mock->shouldReceive('request')->once()->andReturn([
                    'id' => 'cs_test_iCNZun5b8oNK1Ux6wWvVR6E4XYvF7eZ77qZG5mtdeheywi0aHOMVMcLx',
                    'object' => 'checkout.session',
                    'allow_promotion_codes' => null,
                    'amount_subtotal' => null,
                    'amount_total' => null,
                    'automatic_tax' => [
                        'enabled' => false,
                        'status' => null,
                    ],
                    'billing_address_collection' => null,
                    'cancel_url' => route('commerce.checkout.session.cancelled'),
                    'client_reference_id' => null,
                    'currency' => null,
                    'customer' => null,
                    'customer_details' => null,
                    'customer_email' => null,
                    'livemode' => false,
                    'locale' => null,
                    'metadata' => [],
                    'mode' => 'payment',
                    'payment_intent' => 'pi_1DoYvs2eZvKYlo2CXLOFBGXi',
                    'payment_method_options' => [],
                    'payment_method_types' => [
                        'card',
                    ],
                    'payment_status' => 'unpaid',
                    'setup_intent' => null,
                    'shipping' => null,
                    'shipping_address_collection' => null,
                    'submit_type' => null,
                    'subscription' => null,
                    'success_url' => route('commerce.checkout.session.success'),
                    'total_details' => null,
                    'url' => null,
                ]
            );
        });
    }

    protected function createPrintfulOrderMock()
    {
        $order = new Order('printful', 1, [], OrderModel::STATUS_PENDING);

        $order->setExternalOrderId('abc-1234');

        $this->mock(PrintfulChannel::class, function ($mock) use ($order) {
            $mock->shouldReceive('createOrder')
                ->once()
                ->andReturn($order);
        });
    }
}
