<?php

namespace FiftySq\Commerce\Payments\Contracts;

use FiftySq\Commerce\Payments\Data\SessionIntent;
use Illuminate\Http\Request;

interface HasCheckout
{
    /**
     * @param  iterable  $items
     * @return array
     */
    public function buildCheckoutItems(iterable $items): array;

    /**
     * @param  SessionIntent  $intent
     * @return mixed
     */
    public function createCheckoutSession(SessionIntent $intent);

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function handleCheckoutSuccess(Request $request);

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function processWebhook(Request $request);
}
