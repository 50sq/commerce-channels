<?php

namespace Tests\Feature;

use FiftySq\Commerce\Data\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAdddressControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_new_address()
    {
        $customer = $this->setupCustomer();

        $this->actingAs($customer->user)
            ->postJson(route('commerce.addresses.store'), [
                'type' => Address::TYPE_BILLING,
                'address1' => '123 Maple Street',
                'city' => 'Mapleton',
                'region' => 'Ontario',
                'postal_code' => 'L9N V3D',
                'country' => 'CA',
            ])
            ->assertOk();

        $this->assertDatabaseHas('commerce_addresses', [
            'commerce_customer_id' => $customer->id,
            'address1' => '123 Maple Street',
            'city' => 'Mapleton',
            'region' => 'Ontario',
            'postal_code' => 'L9N V3D',
            'country' => 'CA',
            'is_default' => true,
        ]);
    }

    public function test_creates_new_address_and_replaces_existing_default()
    {
        $customer = $this->setupCustomer();

        $current_address = $customer->addresses()->create([
            'type' => Address::TYPE_BILLING,
            'address1' => '123 Maple Street',
            'city' => 'Mapleton',
            'region' => 'Ontario',
            'postal_code' => 'L9N V3D',
            'country' => 'CA',
            'is_default' => true,
        ]);

        $this->actingAs($customer->user)
            ->postJson(route('commerce.addresses.store'), [
                'type' => Address::TYPE_BILLING,
                'address1' => '123 New Avenue',
                'city' => 'Mapleton',
                'region' => 'Ontario',
                'postal_code' => 'L9N V3D',
                'country' => 'CA',
                'is_default' => true,
            ])
            ->assertOk();

        $this->assertDatabaseHas('commerce_addresses', [
            'commerce_customer_id' => $customer->id,
            'address1' => '123 Maple Street',
            'is_default' => false,
        ]);

        $this->assertDatabaseHas('commerce_addresses', [
            'commerce_customer_id' => $customer->id,
            'address1' => '123 New Avenue',
            'is_default' => true,
        ]);
    }
}
