<?php

namespace FiftySq\Commerce\Http\Controllers;

use FiftySq\Commerce\Contracts\CheckoutSuccessViewResponse;
use Illuminate\Routing\Controller;

class CheckoutSuccessController extends Controller
{
    public function index()
    {
        return app(CheckoutSuccessViewResponse::class);
    }
}
