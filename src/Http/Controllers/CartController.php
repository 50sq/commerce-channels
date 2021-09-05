<?php

namespace FiftySq\Commerce\Http\Controllers;

use FiftySq\Commerce\Cart\Manager;
use FiftySq\Commerce\Commerce;
use FiftySq\Commerce\Data\Models\Variant;
use FiftySq\Commerce\Http\Requests\AddToCartRequest;
use FiftySq\Commerce\Http\Requests\RemoveFromCartRequest;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class CartController extends Controller
{
    /**
     * The Cart manager.
     *
     * @var Manager
     */
    protected Manager $manager;

    /**
     * CartController constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Return cart items.
     *
     * @return mixed
     */
    public function index()
    {
        return Commerce::toCollection($this->manager->items());
    }

    /**
     * Add or update items in the cart.
     *
     * @param AddToCartRequest $request
     * @return Response
     */
    public function addItems(AddToCartRequest $request)
    {
        $variant = Variant::find($request->input('id'));

        $this->manager->add(
            $variant->id,
            $variant->unit_price_cents,
            $request->input('quantity'),
            $request->input('options', []),
        );
    }

    /**
     * Remove specific items from the cart.
     *
     * @param RemoveFromCartRequest $request
     * @return Response
     */
    public function removeItems(RemoveFromCartRequest $request)
    {
        $this->manager->remove(...$request->only(['id', 'quantity']));
    }

    /**
     * Remove all items from the cart.
     *
     * @return Response
     */
    public function clearCart()
    {
        $this->manager->clear();
    }
}
