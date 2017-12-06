<?php namespace Tolawho\Cart;

use Tolawho\Cart\Facades\Cart;

trait Shopping
{
    /**
     * Add the Cartable item to the cart
     *
     * @param  string|null  $cartInstance  The cart instance name
     * @param  int          $qty           Quantities of item want to add to the cart
     * @param  array        $options       Array of additional options, such as 'size' or 'color'
     *
     * @return \Tolawho\Cart\Item
     */
    /**
     * @param null $cartInstance
     * @param int $qty
     * @param array $options
     * @return mixed
     */
    public function addToCart($cartInstance = null, $qty = 1, $options = [])
    {
        $id    = $this->getCartableId();
        $title = $this->getCartableTitle();
        $price = $this->getCartablePrice();

        return Cart::instance($cartInstance)->add($this, $qty, $options);
    }

    /**
     * Determine the Cartable item has in the cart
     *
     * @param  string|null  $cartInstance  The cart instance name
     * @param  array        $options       Array of additional options, such as 'size' or 'color'
     *
     * @return boolean
     */
    public function hasInCart($cartInstance = null, array $options = [])
    {
        $foundInCart = $this->searchInCart($cartInstance);

        return ($foundInCart->isEmpty()) ? false : true;
    }

    /**
     * Get all the Cartable item in the cart
     *
     * @param  string|null  $cartInstance  The cart instance name
     *
     * @return \Illuminate\Support\Collection
     */
    public function allFromCart($cartInstance = null)
    {
        return $this->searchInCart($cartInstance);
    }

    /**
     * Get the Cartable items in the cart with given additional options
     *
     * @param  string|null  $cartInstance  The cart instance name
     * @param  array        $options       Array of additional options, such as 'size' or 'color'
     *
     * @return \Illuminate\Support\Collection
     */
    public function searchInCart($cartInstance = null, array $options = [])
    {
        return Cart::instance($cartInstance)->search([
            'id'         => $this->getCartableId(),
            'title'      => $this->getCartableTitle(),
            'options'    => $options,
            'associated' => __CLASS__
        ]);
    }

    /**
     * Get the identifier of the Cartable item.
     *
     * @return int|string
     */
    public function getCartableId()
    {
        return method_exists($this, 'getKey') ? $this->getKey() : $this->id;
    }

    /**
     * Get the title of the Cartable item.
     *
     * @return string
     */
    public function getCartableTitle()
    {
        return property_exists($this, 'title') ? $this->title : ((property_exists($this, 'cartTitleField')) ? $this->getAttribute($this->cartTitleField) : 'Unknown');
    }

    /**
     * Get the price of the Cartable item.
     *
     * @return float
     */
    public function getCartablePrice()
    {
        return property_exists($this, 'price') ? $this->price : ((property_exists($this, 'cartPriceField')) ? $this->getAttribute($this->cartPriceField) : 0);
    }

    /**
     * Find a model by its identifier
     *
     * @param  int  $id  The identifier of model
     *
     * @return \Illuminate\Support\Collection|static|null
     */
    public function findById($id)
    {
        return $this->find($id);
    }
}
