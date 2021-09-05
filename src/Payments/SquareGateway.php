<?php

namespace FiftySq\Commerce\Payments;

use FiftySq\Commerce\Data\Models\Payment;
use FiftySq\Commerce\Payments\Contracts\HasCheckout;
use FiftySq\Commerce\Payments\Data\SessionIntent;
use FiftySq\Commerce\Payments\Webhooks\CheckoutSessionCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Square\Models\CreateCheckoutRequest;
use Square\Models\CreateOrderRequest;
use Square\Models\Money;
use Square\Models\Order;
use Square\Models\OrderLineItem;
use Square\SquareClient;

class SquareGateway implements HasCheckout
{
    protected SquareClient $client;

    protected array $config;

    /**
     * @var
     */
    protected $currency;

    /**
     * @var string
     */
    protected string $webhookHeader = 'x-square-signature';

    /**
     * @var array
     */
    protected array $paymentStatuses = [
        'APPROVED' => Payment::STATUS_PAID,
        'CANCELED' => Payment::STATUS_UNPAID,
        'COMPLETED' => Payment::STATUS_PAID,
        'FAILED' => Payment::STATUS_UNPAID,
        'PENDING' => Payment::STATUS_UNPAID,
    ];

    /**
     * SquareGateway constructor.
     *
     * @param  SquareClient  $client
     * @throws \Square\Exceptions\ApiException
     */
    public function __construct(SquareClient $client)
    {
        $this->client = $client;
        $this->config = config('commerce.gateways.drivers.square');

        $this->currency = $client->getLocationsApi()
            ->retrieveLocation($this->config->location_id)
            ->getResult()
            ->getLocation()
            ->getCurrency();
    }

    /**
     * @param  iterable  $items
     * @return array
     */
    public function buildCheckoutItems(iterable $items): array
    {
        if ($items instanceof Collection) {
            $items = $items->toArray();
        }

        return array_map(fn ($item) => $this->makeLineItem($item), $items);
    }

    /**
     * @param  SessionIntent  $intent
     * @return SessionIntent
     * @throws \Square\Exceptions\ApiException
     */
    public function createCheckoutSession(SessionIntent $intent): SessionIntent
    {
        $response = $this->client->getCheckoutApi()->createCheckout(
            $this->config->location_id,
            $this->makeCheckoutRequest($intent)
        );

        $intent->setCheckoutUrl($response->getResult()->getCheckout()->getCheckoutPageUrl());

        return $intent;
    }

    /**
     * @param  SessionIntent  $intent
     * @return CreateCheckoutRequest
     */
    protected function makeCheckoutRequest(SessionIntent $intent): CreateCheckoutRequest
    {
        $request = new CreateCheckoutRequest($intent->getCartId(), $this->makeOrderRequest($intent));

        $request->setAskForShippingAddress($intent->getCollectShippingAddress());
        $request->setPrePopulateBuyerEmail($intent->getCustomer('email'));
        $request->setRedirectUrl($this->redirectUrl());

        return $request;
    }

    /**
     * @param  SessionIntent  $intent
     * @return CreateOrderRequest
     */
    protected function makeOrderRequest(SessionIntent $intent): CreateOrderRequest
    {
        $request = new CreateOrderRequest();

        $request->setOrder($this->makeOrder($intent));
        $request->setIdempotencyKey($intent->getCartId());

        return $request;
    }

    /**
     * @param  SessionIntent  $intent
     * @return Order
     */
    protected function makeOrder(SessionIntent $intent): Order
    {
        $order = new Order($this->config->location_id);

        $order->setLineItems($intent->getItems());
        $order->setLocationId($this->config->location_id);
        $order->setReferenceId($intent->getCartId());

        return $order;
    }

    /**
     * @param  array  $values
     * @return OrderLineItem
     */
    protected function makeLineItem(array $values): OrderLineItem
    {
        $item = new OrderLineItem($values['quantity']);

        $item->setName($values['title']);
        $item->setCatalogObjectId(''); // external variant ID
        $item->setBasePriceMoney($this->getMoney($item['unit_price']));

        return $item;
    }

    /**
     * @param $unit_price
     * @return Money
     */
    protected function getMoney($unit_price): Money
    {
        $money = new Money();

        $money->setCurrency($this->currency);
        $money->setAmount($unit_price);

        return $money;
    }

    /**
     * @param  Request  $request
     * @return null
     */
    public function handleCheckoutSuccess(Request $request)
    {
        return null;
    }

    /**
     * @param  Request  $request
     * @throws \Exception
     */
    public function processWebhook(Request $request)
    {
        if ($this->validateWebhook($request)) {
            $handler = null;
            $data = $request->input('data.object.payment');

            switch ($type = $request->input('type')) {
                case 'payment.created':
                case 'payment.updated':
                    $handler = new CheckoutSessionCompleted(
                        $type,
                        $data['reference_id'],
                        $this->paymentStatuses[$data['status']],
                    );
                    break;
            }

            if ($handler) {
                $handler->handle();
            }
        }

        throw new \Exception('Invalid webhook.');
    }

    /**
     * @param  Request  $request
     * @return bool
     */
    public function validateWebhook(Request $request): bool
    {
        if ($request->hasHeader($this->webhookHeader) && $this->config->webhook_token) {
            $combined = $this->redirectUrl().$request->getContent();

            $hash = hash_hmac('sha1', $combined, $this->config->webhook_token);

            return $hash === $request->header($this->webhookHeader);
        }

        return true;
    }

    /**
     * @return string
     */
    protected function redirectUrl(): string
    {
        return route('commerce.checkout.success');
    }
}
