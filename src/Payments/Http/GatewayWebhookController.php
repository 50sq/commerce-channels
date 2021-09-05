<?php

namespace FiftySq\Commerce\Payments\Http;

use FiftySq\Commerce\Payments\GatewayManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class GatewayWebhookController extends Controller
{
    public function store(Request $request, GatewayManager $manager)
    {
        $manager->processWebhook($request);

        return new Response('', 200);
    }
}
