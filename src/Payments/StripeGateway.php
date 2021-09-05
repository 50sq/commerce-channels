<?php

namespace FiftySq\Commerce\Payments;

use Exception;
use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Models\Payment;
use FiftySq\Commerce\Payments\Contracts\HasCheckout;
use FiftySq\Commerce\Payments\Data\SessionIntent;
use FiftySq\Commerce\Payments\Webhooks\CheckoutSessionCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeGateway implements HasCheckout
{
    /**
     * @var StripeClient
     */
    protected StripeClient $client;

    /**
     * @var string|null
     */
    protected ?string $webhook_token;

    /**
     * @var array
     */
    protected array $paymentStatuses = [
        'no_payment_required' => Payment::STATUS_NO_PAYMENT_REQUIRED,
        'paid' => Payment::STATUS_PAID,
        'unpaid' => Payment::STATUS_UNPAID,
    ];

    /**
     * StripeGateway constructor.
     *
     * @param  StripeClient  $client
     * @param  null  $webhook_token
     */
    public function __construct(StripeClient $client, $webhook_token = null)
    {
        $this->client = $client;
        $this->webhook_token = $webhook_token;
    }

    /**
     * @param  iterable  $items
     * @return array
     */
    public function buildCheckoutItems(iterable $items): array
    {
        $currency = config('commerce.currency');

        if ($items instanceof Collection) {
            $items = $items->toArray();
        }

        return array_map(fn ($item) => [
            'price_data' => [
                'currency' => $currency,
                'product' => Commerce::prefix('variant_id'),
                'unit_amount' => $item['unit_price_cents'],
                'tax_behavior' => 'exclusive',
            ],
            'adjustable_quantity' => true,
            'dynamic_tax_rates' => true,
            'quantity' => $item['quantity'],
        ], $items);
    }

    /**
     * @param  SessionIntent  $intent
     * @return SessionIntent
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCheckoutSession(SessionIntent $intent): SessionIntent
    {
        $session = $this->client->checkout->sessions->create(
            array_filter([
                'payment_method_types' => ['card'],
                'customer_email' => $intent->getCustomer('email'),
                'client_reference_id' => $intent->getCartId(),
                'line_items' => $intent->getItems(),
                'mode' => $intent->getMode(),
                'discounts' => [
                    array_map(fn ($code) => ['coupon' => $code], $intent->getDiscounts()),
                ],
                //                'shipping_address_collection' => $intent->getCollectShippingAddress()
                //                    ? ['allowed_countries' => config('shipping.countries')]
                //                    : null,
                'success_url' => route('commerce.checkout.session.success'),
                'cancel_url' => route('commerce.checkout.session.cancelled'),
            ])
        );

        $intent->setSessionId($session['id']);
        $intent->setPaymentIntent($session['payment_intent']);

        return $intent;
    }

    /**
     * @param  Request  $request
     * @return array
     */
    public function handleCheckoutSuccess(Request $request): array
    {
        return [
            'id' => $request->input('id'),
            'status' => $this->paymentStatuses[$request->input('payment_status')],
            'payment_intent' => $request->input('payment_intent'),
        ];
    }

    /**
     * @param  Request  $request
     */
    public function processWebhook(Request $request)
    {
        try {
            $this->handleWebhook($this->validateWebhook($request));
        } catch (Exception $exception) {
            abort(400);
        }
    }

    /**
     * @param  Request  $request
     * @return Event
     * @throws \Stripe\Exception\SignatureVerificationException
     */
    protected function validateWebhook(Request $request): Event
    {
        return Webhook::constructEvent(
            $request->getContent(), $request->header('HTTP_STRIPE_SIGNATURE'), $this->webhook_token
        );
    }

    /**
     * Handle the various Stripe webhooks.
     *
     * @param  Event  $event
     */
    protected function handleWebhook(Event $event)
    {
        $handler = null;
        $session = $event->data->object;

        switch ($event->type) {
            case Event::CHECKOUT_SESSION_COMPLETED:
            case Event::CHECKOUT_SESSION_ASYNC_PAYMENT_SUCCEEDED:
            case Event::CHECKOUT_SESSION_ASYNC_PAYMENT_FAILED:
                $handler = new CheckoutSessionCompleted(
                    $event->type,
                    'stripe',
                    $session->client_reference_id,
                    $session->amount_total,
                    $this->paymentStatuses[$session->payment_status],
                );
                break;
        }

        if ($handler) {
            $handler->handle();
        }
    }

    /**
     * @return array
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createWebhookEndpoint(): array
    {
        $endpoint = $this->client->webhookEndpoints->create([
            'url' => route('commerce.api.webhooks.payments'),
            'enabled_events' => [
                Event::CHECKOUT_SESSION_COMPLETED,
                Event::CHECKOUT_SESSION_ASYNC_PAYMENT_SUCCEEDED,
                Event::CHECKOUT_SESSION_ASYNC_PAYMENT_FAILED,
            ],
            'description' => config('app.name').' payments webhooks',
        ]);

        return $endpoint->toArray();
    }
}
