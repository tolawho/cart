<?php namespace Tolawho\Cart\Providers;

use Illuminate\Support\ServiceProvider as Provider;

/**
 * Class ServiceProvider
 *
 * @package \Tolawho\Cart
 */
class ServiceProvider extends Provider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Bootstrap handles
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('cart', 'Tolawho\Cart\Cart');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'cart',
        ];
    }
}
