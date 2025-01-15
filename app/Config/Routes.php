<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Dashboard::index');
$routes->get('portofolio-form', 'Portofolio::index');
