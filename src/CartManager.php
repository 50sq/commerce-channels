<?php

namespace FiftySq\Commerce;

use Closure;
use InvalidArgumentException;

class CartManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved cart drivers.
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * The array of resolved cart connectors.
     *
     * @var array
     */
    protected $connectors = [];

    /**
     * Create a new cart manager instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Resolve a cart driver instance.
     *
     * @param  string|null  $name
     * @return \FiftySq\Commerce\Contracts\CartContract
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        if (! isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->resolve();

            $this->drivers[$name]->setContainer($this->app);
            $this->drivers[$name]->setName($name);
        }

        return $this->drivers[$name];
    }

    /**
     * Resolve a cart driver.
     *
     * @return \FiftySq\Commerce\Contracts\CartContract
     */
    protected function resolve()
    {
        $config = $this->getConfig();

        return $this->getConnector($config['driver'])
            ->connect($config);
    }

    /**
     * Get the connector for a given driver.
     *
     * @param  string  $driver
     * @return  \FiftySq\Commerce\Cart\Connectors\ConnectorInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function getConnector($driver)
    {
        if (! isset($this->connectors[$driver])) {
            throw new InvalidArgumentException("No connector for [$driver].");
        }

        return call_user_func($this->connectors[$driver]);
    }

    /**
     * Get the cart driver configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig()
    {
        return ['driver' => 'database'];
    }

    /**
     * Get the name of the default cart driver.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'database';
    }

    /**
     * Get the full name for the given driver.
     *
     * @param  string|null  $driver
     * @return string
     */
    public function getName($driver = null)
    {
        return $driver ?: $this->getDefaultDriver();
    }

    /**
     * Add a cart connection resolver.
     *
     * @param  string  $driver
     * @param  Closure  $resolver
     * @return void
     */
    public function addConnector($driver, Closure $resolver)
    {
        $this->connectors[$driver] = $resolver;
    }

    /**
     * Get the application instance used by the manager.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Set the application instance used by the manager.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return $this
     */
    public function setApplication($app)
    {
        $this->app = $app;

        foreach ($this->drivers as $driver) {
            $driver->setContainer($app);
        }

        return $this;
    }

    /**
     * Dynamically pass calls to the default driver.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }
}
