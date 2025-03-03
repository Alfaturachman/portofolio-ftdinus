<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Dashboard::index');

// Rute autentikasi login
$routes->group('login', function ($routes) {
    // Rute GET
    $routes->get('/', 'Auth::login');

    // Rute POST
    $routes->post('process-login', 'Auth::processLogin');
});

// Rute profile
$routes->get('profile', 'Profile::index');

// Rute autentikasi logout
$routes->get('logout', 'Auth::logout');

// Rute form portofolio
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
    $routes->post('saveUploadRps', 'Portofolio::saveUploadRps');
    $routes->post('saveInfoMatkul', 'Portofolio::saveInfoMatkul');
    $routes->post('saveTopikPerkuliahan', 'Portofolio::saveTopikPerkuliahan');

    $routes->post('saveCPMKToSession', 'Portofolio::saveCPMKToSession');
    $routes->get('getCPMKFromSession', 'Portofolio::getCPMKFromSession');

    $routes->post('saveMappingToSession', 'Portofolio::saveMappingToSession');
    $routes->post('saveAssessmentToSession', 'Portofolio::saveAssessmentToSession');
    $routes->post('saveAssessmentWithFiles', 'Portofolio::saveAssessmentWithFiles');
    $routes->post('pelaksanaan_perkuliahan', 'Portofolio::pelaksanaan_perkuliahan');
    $routes->post('savePelaksanaanPerkuliahan', 'Portofolio::savePelaksanaanPerkuliahan');
    $routes->post('saveHasilAsesmen', 'Portofolio::saveHasilAsesmen');
    $routes->post('saveEvaluasiPerkuliahan', 'Portofolio::saveEvaluasiPerkuliahan');

    $routes->post('save-portofolio', 'Portofolio::savePortofolio');
});

// Rute untuk mengakses file PDF yang diupload
$routes->get('uploads/temp/(:segment)', 'Portofolio::view_uploaded_pdf/$1');
