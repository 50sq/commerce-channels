<?php

namespace Tests\Feature;

use FiftySq\Commerce\Contracts\CheckoutSuccessViewResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_load_checkout()
    {
        $this->mock(CheckoutSuccessViewResponse::class)
            ->shouldReceive('toResponse')->once()
            ->andReturn('Checkout Page');

        $this->setupCart();

        $this->actingAs($this->user)
            ->getJson(route('commerce.checkout.index'))
            ->assertOk()
            ->assertSee('Checkout Page');
    }
}
