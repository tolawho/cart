<?php namespace Tolawho\Cart;

use Illuminate\Support\Collection;

class ItemOptions extends Collection
{

    /**
     * Get the option by the given key.
     *
     * @param string $key The option key.
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }
}
