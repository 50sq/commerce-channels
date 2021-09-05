<?php

namespace FiftySq\Commerce\Data\Models;

use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\BillingAddress;
use FiftySq\Commerce\Data\ShippingAddress;
use FiftySq\Commerce\Pipes\ApplyDiscounts;
use FiftySq\Commerce\Pipes\ApplyShipping;
use FiftySq\Commerce\Pipes\ApplyTaxes;
use FiftySq\Commerce\Pipes\AssociateBillingInformation;
use FiftySq\Commerce\Pipes\AssociateContactInformation;
use FiftySq\Commerce\Pipes\AssociateShippingInformation;
use FiftySq\Commerce\Pipes\RemoveZeroedItems;
use FiftySq\Commerce\Pipes\SetTotal;
use FiftySq\Commerce\Pipes\SubtotalItems;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pipeline\Pipeline;

class PendingOrder extends Cart
{
    /**
     * @var string
     */
    protected string $contactEmail;

    /**
     * @var string
     */
    protected string $contactPhone;

    /**
     * Subtotal (cents) of the order.
     *
     * @var int
     */
    protected int $subtotal = 0;

    /**
     * Discounts for the order.
     *
     * @var array
     */
    protected array $discounts = [];

    /**
     * Discount total (cents) on the order.
     *
     * @var int
     */
    protected int $discountTotal = 0;

    /**
     * Shipping total (cents) on the order.
     *
     * @var int
     */
    protected int $shippingTotal = 0;

    /**
     * Sales tax total (cents) on the order.
     *
     * @var int
     */
    protected int $salesTaxTotal = 0;

    /**
     * Total (cents) of the order.
     *
     * @var int
     */
    protected int $total = 0;

    /**
     * Discount relationships.
     *
     * @return BelongsToMany
     */
    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class, Commerce::prefix('discount_order'), Commerce::prefix('order_id'));
    }

    /**
     * Process cart through pipes to an Order.
     *
     * @return PendingOrder $order
     */
    public function prepareForCheckout(): self
    {
        // TODO: Allow for custom pretax and posttax pipes

        app(Pipeline::class)
            ->send($this)
            ->through([
                RemoveZeroedItems::class,
                SubtotalItems::class,
                AssociateContactInformation::class,
                AssociateBillingInformation::class,
                AssociateShippingInformation::class,
                ApplyDiscounts::class,
                ApplyShipping::class,
                // InjectPretax
                ApplyTaxes::class,
                // InjectPosttax
                SetTotal::class,
            ])
            ->then(function (self $order) {
                // TODO: emit events, etc.
            });

        return $this;
    }

    /**
     * Use the parent model's table.
     *
     * @return mixed
     */
    public function getTable()
    {
        $class = get_parent_class($this);

        return (new $class)->getTable();
    }

    /**
     * Return the customer ID;.
     *
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->commerce_customer_id;
    }

    /**
     * Return the customer's currency.
     *
     * @return string
     */
    public function getCustomerCurrency(): string
    {
        return (string) ($this->customer->currency ?? config('commerce.currency'));
    }

    /**
     * Return the subtotal.
     *
     * @return int
     */
    public function getSubtotal(): int
    {
        return $this->subtotal;
    }

    /**
     * Set the subtotal.
     *
     * @param int $subtotal
     * @return $this
     */
    public function setSubtotal(int $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Return discounts.
     *
     * @return array
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    /**
     * Set discounts.
     *
     * @param array $discounts
     * @return $this
     */
    public function setDiscounts(array $discounts): self
    {
        $this->discounts = $discounts;

        return $this;
    }

    /**
     * Return the discount total.
     *
     * @return int
     */
    public function getDiscountTotal(): int
    {
        return $this->discountTotal;
    }

    /**
     * Set the discount total.
     *
     * @param int $discountTotal
     * @return $this
     */
    public function setDiscountTotal(int $discountTotal): self
    {
        $this->discountTotal = $discountTotal;

        return $this;
    }

    /**
     * Return the sales tax total.
     *
     * @return int
     */
    public function getSalesTaxTotal(): int
    {
        return $this->salesTaxTotal;
    }

    /**
     * Set the sales tax total.
     *
     * @param int $salesTaxTotal
     * @return $this
     */
    public function setSalesTaxTotal(int $salesTaxTotal): self
    {
        $this->salesTaxTotal = $salesTaxTotal;

        return $this;
    }

    /**
     * Return the shipping total.
     *
     * @return int
     */
    public function getShippingTotal(): int
    {
        return $this->shippingTotal;
    }

    /**
     * Set the shipping total.
     *
     * @param int $shippingTotal
     * @return $this
     */
    public function setShippingTotal(int $shippingTotal): self
    {
        $this->shippingTotal = $shippingTotal;

        return $this;
    }

    /**
     * Return the total.
     *
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Set the total.
     *
     * @param int $total
     * @return $this
     */
    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return BillingAddress
     */
    public function getBillingAddress(): BillingAddress
    {
        return $this->billingAddress->toDto();
    }

    /**
     * @return ShippingAddress
     */
    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress->toDto();
    }

    /**
     * @return string
     */
    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    /**
     * @param  string  $contactEmail
     */
    public function setContactEmail(string $contactEmail): void
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @return string
     */
    public function getContactPhone(): string
    {
        return $this->contactPhone;
    }

    /**
     * @param  string  $contactPhone
     */
    public function setContactPhone(string $contactPhone): void
    {
        $this->contactPhone = $contactPhone;
    }
}
