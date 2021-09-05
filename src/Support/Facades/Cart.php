<?php

namespace FiftySq\Commerce\Support\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection items()
 * @method static void add($id, $quantity, $options)
 * @method static void remove($id, $quantity)
 * @method static mixed getItem($id)
 * @method static void setItem($id, $quantity, $options)
 * @method static bool has($id)
 * @method static void clear()
 *
 * @see \FiftySq\Commerce\Contracts\CartContract
 */
class Cart extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'commerce.cart';
    }
}
