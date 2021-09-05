<?php

namespace FiftySq\Commerce\Data;

class Product extends DataObject
{
    protected string $title;
    protected string $description;
    protected string $type;
    protected int $defaultPrice;
    protected ?string $url;
    protected ?string $primaryImageUrl;
    protected bool $onSale;
    protected bool $isEnabled;
    protected bool $isShippable;
    protected bool $isTaxable;
    protected ?string $channel = null;
    protected ?string $external_channel_id = null;
    protected ?string $gateway = null;
    protected ?string $external_gateway_id = null;

    /**
     * Product constructor.
     *
     * @param  string  $title
     * @param  string  $description
     * @param  string  $type
     * @param  int  $defaultPrice
     * @param  string|null  $url
     * @param  string|null  $primaryImageUrl
     * @param  bool  $onSale
     * @param  bool  $isEnabled
     * @param  bool  $isShippable
     * @param  bool  $isTaxable
     */
    public function __construct(
        string $title,
        string $description,
        string $type,
        int $defaultPrice,
        ?string $url = null,
        ?string $primaryImageUrl = null,
        bool $onSale = false,
        bool $isEnabled = true,
        bool $isShippable = true,
        bool $isTaxable = true
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->defaultPrice = $defaultPrice;
        $this->url = $url;
        $this->primaryImageUrl = $primaryImageUrl;
        $this->onSale = $onSale;
        $this->isEnabled = $isEnabled;
        $this->isShippable = $isShippable;
        $this->isTaxable = $isTaxable;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getDefaultPrice(): int
    {
        return $this->defaultPrice;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getPrimaryImageUrl(): ?string
    {
        return $this->primaryImageUrl;
    }

    /**
     * @return bool
     */
    public function getOnSale(): bool
    {
        return filter_var($this->onSale, FILTER_VALIDATE_BOOL);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return bool
     */
    public function isShippable(): bool
    {
        return $this->isShippable;
    }

    /**
     * @return bool
     */
    public function isTaxable(): bool
    {
        return $this->isTaxable;
    }

    /**
     * @return string|null
     */
    public function getChannel(): ?string
    {
        return $this->channel;
    }

    /**
     * @param  string|null  $channel
     */
    public function setChannel(?string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * @return string|null
     */
    public function getExternalChannelId(): ?string
    {
        return $this->external_channel_id;
    }

    /**
     * @param  string|null  $external_channel_id
     */
    public function setExternalChannelId(?string $external_channel_id): void
    {
        $this->external_channel_id = $external_channel_id;
    }

    /**
     * @return string|null
     */
    public function getGateway(): ?string
    {
        return $this->gateway;
    }

    /**
     * @param  string|null  $gateway
     */
    public function setGateway(?string $gateway): void
    {
        $this->gateway = $gateway;
    }

    /**
     * @return string|null
     */
    public function getExternalGatewayId(): ?string
    {
        return $this->external_gateway_id;
    }

    /**
     * @param  string|null  $external_gateway_id
     */
    public function setExternalGatewayId(?string $external_gateway_id): void
    {
        $this->external_gateway_id = $external_gateway_id;
    }
}
