<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Dashboard::index');

$routes->get('portofolio-form', 'Portofolio::index');
$routes->get('portofolio-form/info-matkul', 'Portofolio::info_matkul');
$routes->get('portofolio-form/topik-perkuliahan', 'Portofolio::topik_perkuliahan');
$routes->post('/portofolio/saveInfoMatkul', 'Portofolio::saveInfoMatkul');
