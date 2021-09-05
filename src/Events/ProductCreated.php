<?php

namespace FiftySq\Commerce\Events;

use Illuminate\Queue\SerializesModels;

class ProductCreated
{
    use SerializesModels;

    /**
     * @var
     */
    public $product;

    /**
     * ProductCreated constructor.
     *
     * @param $product
     */
    public function __construct($product)
    {
        $this->product = $product;
    }
}
