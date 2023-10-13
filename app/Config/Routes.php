<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
    $routes->get('profile', 'AuthController::profile', ['filter' => 'apiauth']);
    $routes->get('logout', 'AuthController::logout', ['filter' => 'apiauth']);

    $routes->post('add-project', 'ProjectController::addProject', ['filter' => 'apiauth']);
    $routes->post('list-projects', 'ProjectController::listProjects');
    $routes->delete('delete-project/(:any)', 'ProjectController::deleteProject');

    $routes->get('invalid', 'AuthController::invalidRequest');
});