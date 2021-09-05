<?php

namespace FiftySq\Commerce\Events;

use Illuminate\Queue\SerializesModels;

class VariantCreated
{
    use SerializesModels;

    /**
     * The created variant.
     *
     * @var \FiftySq\Commerce\Data\Models\Variant
     */
    public $variant;

    /**
     * Create a new event instance.
     *
     * @param  \FiftySq\Commerce\Data\Models\Variant  $variant
     * @return void
     */
    public function __construct($variant)
    {
        $this->variant = $variant;
    }
}
