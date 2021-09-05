<?php

namespace FiftySq\Commerce\Channels;

class DriverManager
{
    /**
     * @var ChannelManager
     */
    protected ChannelManager $manager;

    /**
     * Set a different driver.
     *
     * @var
     */
    protected $driver;

    /**
     * Create a new channel handler instance.
     *
     * @param  \FiftySq\Commerce\Channels\ChannelManager
     * @return void
     */
    public function __construct(ChannelManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $driver
     * @return $this
     */
    public function via($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function driver()
    {
        return $this->driver ?? config('commerce.channels.default');
    }

    /**
     * @return ChannelManager
     */
    public function manager(): ChannelManager
    {
        return $this->manager->driver($this->driver());
    }

    /**
     * Pass dynamic instance methods to the manager.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->manager->driver($this->driver())->$method(...$parameters);
    }

    /**
     * Dynamically pass methods to the default channel.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return static::manager()->$method(...$parameters);
    }
}
