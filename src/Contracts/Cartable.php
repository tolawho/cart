<?php namespace Tolawho\Cart\Contracts;

/**
 * Interface Cartable
 * @package Tolawho\Cart\Contracts
 */
interface Cartable
{
    /**
     * Get the identifier of the Cartable item.
     *
     * @return int|string
     */
    public function getCartableId();

    /**
     * Get the title of the Cartable item.
     *
     * @return string
     */
    public function getCartableTitle();

    /**
     * Get the price of the Cartable item.
     *
     * @return float
     */
    public function getCartablePrice();

    /**
     * Add the Cartable item to the cart
     *
     * @param  string|null  $cartInstance  The cart instance name
     * @param  int          $qty           Quantities of item want to add to the cart
     * @param  array        $options       Array of additional options, such as 'size' or 'color'
     *
     * @return \Tolawho\Cart\Item
     */
    public function addToCart($cartInstance = null, $qty = 1, $options = []);

    /**
     * Determine the Cartable item has in the cart
     *
     * @param  string|null  $cartInstance  The cart instance name
     * @param  array        $options       Array of additional options, such as 'size' or 'color'
     *
     * @return boolean
     */
    public function hasInCart($cartInstance = null, array $options = []);

    /**
     * Get all the Cartable item in the cart
     *
     * @param  string|null  $cartInstance  The cart instance name
     *
     * @return \Illuminate\Support\Collection
     */
    public function allFromCart($cartInstance = null);

    /**
     * Get the Cartable items in the cart with given additional options
     *
     * @param  string|null  $cartInstance  The cart instance name
     * @param  array        $options       Array of additional options, such as 'size' or 'color'
     *
     * @return \Illuminate\Support\Collection
     */
    public function searchInCart($cartInstance = null, array $options = []);

    /**
     * Find a model by its identifier
     *
     * @param  int  $id  The identifier of model
     *
     * @return \Illuminate\Support\Collection|static|null
     */
    public function findById($id);
}
