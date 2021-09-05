<?php

namespace FiftySq\Commerce\Support;

use FiftySq\Commerce\Data\Models\Customer;
use Illuminate\Support\Facades\Request;

class RequestMacros
{
    public static function register()
    {
        Request::macro('customer', function (): ?Customer {
            return $this->customer ?? null;
        });
    }
}
