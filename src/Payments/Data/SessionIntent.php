<?php

namespace FiftySq\Commerce\Payments\Data;

use Illuminate\Support\Arr;

class SessionIntent
{
    const MODE_PAYMENT = 'payment';
    const MODE_SETUP = 'setup';
    const MODE_SUBSCRIPTION = 'subscription';

    /**
     * The ID of the cart for this session.
     *
     * @var
     */
    protected $cartId;

    /**
     * Customer attributes.
     *
     * @var array
     */
    protected array $customer = [];

    /**
     * The checkout URL generated for this session.
     *
     * @var string
     */
    protected string $url;

    /**
     * The items to be checked out.
     *
     * @var array
     */
    protected array $items;

    /**
     * The checkout mode.
     *
     * @var string|mixed
     */
    protected string $mode;

    /**
     * Array of discount codes for this session.
     *
     * @var array
     */
    protected array $discounts = [];

    /**
     * Elect to collect shipping address.
     *
     * @var bool
     */
    protected bool $collectShippingAddress = true;

    /**
     * The ID generated for this session.
     * Returned from provider.
     *
     * @var string
     */
    private string $id;

    /**
     * The checkout URL generated for this session.
     * Returned from provider.
     *
     * @var string
     */
    private string $checkoutUrl;

    /**
     * Payment intent object for reference.
     * Returned from provider.
     *
     * @var string
     */
    private string $paymentIntent;

    /**
     * SessionIntent constructor.
     *
     * @param $cartId
     * @param  array  $items
     * @param  string  $mode
     */
    public function __construct(
        $cartId,
        array $items,
        string $mode = self::MODE_PAYMENT
    ) {
        $this->cartId = $cartId;
        $this->items = $items;
        $this->mode = $mode;
    }

    /**
     * @return mixed
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return mixed|string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param  null  $attribute
     * @return string|array|null
     */
    public function getCustomer($attribute = null)
    {
        if ($attribute) {
            return Arr::get($this->customer, $attribute);
        }

        return $this->customer;
    }

    /**
     * @param  array  $customer
     */
    public function setCustomer(array $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setSessionId($id)
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getPaymentIntent(): array
    {
        return $this->paymentIntent;
    }

    /**
     * @param  string  $paymentIntent
     */
    public function setPaymentIntent(string $paymentIntent)
    {
        $this->paymentIntent = $paymentIntent;
    }

    /**
     * @return array
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    /**
     * @param  string  $discountCode
     */
    public function setDiscount(string $discountCode)
    {
        $this->discounts[] = $discountCode;
    }

    /**
     * @return bool
     */
    public function getCollectShippingAddress(): bool
    {
        return $this->collectShippingAddress;
    }

    /**
     * @param  bool  $collectShippingAddress
     */
    public function setCollectShippingAddress(bool $collectShippingAddress): void
    {
        $this->collectShippingAddress = $collectShippingAddress;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl(): string
    {
        return $this->checkoutUrl;
    }

    /**
     * @param  string  $checkoutUrl
     */
    public function setCheckoutUrl(string $checkoutUrl): void
    {
        $this->checkoutUrl = $checkoutUrl;
    }
}
