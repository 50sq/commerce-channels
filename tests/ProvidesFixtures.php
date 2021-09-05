<?php

namespace Tests;

use FiftySq\Commerce\Data\Models\Cart;
use FiftySq\Commerce\Data\Models\Customer;
use FiftySq\Commerce\Data\Models\PendingOrder;
use Illuminate\Support\Facades\Hash;
use Tests\Fixtures\User;

trait ProvidesFixtures
{
    protected User $user;
    protected Customer $customer;
    protected Cart $cart;
    protected PendingOrder $pending_order;

    /**
     * @return User
     */
    protected function setupUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'test@test.test',
            'password' => Hash::make('password'),
        ]);
    }

    /**
     * @param  null  $user
     * @return Customer
     */
    protected function setupCustomer($user = null): Customer
    {
        if (! $user) {
            $user = $this->setupUser();
        }

        return Customer::create([
            'user_id' => $user->id,
        ]);
    }

    /**
     * @param  null  $customer
     */
    protected function setupCart($customer = null): void
    {
        if (! $customer) {
            $user = $this->setupUser();

            $customer = $this->setupCustomer($user);
        } else {
            $user = $customer->user;
        }

        $cart = $customer->cart()->create();

        $this->user = $user;
        $this->customer = $customer;
        $this->cart = $cart;
    }

    /**
     * @param  null  $customer
     */
    protected function setupPendingOrder($customer = null): void
    {
        if (! $customer) {
            $user = $this->setupUser();

            $customer = $this->setupCustomer($user);
        } else {
            $user = $customer->user;
        }

        $cart = $customer->cart()->create();

        $this->user = $user;
        $this->customer = $customer;
        $this->cart = $cart;
        $this->pending_order = PendingOrder::find($cart->id);
    }
}
