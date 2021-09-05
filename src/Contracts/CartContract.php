<?php

namespace FiftySq\Commerce\Contracts;

use Illuminate\Support\Collection;

interface CartContract
{
    /**
     * Get the cart ID.
     *
     * @return mixed
     */
    public function id();

    /**
     * Get the cart items.
     *
     * @return Collection
     */
    public function items();

    /**
     * Add an item to the cart.
     *
     * @param $id
     * @param int $unit_price
     * @param int $quantity
     * @param array $options
     * @return mixed
     */
    public function add($id, int $unit_price, int $quantity = 1, array $options = []);

    /**
     * Remove an item from the cart.
     *
     * @param $id
     * @param int $quantity
     * @return mixed
     */
    public function remove($id, int $quantity = 1);

    /**
     * Remove an item from the cart.
     *
     * @param $id
     * @return mixed
     */
    public function getItem($id);

    /**
     * Set a specific quantity of an item in the cart.
     *
     * @param $id
     * @param int $unit_price_cents
     * @param int $quantity
     * @param array $options
     * @return mixed
     */
    public function setItem($id, int $unit_price_cents, int $quantity = 1, array $options = []);

    /**
     * Check if the cart has a specific item.
     *
     * @param $id
     * @return bool
     */
    public function has($id): bool;

    /**
     * Clear the cart.
     *
     * @return mixed
     */
    public function clear();

    /**
     * Set the connection.
     *
     * @param $connection
     * @return mixed
     */
    public function setConnection($connection);

    /**
     * Set the driver name.
     *
     * @param string $name
     * @return mixed
     */
    public function setName(string $name);
}
