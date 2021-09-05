<?php

namespace FiftySq\Commerce\Channels\Clients\Shopify;

use FiftySq\Commerce\Channels\Clients\AbstractClient;

abstract class ShopifyClient extends AbstractClient
{
    protected string $shop;
    protected string $key;
    protected string $password;
    protected string $version;

    /**
     * ShopifyClient constructor.
     *
     * @param  string  $shop
     * @param  string  $key
     * @param  string  $password
     * @param  string  $version
     */
    public function __construct(string $shop, string $key, string $password = '', string $version = '2021-04')
    {
        $this->shop = $shop;
        $this->key = $key;
        $this->password = $password;
        $this->version = $version;
    }
}
