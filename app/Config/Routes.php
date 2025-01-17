<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Dashboard::index');

$routes->get('portofolio-form', 'Portofolio::index');
$routes->get('portofolio-form/info-matkul', 'Portofolio::info_matkul');
$routes->get('portofolio-form/topik-perkuliahan', 'Portofolio::topik_perkuliahan');
$routes->get('portofolio-form/cpl-ikcp', 'Portofolio::cpl_ikcp');
$routes->get('portofolio-form/cpmk-subcpmk', 'Portofolio::cpmk_subcpmk');
$routes->get('portofolio-form/cetak', 'Portofolio::cetak');
$routes->get('portofolio-form/upload-rps', 'Portofolio::upload_rps');
$routes->get('portofolio-form/rancangan-asesmen', 'Portofolio::rancangan_asesmen');

$routes->post('/portofolio/saveInfoMatkul', 'Portofolio::saveInfoMatkul');
