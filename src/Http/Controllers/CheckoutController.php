<?php

namespace FiftySq\Commerce\Http\Controllers;

use FiftySq\Commerce\Actions\UpdatePendingOrder;
use FiftySq\Commerce\Http\Requests\UpdatePendingOrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CheckoutController extends Controller
{
    public function update(UpdatePendingOrderRequest $request, UpdatePendingOrder $update)
    {
        $update($request, $request->pendingOrder());

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'pending-order-updated');
    }
}
