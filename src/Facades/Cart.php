<?php namespace Tolawho\Cart\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * The Cart facade.
 *
 * @package Tolawho\Cart\Facades
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
        return 'cart';
    }
}
