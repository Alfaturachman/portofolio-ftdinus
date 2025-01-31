<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Dashboard::index');

// Rute untuk autentikasi (login, register, logout)
$routes->group('auth', function ($routes) {
    // Rute GET
    $routes->get('login', 'Auth::login');
    // $routes->get('register', 'Auth::register');

    // Rute POST
    $routes->post('process-login', 'Auth::processLogin');
    $routes->post('process-register', 'Auth::processRegister');

    // Rute logout
    $routes->get('logout', 'Auth::logout');
});

// Rute untuk form portofolio
$routes->group('portofolio-form', function ($routes) {
    // Rute GET
    $routes->get('/', 'Portofolio::index');
    $routes->get('view-pdf', 'Portofolio::view_pdf');
    $routes->get('upload-rps', 'Portofolio::upload_rps');
    $routes->get('info-matkul', 'Portofolio::info_matkul');
    $routes->get('cpl-pi', 'Portofolio::cpl_pi');
    $routes->get('cpmk-subcpmk', 'Portofolio::cpmk_subcpmk');
    $routes->get('pemetaan', 'Portofolio::pemetaan');
    $routes->get('rancangan-asesmen', 'Portofolio::rancangan_asesmen');
    $routes->get('pelaksanaan-perkuliahan', 'Portofolio::pelaksanaan_perkuliahan');
    $routes->get('hasil-asesmen', 'Portofolio::hasil_asesmen');
    $routes->get('evaluasi-perkuliahan', 'Portofolio::evaluasi_perkuliahan');

    // Rute POST
    $routes->post('save-upload-rps', 'Portofolio::saveUploadRps');
    $routes->post('saveInfoMatkul', 'Portofolio::saveInfoMatkul');
    $routes->post('saveTopikPerkuliahan', 'Portofolio::saveTopikPerkuliahan');
});

// Rute untuk mengakses file PDF yang diupload
$routes->get('uploads/temp/(:segment)', 'Portofolio::view_uploaded_pdf/$1');
