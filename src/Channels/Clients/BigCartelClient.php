<?php

namespace FiftySq\Commerce\Channels\Clients;

use FiftySq\Commerce\Channels\Clients\Concerns\MakesRequests;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class BigCartelClient extends AbstractClient
{
    use MakesRequests;

    protected string $subdomain;
    protected string $key;
    protected string $accountId;

    /**
     * BigCartelClient constructor.
     *
     * @param  string  $subdomain
     * @param  string  $key
     * @param  string  $accountId
     */
    public function __construct(string $subdomain, string $key, string $accountId)
    {
        $this->subdomain = $subdomain;
        $this->key = $key;
        $this->accountId = $accountId;
    }

    /**
     * @param  array  $filters
     * @return mixed
     */
    public function getProducts(array $filters = [])
    {
        return $this->get('products', $filters)->json('data');
    }

    /**
     * @param $productId
     * @return mixed
     */
    public function getProduct($productId)
    {
        return $this->get("products/{$productId}")->json('data');
    }

    /**
     * @return PendingRequest
     */
    public function client(): PendingRequest
    {
        return Http::asJson()
            ->baseUrl($this->baseUrl())
            ->withUserAgent($this->userAgent())
            ->withBasicAuth($this->subdomain, $this->key);
    }

    /**
     * @return string
     */
    public function baseUrl(): string
    {
        return "https://api.bigcartel.com/v1/accounts/{$this->accountId}";
    }

    /**
     * @return string[]
     */
    public function headers(): array
    {
        return [
            'Accept' => 'application/vnd.api+json',
            'Content-type' => 'application/vnd.api+json',
        ];
    }

    /**
     * @return string
     */
    protected function userAgent(): string
    {
        return implode(' ', [
            config('commerce.contact.name'),
            '<'.config('commerce.contact.email').'>',
        ]);
    }
}
