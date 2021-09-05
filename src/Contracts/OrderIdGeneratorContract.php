<?php

namespace FiftySq\Commerce\Contracts;

interface OrderIdGeneratorContract
{
    public static function generate($customer, $cart);
}
