<?php

namespace FiftySq\Commerce\Http\Responses;

use FiftySq\Commerce\Contracts\CheckoutSuccessViewResponse;
use FiftySq\Commerce\Contracts\OrderSummaryViewResponse;
use FiftySq\Commerce\Contracts\PaymentInformationViewResponse;
use FiftySq\Commerce\Contracts\ShippingInformationViewResponse;
use Illuminate\Contracts\Support\Responsable;

class SimpleViewResponse implements
    CheckoutSuccessViewResponse,
    PaymentInformationViewResponse,
    OrderSummaryViewResponse,
    ShippingInformationViewResponse
{
    /**
     * The name of the view or the callable used to generate the view.
     *
     * @var callable|string
     */
    protected $view;

    /**
     * Create a new response instance.
     *
     * @param callable|string $view
     * @return void
     */
    public function __construct($view)
    {
        $this->view = $view;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if (! is_callable($this->view) || is_string($this->view)) {
            return view($this->view, ['request' => $request]);
        }

        $response = call_user_func($this->view, $request);

        if ($response instanceof Responsable) {
            return $response->toResponse($request);
        }

        return $response;
    }
}
