<?php

namespace FiftySq\Commerce\Payments\Http;

use FiftySq\Commerce\Contracts\CheckoutCancellationViewResponse;
use FiftySq\Commerce\Payments\Data\SessionIntent;
use FiftySq\Commerce\Payments\GatewayManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class CheckoutSessionController extends Controller
{
    /**
     * @param  Request  $request
     * @param  GatewayManager  $manager
     * @return JsonResponse
     */
    public function store(Request $request, GatewayManager $manager): JsonResponse
    {
        $pendingOrder = $request->customer()->pendingOrder->prepareForCheckout();

        $intent = new SessionIntent(
            $pendingOrder->uuid,
            $manager->buildCheckoutItems($pendingOrder->items),
        );

        if ($request->filled('coupon')) {
            $intent->setDiscount($request->input('coupon'));
        }

        $session = $manager->createCheckoutSession($intent);

        $pendingOrder->gateway = $manager->getDefaultDriver();
        $pendingOrder->gateway_checkout_id = $session->getSessionId();
        $pendingOrder->save();

        return new JsonResponse(['id' => $pendingOrder->gateway_checkout_id], 200);
    }

    /**
     * @param  Request  $request
     * @param  GatewayManager  $manager
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request, GatewayManager $manager)
    {
        $pendingOrder = $request->customer()->pendingOrder;

        if ($values = $manager->handleCheckoutSuccess($request)) {
            $pendingOrder->status = $values['status'];
            $pendingOrder->save();
        }

        return Response::redirectTo(route('commerce.checkout.success', $pendingOrder->uuid));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public function cancelled()
    {
        return app(CheckoutCancellationViewResponse::class);
    }
}
