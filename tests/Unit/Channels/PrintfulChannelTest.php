<?php

namespace Tests\Unit\Channels;

use FiftySq\Commerce\Support\Facades\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Printful\PrintfulApiClient;
use Tests\TestCase;

class PrintfulChannelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('commerce.channels.default', 'printful');
    }

    public function test_get_products()
    {
        Config::set('commerce.channels.drivers.printful.key', Str::random(32));

        $this->mock(PrintfulApiClient::class, function ($mock) {
            $mock->shouldReceive('get')->once()
                ->andReturn([]);
        });

        $products = Channel::getProducts();

        $this->assertSame(Collection::class, get_class($products));
    }
}
