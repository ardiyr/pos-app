<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'CashierController::index');
$routes->get('/api/search', 'CashierController::searchProducts');
$routes->post('/api/checkout', 'CashierController::processCheckout');
$routes->get('/api/orders/pending', 'CashierController::getPendingOrders');
$routes->post('/api/checkout/pay/(:num)', 'CashierController::payOrder/$1');
$routes->get('/invoice/print/(:num)', 'CashierController::invoicePrint/$1');

// Auth
$routes->get('/login', 'AuthController::index');
$routes->post('/login/authenticate', 'AuthController::authenticate');
$routes->get('/logout', 'AuthController::logout');

// Dashboard
$routes->get('/dashboard', 'DashboardController::index');

// Products CRUD
$routes->group('products', function($routes) {
    $routes->get('/', 'ProductController::index');
    $routes->post('store', 'ProductController::store');
    $routes->post('update/(:num)', 'ProductController::update/$1');
    $routes->post('delete/(:num)', 'ProductController::delete/$1');
});

// Transactions History
$routes->group('transactions', function($routes) {
    $routes->get('history', 'TransactionController::history');
    $routes->get('details/(:num)', 'TransactionController::details/$1');
    $routes->post('delete/(:num)', 'TransactionController::delete/$1');
});

// Users Management (Admin Only)
$routes->group('users', function($routes) {
    $routes->get('/', 'UserController::index');
    $routes->post('store', 'UserController::store');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->post('delete/(:num)', 'UserController::delete/$1');
});
