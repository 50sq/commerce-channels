<?php

namespace FiftySq\Commerce\Contracts;

interface SkuGeneratorContract
{
    public static function generate($product, $variant);
}
