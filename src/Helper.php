<?php

if (!function_exists('cart')) {

    /**
     * Create new cart facade instance
     *
     * @author tolawho
     * @return \Tolawho\Cart\Cart
     * @link https://github.com/tolawho/cart
     */
    function cart()
    {
        return app('cart');
    }
}