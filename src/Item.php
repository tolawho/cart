<?php namespace Tolawho\Cart;

use Illuminate\Support\Collection;
use Tolawho\Cart\Exceptions\CartInvalidArgumentException;
use Tolawho\Cart\Exceptions\CartInvalidModelException;

class Item extends Collection
{

    /**
     * Initialize a well-formed cart item instance
     *
     * @param int $id Unique ID of item before insert to the cart
     * @param string $title Name of item
     * @param int $qty
     * @param float $price Unit price of one item
     * @param array $options Array of additional options, such as 'size' or 'color'
     * @param mixed $associated The model or the FQN of model that will be associated
     * @return \Tolawho\Cart\Item
     * @throws CartInvalidArgumentException
     */
    public function init($id, $title, $qty, $price, array $options = [], $associated = null)
    {
        if (empty($id)) {
            throw new CartInvalidArgumentException("The item identifier argument is not allowed to be empty.");
        }

        if (empty($title)) {
            throw new CartInvalidArgumentException("The item title argument is not allowed to be empty.");
        }

        if (!is_numeric($qty) || $qty < 1) {
            throw new CartInvalidArgumentException("The item quantity argument must be an integer type greater than 1.");
        }

        if (!is_numeric($price) || $price < 0) {
            throw new CartInvalidArgumentException("The item quantity argument must be an float type greater than 0.");
        }

        $quantity = intval($qty);
        $price = floatval($price);
        $this->put('hash', $this->genHash($id, $associated, $options));
        $this->put('id', $id);
        $this->put('title', $title);
        $this->put('qty', $quantity);
        $this->put('price', $price);
        $this->put('subtotal', $this->calcSubTotal($quantity, $price));
        $this->put('options', new ItemOptions($options));
        $this->put('associated', $associated);

        return $this;
    }

    /**
     * Magic accessor.
     *
     * @param string $property Property name.
     * @return mixed
     * @throws CartInvalidModelException
     */
    public function __get($property)
    {
        if ($property === 'model') {
            $model = with(new $this->associated)->findById($this->id);

            if (!$model) {
                throw new CartInvalidModelException("The supplied associated model from ".$this->get('associated')." does not exist.");
            }

            return $model;
        }

        return $this->get($property);
    }

    /**
     * Update info of cart item
     *
     * @param  array  $attributes
     *
     * @return \Tolawho\Cart\Item
     */
    public function update(array $attributes)
    {
        // Don't allow update manually following attributes: hash, id, subtotal, associated
        $attributes = array_only($attributes, ['title', 'qty', 'price', 'options']);

        // Format data
        foreach ($attributes as $key => $value) {
            switch (true) {
                case ($key == 'options'):
                    $value = $this->options->merge($value);
                    break;

                case ($key == 'qty'):
                    $value = intval($value);
                    break;

                case ($key == 'title'):
                    $value = title_case($key);
                    break;

                case ($key == 'price'):
                    $value = floatval($value);
                    break;

                default:
                    # code...
                    break;
            }

            $this->put($key, $value);
        }

        // Recalculate subtotal
        if (count(array_intersect(array_keys($attributes), ['qty', 'price'])) > 0) {
            $this->updateSubTotal();
        }

        // Update hash
        if (count(array_intersect(array_keys($attributes), ['options'])) > 0) {
            $this->updateHash();
        }

        return $this;
    }

    /**
     * Get data for initializing item from instance of Cartable
     *
     * @param  object  $useCartableInstance
     * @param  int     $qty
     * @param  array   $options
     *
     * @return array
     */
    protected function parseFromCartable($useCartableInstance, $qty, $options)
    {
        $id         = $useCartableInstance->getCartableId();
        $title      = $useCartableInstance->getCartableTitle();
        $qty        = $qty ?: 1;
        $price      = $useCartableInstance->getCartablePrice();
        $options    = (!is_array($options)) ? [] : $options;
        $associated = get_class($useCartableInstance);

        return [$id, $title, $qty, $price, $options, $associated];
    }

    /**
     * Generate the unique identifier for the cart item
     *
     * @param mixed $id Unique ID of item before insert to the cart
     * @param $associated
     * @param array $options Array of additional options
     * @return string
     */
    protected function genHash($id, $associated, $options = [])
    {
        ksort($options);
        return md5($id . serialize($associated) . serialize($options));
    }

    /**
     * Update unique identifier for the cart item
     *
     * @return void
     */
    protected function updateHash()
    {
        $this->put('hash', $this->genHash($this->id, $this->associated, $this->options->all()));
    }

    /**
     * Calculate sub total price from qty and price of the cart item
     *
     * @param  int    $qty
     * @param  float  $price
     *
     * @return float
     */
    protected function calcSubTotal($qty, $price)
    {
        return intval($qty) * floatval($price);
    }

    /**
     * Re calculate sub total price
     *
     * @return void
     */
    protected function updateSubTotal()
    {
        $this->put('subtotal', $this->calcSubTotal($this->qty, $this->price));
    }
}
