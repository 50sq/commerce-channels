<?php

namespace FiftySq\Commerce\Payments\Webhooks;

use FiftySq\Commerce\Data\Models\Payment;
use FiftySq\Commerce\Data\Models\PendingOrder;
use FiftySq\Commerce\Data\Payment as PaymentObject;
use FiftySq\Commerce\Payments\Actions\CompleteCheckout;
use FiftySq\Commerce\Payments\Events\CheckoutPaymentSucceeded;
use Illuminate\Database\Eloquent\Model;

class CheckoutSessionCompleted
{
    /**
     * Type of webhook event.
     *
     * @var string
     */
    protected string $type;

    /**
     * Checkout gateway.
     *
     * @var string
     */
    protected string $gateway;

    /**
     * Reference ID. This *should* be your system cart ID.
     *
     * @var string
     */
    protected string $gatewayCheckoutId;

    /**
     * Total amount of the checkout, in cents.
     *
     * @var string
     */
    protected string $amountCents;

    /**
     * Status of payment.
     *
     * @var string
     */
    protected string $paymentStatus;

    /**
     * CheckoutSessionCompleted constructor.
     *
     * @param  string  $type
     * @param  string  $gateway
     * @param  string  $gatewayCheckoutId
     * @param  string  $amountCents
     * @param  string  $paymentStatus
     */
    public function __construct(string $type, string $gateway, string $gatewayCheckoutId, string $amountCents, string $paymentStatus)
    {
        $this->type = $type;
        $this->gateway = $gateway;
        $this->gatewayCheckoutId = $gatewayCheckoutId;
        $this->amountCents = $amountCents;
        $this->paymentStatus = $paymentStatus;
    }

    public function handle(CompleteCheckout $action)
    {
        $pendingOrder = $this->findOrder();

        $payment = new PaymentObject(
            $this->gatewayCheckoutId,
            $this->amountCents,
            $this->paymentStatus
        );

        switch ($payment->getPaymentStatus()) {
            case Payment::STATUS_PAID:
                $order = $action($pendingOrder, $payment);
                event(new CheckoutPaymentSucceeded($order));
                break;
            case Payment::STATUS_UNPAID:
                //            event(new CheckoutPaymentSucceeded());
                break;
            case Payment::STATUS_NO_PAYMENT_REQUIRED:
                //            event(new CheckoutPaymentSucceeded());
                break;
        }
    }

    /**
     * @return Model|PendingOrder
     */
    protected function findOrder(): PendingOrder
    {
        return PendingOrder::query()
            ->where('gateway', $this->gateway)
            ->where('gateway_checkout_id', $this->gatewayCheckoutId)->first();
    }
}
