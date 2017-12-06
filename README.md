# Laravel Cart

Forked from [Jackie Do Cart](https://github.com/JackieDo/Laravel-Cart).

A small package use to create cart (such as shopping, wishlist, recent views...) in Laravel application.

# Overview
Look at one of the following topics to learn more about Laravel Cart

* [Installation](#installation)
* [Basic usage](#basic-usage)
    - [The Cart facade](#the-cart-facade)
    - [Name your carts](#name-your-carts)
    - [Get name of current cart](#get-name-of-current-cart)
    - [Add an item to cart](#add-an-item-to-cart)
    - [Update cart item](#update-cart-item)
    - [Get the specified cart item](#get-the-specified-cart-item)
    - [Get all cart items](#get-all-cart-items)
    - [Get total price of all cart items](#get-total-price-of-all-cart-items)
    - [Count cart items](#count-cart-items)
    - [Count quantities of all cart items](#count-quantities-of-all-cart-items)
    - [Search cart items](#search-cart-items)
    - [Remove the specified cart item](#remove-the-specified-cart-item)
    - [Destroy your cart](#destroy-your-cart)
* [Advanced usage](#advanced-usage)
    - [Collections](#collections)
    - [Eloquent model association](#eloquent-model-association)
    - [Custom search building](#custom-search-building)
    - [Dependency injection](#dependency-injection)
    - [Event and listener](#event-and-listener)
    - [Exceptions](#exceptions)
* [Thanks from author](#thanks-for-use)

## Installation
You can install this package through [Composer](https://getcomposer.org).

```
$ composer require tolawho/cart
```

- Next step, we run Composer update commend from the Terminal on your project source directory:
```shell
$ composer update
```

> **Note:** Instead of performing the above two steps, you can perform faster with the command line `$ composer require tolawho/cart` from Terminal

- Once update operation completes, the third step is add the service provider. Open `config/app.php`, and add a new item to the `providers` section:
```
'Tolawho\Cart\Providers\ServiceProvider',
```

> **Note:** From Laravel 5.1, you should write as `Tolawho\Cart\Providers\ServiceProvider::class,`

- And the final step is add the following line to the `aliases` section in file `config/app.php`:`
```
'Cart' => 'Tolawho\Cart\Facades\Cart',
```

> **Note:** From Laravel 5.1, you should write as `'Cart' => Tolawho\Cart\Facades\Cart::class,`

> **Note:** From Laravel 5.5+, no need manual config this

## Basic usage

### The Cart facade
Laravel Cart has a facade with name is `Tolawho\Cart\Facades\Cart`. You can do any cart operation through this facade.

### Name your carts
Laravel Cart supports multiple instances of the cart. This is useful for using different carts for different management purposes, such as shopping cart, whishlist items, recently views...

Whenever you create a shopping cart, you should name it so that it can be distinguished from other carts. You can set name for an instance of the cart by calling `Cart::instance($instanceName)`. From this point of time, the active instance of the cart will have a specified name, so whenever you perform a cart operation, you must specify a specific cart.

Example:
```php
Cart::instance('shopping');
```

If you want to switch between instances, you just call `Cart::instance($otherName)` again, and you're working with the other instance.

Example:
```php
// Create new instance of the cart with named is `shopping`
$cart = Cart::instance('shopping');

// Perform a something with this instance
$cart->doSomething();

// Switch to `whishlist` cart and do something
Cart::instance('wishlist')->doSomething();

// Do another thing also with `whishlist` cart
Cart::doAnother();

// Switch to `recent-view` cart and do something
Cart::instance('recent-view')->doSomething();

// Go back to work with `shopping` cart
Cart::instance('shopping')->doSomething();
...
```

**Note:**
- The default cart instance is called `default`, so when you're not using instances, example `Cart::doSomething();` is the same as `Cart::instance('default')->doSomething();`
- Keep in mind that the cart stays in the last set instance for as long as you don't set a different one during script execution.

### Get name of current cart
You can easily get current cart instance name by calling `Cart::getInstance()` method.

### Add an item to cart
Adding an item to the cart is really simple, you just use the `Cart::add()` method, which accepts a variety of parameters.

```php
/**
 * Add an item to the cart
 *
 * @param  string|int  $id       Associated model or Unique ID of item before insert to the cart
 * @param  string      $title    Name of item
 * @param  int         $qty      Quantities of item want to add to the cart
 * @param  float       $price    Price of one item
 * @param  array       $options  Array of additional options, such as 'size' or 'color'
 *
 * @return Tolawho\Cart\Item
 */
Cart::add($id, $title[, $qty = 1[, $price = 0[, $options = array()]]]);
```

Example:
```php
// Add an article to the `recent-view` cart
$recentView = Cart::instance('recent-view')->add('AID0782000', 'An example article');

// Add an product to the `shopping` cart
$shoppingItem = Cart::instance('shopping')->add(37, 'Polo T-shirt for men', 5, 17.5, ['color' => 'red', 'size' => 'M']);
```

The result of this method is an instance of `Tolawho\Cart\Item` class (extended from `Illuminate\Support\Collection`) and has structured as follows:
```
{
    hash       : "6afbeca78618c01954c98fbd473fd176",
    id         : 37,
    title      : "Polo T-shirt for men",
    qty        : 5,
    price      : 17.5,
    subtotal   : 87.5,
    options    : {
        color : "red",
        size  : "M"
    },
    associated : null
}
```

So, you can access it's property by method `get()` of Laravel Collection instance.

Example:
```php
// Get title of the cart item
$thisItemId = $shoppingItem->get('title');  // Polo T-shirt for men
```

But with some enhancements to this Item class, you can more easily access entity attributes in a more succinct way:

Example:
```php
// Get title of the cart item
$thisItemId = $shoppingItem->title;         // Polo T-shirt for men

// Get sub total price of this cart item
$thisSubTotal = $shoppingItem->subtotal;        // 87.5

// Get color option of this cart item
$thisSubTotal = $shoppingItem->options->color;  // red
...
```

> **Note:** In the visible attributes of each cart item returned from the cart, there is an important type of information, called a hash. The hash of the cart item is used to distinguish items with different attributes in the cart. So with the same item, when you add to the cart with different attributes, you will have cart items with different hash.

### Update cart item
Update the specified cart item with given quantity or attributes. To do this, you need hash information of cart item.

```php
/**
 * Update an item in the cart with the given ID.
 *
 * @param  string     $itemHash    The unique identifier of the cart item
 * @param  int|array  $attributes  New quantity of item or array of new attributes to update
 *
 * @return Tolawho\Cart\Item|null
 */
Cart::update($itemHash, $quantity);
Cart::update($itemHash, $attributes);
```

Example:
```php
$itemHash = "6afbeca78618c01954c98fbd473fd176";

// Update title and options
$cartItem = Cart::instance('shopping')->update($itemHash, ['title' => 'New item title', 'options' => ['color' => 'yellow']]);

// or only update quantity
$cartItem = Cart::instance('shopping')->update($itemHash, 10);
```

> **Note:** You can only update attributes about title, quantity, price and options. Whenever you update cart item's info, the hash of cart item can be changed.

### Get the specified cart item
Get the specified cart item with given hash of cart item

```php
/**
 * Get an item in the cart by its ID.
 *
 * @param  string  $itemHash  The unique identifier of the cart item
 *
 * @throws Tolawho\Cart\Exceptions\CartInvalidHashException
 *
 * @return Tolawho\Cart\Item
 */
Cart::get($itemHash);
```

Example:
```php
$item = Cart::instance('shopping')->get('c2bb42b0b2a16eb1fb477b68822448de');
```

```
{
    hash       : "c2bb42b0b2a16eb1fb477b68822448de",
    id         : 37,
    title      : "New item title",
    qty        : 5,
    price      : 17.5,
    subtotal   : 87.5,
    options    : {
        color : "yellow",
        size  : "M"
    },
    associated : null
}
```

### Get all cart items
Get all the cart items in the cart.

```php
/**
 * Get cart content
 *
 * @return \Illuminate\Support\Collection
 */
Cart::all();
```

Example:
```php
$items = Cart::instance('shopping')->all();
```

And the results may have the following structure:
```
{
    c2bb42b0b2a16eb1fb477b68822448de: {
        hash       : "c2bb42b0b2a16eb1fb477b68822448de",
        id         : 37,
        title      : "Polo T-shirt for men",
        qty        : 1,
        price      : 17.5,
        subtotal   : 17.5,
        options    : {
            color : "yellow",
            size  : "L"
        },
        associated : null
    },
    6afbeca78618c01954c98fbd473fd176: {
        hash       : "6afbeca78618c01954c98fbd473fd176",
        id         : 37,
        title      : "Polo T-shirt for men",
        qty        : 5,
        price      : 17.5,
        subtotal   : 87.5,
        options    : {
            color : "red",
            size  : "M"
        },
        associated : null
    }
}
```

### Get total price of all cart items
Returns the total price of all items in the cart.

```php
/**
 * Get the total price of all items in the cart.
 *
 * @return float
 */
Cart::total();
```

Example:
```php
$total = Cart::instance('shopping')->total();
```

### Count cart items
Return the number of unique items in the cart.

```php
/**
 * Get number of items in the cart.
 *
 * @return int
 */
Cart::countItems();
```

Example:
```php
Cart::instance('shopping')->add(37, 'Polo T-shirt for men', 5, 100.00, ['color' => 'red', 'size' => 'M']);
Cart::add(37, 'Polo T-shirt for men', 1, 100.00, ['color' => 'red', 'size' => 'M']);
Cart::add(37, 'Polo T-shirt for men', 5, 100.00, ['color' => 'red', 'size' => 'M']);
Cart::add(127, 'Another example product', 15, 100.00, ['color' => 'green', 'size' => 'S']);

$items = Cart::countItems();  // 2 (Polo T-shirt for men, Another example product)
```

### Count quantities of all cart items
Return the number of quantities for all items in the cart.

```php
/**
 * Get quantities of all items in the cart.
 *
 * @return int
 */
Cart::countQuantities();
```

Example:
```php
Cart::instance('shopping')->add(37, 'Polo T-shirt for men', 5, 100.00, ['color' => 'red', 'size' => 'M']);
Cart::add(37, 'Polo T-shirt for men', 1, 100.00, ['color' => 'red', 'size' => 'M']);
Cart::add(37, 'Polo T-shirt for men', 5, 100.00, ['color' => 'red', 'size' => 'M']);
Cart::add(127, 'Another example product', 15, 100.00, ['color' => 'green', 'size' => 'S']);

$quantities = Cart::countQuantities();  // 26
```

### Search cart items
To search cart items in the cart, you must provide array of the cart item's attributes

```php
/**
 * Search the cart items with given filter
 *
 * @param  Closure|array  $filter    A closure or an array with item's attributes
 * @param  boolean        $allScope  Indicates that the results returned must satisfy
 *                                   all the conditions of the filter at the same time
 *                                   or that only parts of the filter.
 *
 * @return Illuminate\Support\Collection;
 */
Cart::search($filter[, $allScope = true]);
```

> **Note:** The `$allScope` parameter use to indicates that the results returned must satisfy all the conditions of the filter at the same time or that only parts of the filter.

Example:
```php
// Get all cart items that have color is red
$items = Cart::instance('shopping')->search([
    'options' => [
        'color' => 'red'
    ]
]);

// Get all cart items that have title is [Polo T-shirt for men] AND size is [L]
$items = Cart::instance('shopping')->search([
    'title'   => 'Polo T-shirt for men',
    'options' => [
        'size' => 'L'
    ]
]);

// Get all cart items that have id is [10], OR size is [M]
$items = Cart::instance('shopping')->search([
    'id'      => 10,
    'options' => [
        'size' => 'M'
    ]
], false);
```

### Remove the specified cart item
Remove the specified cart item by its hash.

```php
/**
 * Remove an cart item with the given hash out of the cart.
 *
 * @param  string  $itemHash  The unique identifier of the cart item
 *
 * @return Tolawho\Cart\Cart
 */
Cart::remove($itemHash);
```

Example:
```php
Cart::instance('shopping')->remove('6afbeca78618c01954c98fbd473fd176');
```

### Destroy your cart
Clean a specified cart with given cart instance name.

```php
/**
 * Remove all items in the cart
 *
 * @return Tolawho\Cart\Cart
 */
Cart::destroy()
```

Example:
```php
Cart::instance('shopping')->destroy();
```

## Advanced usage

### Collections
You need to know that all result of cart content (response by methods such as `Cart::all()`, `Cart::search()`), every cart item (response by methods such as `Cart::add()`, `Cart::get()`, `Cart::update()`) and cart item's options (is property of one cart item) are return a Laravel Collection, so all methods you can call on a Laravel Collection are also available on them.

Example:
```php
$count = Cart::instance('shopping')->all()->count();
```

And another example (group by attribute):
```php
$groupByTitle = Cart::instance('shopping')->all()->groupBy('title');
```

And may be you will see:
```
{
    Polo neck T-shirt for men: [
        {
            hash       : "c2bb42b0b2a16eb1fb477b68822448de",
            id         : 37,
            title      : "Polo neck T-shirt for men",
            qty        : 5,
            price      : 17.5,
            subtotal   : 87.5,
            options    : {
                color : "yellow",
                size  : "M"
            },
            associated : null
        },
        {
            hash       : "6afbeca78618c01954c98fbd473fd176",
            id         : 37,
            title      : "Polo neck T-shirt for men",
            qty        : 10,
            price      : 17.5,
            subtotal   : 175,
            options    : {
                color : "red",
                size  : "M"
            },
            associated : null
        }
    ]
}
```

### Eloquent model association
A special feature of Laravel Cart is association an Eloquent model with the cart item in the cart. Let's say you have a `Product` model in your application. With this feature, you can tell the cart that an item in the cart, is associated to a instance `Product` model. That way, you can access your instance of model right from instance of `Item`!

To be ready for this, Laravel Cart has one interface (with namespace is `Tolawho\Cart\Contracts\Cartable`) and one trait (with namespace is `Tolawho\Cart\Traits\Shopping`). The rest is you just apply for your Eloquent model.

#### Preparing for association:
It's easily to do this. Your Eloquent model just only need implements the `Cartable` interface and use the `Shopping` trait.
```php
<?php namespace App;

use Illuminate\Database\Eloquent\Model;

use Tolawho\Cart\Contracts\Cartable; // Interface
use Tolawho\Cart\Shopping;     // Trait

class Product extends Model implements Cartable {
    use Shopping;

    ...
}

```

#### Make an association
If your model implemented the Cartable interface and you uses your model to add the item to the cart, it will associate automatically.

```php
/**
 * Add an item to the cart
 *
 * @param  object  $model    The Cartable instance
 * @param  int     $qty      Quantities of instance want to add to the cart
 * @param  array   $options  Array of additional options, such as 'size' or 'color'
 *
 * @return Tolawho\Cart\Item
 */
Cart::add($model[, int $qty = 1[, array $options = array()]]);
```

Example:
```php
// Get your model
$product = Product::find(1);

// Add 5 items of this product to the cart
$cartItem = Cart::instance('shopping')->add($product, 5);

// Or you can do this by another way
$cartItem = $product->addToCart('shopping', 5);
```

And the result will be:
```
{
    hash       : "3fbdcdad4cbcee36f36ee15d89505d54",
    id         : 1,
    title      : "Polo neck T-shirt for men",
    qty        : 5,
    price      : 17.5,
    subtotal   : 87.5,
    options    : {
        color : "red",
        size  : "M"
    },
    associated : "App\Product"
}
```

Please, notice the associated attribute of the cart item. Do you see the value is `App\Product`? Wow! now you can be able to directly access your model from this cart item instance through `model` property. Example:

```php
return $cartItem->model->name; // Get name of your product
```

#### Configuration for association
When your model implemented the Cartable interface and you uses your model to add the item to the cart, Laravel Cart will get automatically attributes `title`, `price` in your Eloquent model to use for inserting item to the cart.

But if your Eloquent model doesn't have these attributes (for example, your model field used to store product's name is not a `title` but `name`), you need to reconfigure it so that automatic information retrieval is done correctly. You can do this easily by placing your Eloquent model in two attributes:

```php
<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Tolawho\Cart\Contracts\Cartable;
use Tolawho\Cart\Shopping;

class Product extends Model implements Cartable {
    use Shopping;

    protected $cartTitleField = 'name'; // Your correctly field for product's title
    protected $cartPriceField = 'unit_price'; // Your correctly field for product's price

    ...
}

```

#### Some other methods for associated model

```php
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
 * @return Illuminate\Support\Collection
 */
public function allFromCart($cartInstance = null);

/**
 * Get the Cartable items in the cart with given additional options
 *
 * @param  string|null  $cartInstance  The cart instance name
 * @param  array        $options       Array of additional options, such as 'size' or 'color'
 *
 * @return Illuminate\Support\Collection
 */
public function searchInCart($cartInstance = null, array $options = []);
```

Example:
```php
$product = Product::find(1);
return ($product->hasInCart('shopping')) ? 'Yes' : 'No';
```

### Custom search building
At the above, we knew how to do a searching for items in the cart. And because informations of each cart item is a Laravel Collection, so that you can easily building a custom searching with placing a Closure into `Cart::search()` method. Laravel Cart will use this Closure with `filter()` method of Collection. Example:

```php
$results = Cart::instance('shopping')->search(function ($cartItem) {
        return $cartItem->hash === 1 && $cartItem->associated === "Product";
    });
```

You can refer to Collection on the Laravel homepage.

### Dependency injection
In version 2.x of this package, it's possibly to use dependency injection to inject an instance of the Cart class into your controller or other class. Example:

```php
<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Product;
use Tolawho\Cart\Cart;

class TestCartController extends Controller {

    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function content()
    {
        $cart = $this->cart->instance('shopping')->all();
    }

    public function add()
    {
        $product = Product::find(1);
        $cartItem = $this->cart->instance('shopping')->add($product, 5);
    }
}

```

### Event and listener
The Laravel Cart package has events build in. Currently, there are eight events available for you to listen for.

| Event Name        | Fired                                        | Parameters          |
| ----------------- | -------------------------------------------- | ------------------- |
| *cart.adding*     | When an item is being added to the cart.     | ($cartItem, $cart); |
| *cart.added*      | When an item was added to the the cart.      | ($cartItem, $cart); |
| *cart.updating*   | When an item in the cart is being updated.   | ($cartItem, $cart); |
| *cart.updated*    | When an item in the cart was updated.        | ($cartItem, $cart); |
| *cart.removing*   | When an item is being removed from the cart. | ($cartItem, $cart); |
| *cart.removed*    | When an item was removed from the cart.      | ($cartItem, $cart); |
| *cart.destroying* | When a cart is being destroyed.              | ($cart);            |
| *cart.destroyed*  | When a cart was destroyed.                   | ($cart);            |

You can easily handle these events. For example, we can listen events through the Event facade (I usually use this way with Laravel 4+. But now, in Laravel 5+, we have a better way to listen, please refer at [Laravel homepage](http://laravel.com)):
```php
<?php

use Illuminate\Support\Facades\Event;
...
    Event::on('cart.adding', function($cartItem, $cart){
        // code
    });
...

```

### Exceptions
The Laravel Cart package will throw exceptions if something goes wrong. This way it's easier to debug your code using the Laravel Cart package or to handle the error based on the type of exceptions. The Laravel Cart packages can throw the following exceptions:

| Exception                      | Reason                                                                     |
| ------------------------------ | -------------------------------------------------------------------------- |
| *CartInvalidArgumentException* | When you missed or entered invalid argument (such as title, qty...).       |
| *CartInvalidHashException*     | When the hash information you provided doesn't exists in the current cart. |
| *CartInvalidModelException*    | When an associated model of the cart item row doesn't exists.              |

## Thanks for use
Hopefully, this package is useful to you.