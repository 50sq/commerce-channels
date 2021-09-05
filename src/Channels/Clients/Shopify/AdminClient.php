<?php

namespace FiftySq\Commerce\Channels\Clients\Shopify;

use FiftySq\Commerce\Channels\Clients\Concerns\MakesRequests;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Str;

class AdminClient extends ShopifyClient
{
    use MakesRequests;

    /**
     * @param $method
     * @param $path
     * @param  array  $arguments
     * @return mixed
     */
    public function request($method, $path, array $arguments = [])
    {
        if (! Str::endsWith($path, '.json')) {
            $path = $path.'.json';
        }

        return parent::request($method, $path, $arguments);
    }

    /**
     * Return a client instance.
     *
     * @return PendingRequest
     */
    public function client(): PendingRequest
    {
        return (new \Illuminate\Http\Client\Factory)->asJson()
            ->baseUrl($this->baseUrl())
            ->withHeaders($this->headers());
    }

    /**
     * Provide the base URL.
     *
     * @return string
     */
    public function baseUrl(): string
    {
        return "https://{$this->shop}.myshopify.com/admin/api/{$this->version}";
    }

    /**
     * Build request headers.
     *
     * @return array
     */
    public function headers(): array
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Basic '.base64_encode(implode(':', [$this->key, $this->password])),
        ];
    }
}
