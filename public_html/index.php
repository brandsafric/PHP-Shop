<?php

$actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if (strpos($actual_link, '//')){
    while (strpos($actual_link, '//')){
        $actual_link = str_replace('//', '/', $actual_link);
    }
    header ('Location: ' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . '://' . $actual_link);
}
require '../vendor/autoload.php';
require '../Core/functions.php';

init();

$router = new Core\Router();
$router->get('', 'Home@index');
$router->get('products/{id}', 'Products@show');
$router->post('products/{id}/add-review', 'Products@addReview');
$router->get('search', 'Home@search');
$router->post('set-sort-type', 'Home@setSortAndProductsPerPage');
$router->get('cart', 'ProductOrders@cart');
$router->put('cart/{id}', 'ProductOrders@update');
$router->delete('cart/{id}', 'ProductOrders@destroy');
$router->get('checkout', 'ProductOrders@checkout');
$router->post('checkout', 'ProductOrders@store');

$router->get('signin', 'Users@index');
$router->post('signin', 'Users@signin');
$router->get('forgot-password', 'Users@forgot_password');
$router->post('forgot-password', 'Users@forgot_password_post');
$router->get('recovery', 'Users@recovery_password');
$router->post('recovery', 'Users@recovery_password_post');
$router->get('signup', 'Users@registerIndex');
$router->post('signup', 'Users@register');
$router->get('logout', 'Users@logout');
$router->get('edit-profile', 'Users@edit');
$router->put('edit-profile', 'Users@editProfile');

$router->post('order', 'Orders@store');
$router->post('remove-ordered-product', 'Orders@removeOrderedProduct');
$router->get('category/{category}', 'Home@index');

if(\Core\Auth::isAdmin()) {
    $router->get('admin', 'Admin@index');
    $router->get('admin/settings', 'Admin@settings');
    $router->post('admin/get-pictures', 'Admin@getPictures');
    $router->post('admin/get-gallery-pictures', 'Admin@getGalleryPictures');
    $router->post('admin/set-product-picture', 'Admin@setProductPicture');
    $router->post('admin/set-gallery-pictures', 'Admin@setGalleryPictures');

    //Pictures
    $router->get('admin/pictures/create', 'Pictures@create');
    $router->post('admin/upload', 'Pictures@store');
    $router->post('admin/select-picture', 'Admin@selectPicture');

    //Settings
    $router->post('admin/thumbnails-settings', 'Admin@storeSettings');

    //Categories
    $router->get('admin/categories/create', 'Categories@create');
    $router->post('admin/categories', 'Categories@store');
    $router->get('admin/categories', 'Categories@index');
    $router->put('admin/categories/{id}', 'Categories@update');
    $router->delete('admin/categories/{id}', 'Categories@destroy');

    //Products
    $router->get('admin/products', 'Products@indexAdmin');
    $router->get('admin/products/create', 'Products@create');
    $router->put('admin/products/{id}', 'Products@update');
    $router->get('admin/products/{id}/edit', 'Products@edit');
    $router->delete('admin/products/{id}', 'Products@destroy');
    $router->post('admin/products', 'Products@store');

    //Users
    $router->get('admin/users', 'Users@indexAdmin');
    $router->get('admin/users/{id}/edit', 'Users@editAdmin');
    $router->put('admin/users/{id}', 'Users@updateAdmin');
    $router->delete('admin/users/{id}', 'Users@destroyAdmin');

    //Orders
    $router->get('admin/orders', 'Orders@index');
    $router->get('admin/orders/{id}', 'Orders@show');
    $router->put('admin/orders/{id}', 'Orders@update');

    //ProductOrders
    $router->put('admin/productorders/{id}', 'ProductOrders@updateAdmin');
}

$url=$_SERVER['QUERY_STRING'];
if($url!=''){
    ($_SERVER['QUERY_STRING'][strlen($_SERVER['QUERY_STRING'])-1])=='/'
        ? $_SERVER['QUERY_STRING']
        : $_SERVER['QUERY_STRING'] .'/';
}
$router->dispatch($url);

