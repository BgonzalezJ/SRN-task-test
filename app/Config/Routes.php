<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'TasksController::home');

$routes->group('tasks', static function($routes) {
    $routes->get('/', 'TasksController::index');
    $routes->get('(:num)', 'TasksController::show/$1');
    $routes->post('/', 'TasksController::create');
    $routes->put('(:num)', 'TasksController::update/$1');
    $routes->delete('(:num)', 'TasksController::delete/$1');
});