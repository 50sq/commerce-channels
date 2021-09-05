<?php

namespace FiftySq\Commerce\Payments;

use FiftySq\Commerce\Payments;
use FiftySq\Commerce\Payments\Data\SessionIntent;
use Illuminate\Http\Request;
use Illuminate\Support\Manager;
use Illuminate\Support\Traits\ForwardsCalls;
use InvalidArgumentException;

/**
 * @method static array buildCheckoutItems(iterable $items)
 * @method static SessionIntent createCheckoutSession(SessionIntent $intent)
 * @method static mixed handleCheckoutSuccess(Request $request)
 * @method static void processWebhook(Request $request)
 *
 * @see \FiftySq\Commerce\Payments\Contracts\HasCheckout
 */
class GatewayManager extends Manager
{
    use ForwardsCalls;

    /**
     * The default gateway.
     *
     * @var string
     */
    protected $defaultGateway = 'null';

    /**
     * Create Stripe driver.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createStripeDriver()
    {
        return $this->container->make(Payments\StripeGateway::class);
    }

    /**
     * Create Square driver.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createSquareDriver()
    {
        return $this->container->make(Payments\SquareGateway::class);
    }

    /**
     * Get a gateway instance.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function gateway(string $name = null)
    {
        return $this->driver($name);
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException|\Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function createDriver($driver)
    {
        try {
            return parent::createDriver($driver);
        } catch (InvalidArgumentException $e) {
            if (class_exists($driver)) {
                return $this->container->make($driver);
            }

            throw $e;
        }
    }

    /**
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return config('commerce.gateways.default') ?? $this->defaultGateway;
    }
}
