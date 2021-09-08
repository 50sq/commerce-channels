<?php

namespace FiftySq\Commerce\Channels\Drivers\Http;

use FiftySq\Commerce\Channels\Drivers\Data\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class OrdersController extends Controller
{
    public function show(Request $request, Order $order)
    {
        return $request->wantsJson()
            ? new JsonResponse($order->toArray(), 200)
            : back()->with([
                    'status' => 'order-complete',
                    'order' => $order,
                ]);
    }
}
