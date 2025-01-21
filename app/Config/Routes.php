<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Dashboard::index');

$routes->get('portofolio-form/view-pdf', 'Portofolio::view_pdf');
$routes->get('uploads/temp/(:segment)', 'Portofolio::view_uploaded_pdf/$1');

$routes->get('portofolio-form', 'Portofolio::index');
$routes->get('portofolio-form/upload-rps', 'Portofolio::upload_rps');
$routes->get('portofolio-form/info-matkul', 'Portofolio::info_matkul');
$routes->get('portofolio-form/topik-perkuliahan', 'Portofolio::topik_perkuliahan');
$routes->get('portofolio-form/cpl-pi', 'Portofolio::cpl_pi');
$routes->get('portofolio-form/cpmk-subcpmk', 'Portofolio::cpmk_subcpmk');
$routes->get('portofolio-form/pemetaan', 'Portofolio::pemetaan');
$routes->get('portofolio-form/rancangan-asesmen', 'Portofolio::rancangan_asesmen');
$routes->get('portofolio-form/nilai-cpmk', 'Portofolio::nilai_cpmk');

$routes->post('portofolio-form/save-upload-rps', 'Portofolio::saveUploadRps');
$routes->post('portofolio/saveInfoMatkul', 'Portofolio::saveInfoMatkul');
$routes->post('portofolio/saveTopikPerkuliahan', 'Portofolio::saveTopikPerkuliahan');
