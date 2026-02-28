<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/', 'Login::index');
$routes->post('login/proses', 'Login::proses');
$routes->get('logout', 'Login::logout');
$routes->get('dashboard', 'Dashboard::index');

// ── Portofolio index & form ──────────────────────────
$routes->get('admin/portofolio', 'Portofolio::index');
$routes->get('admin/portofolio/add', 'Portofolio::add');
$routes->get('admin/portofolio/form/(:any)', 'Portofolio::form/$1');
$routes->post('admin/portofolio/start/(:any)', 'Portofolio::start/$1');

// ── Per-step AJAX savers ─────────────────────────────
// Step 1 — Upload RPS (multipart)
$routes->post('admin/portofolio/step/rps',             'Portofolio::saveRPS');
$routes->get('admin/portofolio/rps/(:any)', 'Portofolio::serveRPS/$1');

// Serve asesmen files
$routes->get('admin/portofolio/asesmen/(:any)', 'Portofolio::serveAsesmen/$1');
$routes->get('admin/portofolio/preview-asesmen/(:any)', 'Portofolio::previewAsesmen/$1');

// Step 2 — Info Mata Kuliah (JSON or form-encoded)
$routes->post('admin/portofolio/step/info-mk',          'Portofolio::saveInfoMK');

// Step 3 — CPL & PI (just advance step, no payload)
$routes->post('admin/portofolio/step/cpl',              'Portofolio::saveCPL');

// Step 4 — CPMK & Sub CPMK (JSON)
$routes->post('admin/portofolio/step/cpmk',             'Portofolio::saveCPMK');

// Step 5 — Pemetaan CPL-CPMK-SubCPMK (JSON)
$routes->post('admin/portofolio/step/mapping',          'Portofolio::saveMapping');

// Step 6 — Rancangan Asesmen (multipart + JSON field)
$routes->post('admin/portofolio/step/asesmen',          'Portofolio::saveAsesmen');

// Step 7 — Rancangan Soal (JSON)
$routes->post('admin/portofolio/step/soal',             'Portofolio::saveSoal');

// Step 8 — Pelaksanaan Perkuliahan (multipart)
$routes->post('admin/portofolio/step/pelaksanaan',      'Portofolio::savePelaksanaan');

// Step 9 — Hasil Asesmen (multipart)
$routes->post('admin/portofolio/step/hasil-asesmen',    'Portofolio::saveHasilAsesmen');

// Step 10 — Evaluasi (JSON)
$routes->post('admin/portofolio/step/evaluasi',         'Portofolio::saveEvaluasi');

// Route cetak portofolio
$routes->group('cetak', function ($routes) {
    // Preview HTML di browser
    $routes->get('(:segment)', 'Cetak::index/$1');

    // Download PDF hasil gabungan
    $routes->get('pdf/(:segment)', 'Cetak::generatePdf/$1');

    // Tampilkan file PDF inline (preview lampiran)
    $routes->get('file/(:segment)/(:segment)', 'Cetak::show/$1/$2');
});

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {


    // Users        
    $routes->get('dashboard',         'Dashboard::index');
    $routes->get('users',             'UsersController::index');
    $routes->get('users/data',        'UsersController::getData');
    $routes->post('users/store',      'UsersController::store');
    $routes->get('users/(:segment)',  'UsersController::show/$1');
    $routes->post('users/update/(:segment)', 'UsersController::update/$1');
    $routes->post('users/delete/(:segment)', 'UsersController::delete/$1');
    $routes->post('users/import', 'UsersController::importUsers');
    $routes->get('users/template',       'UsersController::downloadTemplate');

    // Kurikulum
    $routes->get('kurikulum',             'KurikulumController::index');
    $routes->get('kurikulum/data',        'KurikulumController::getData');
    $routes->post('kurikulum/store',      'KurikulumController::store');
    $routes->get('kurikulum/(:num)',      'KurikulumController::show/$1');
    $routes->post('kurikulum/update/(:num)', 'KurikulumController::update/$1');
    $routes->post('kurikulum/delete/(:num)', 'KurikulumController::delete/$1');

    // Prodi
    $routes->get('prodi',             'ProdiController::index');
    $routes->get('prodi/data',        'ProdiController::getData');
    $routes->post('prodi/store',      'ProdiController::store');
    $routes->get('prodi/(:num)',      'ProdiController::show/$1');
    $routes->post('prodi/update/(:num)', 'ProdiController::update/$1');
    $routes->post('prodi/delete/(:num)', 'ProdiController::delete/$1');

    $routes->get('mk',                'MKController::index');
    $routes->get('mk/data',           'MKController::getData');
    $routes->post('mk/store',         'MKController::store');
    $routes->get('mk/(:num)',         'MKController::show/$1');
    $routes->post('mk/update/(:num)', 'MKController::update/$1');
    $routes->post('mk/delete/(:num)', 'MKController::delete/$1');
    $routes->post('mk/import',        'MKController::import');
    $routes->get('mk/template',       'MKController::downloadTemplate');

    // CPL
    $routes->get('cpl',                'CPLController::index');
    $routes->get('cpl/data',           'CPLController::getData');
    $routes->post('cpl/store',         'CPLController::store');
    $routes->get('cpl/(:num)',         'CPLController::show/$1');
    $routes->post('cpl/update/(:num)', 'CPLController::update/$1');
    $routes->post('cpl/delete/(:num)', 'CPLController::delete/$1');
    $routes->post('cpl/import',        'CPLController::import');
    $routes->get('cpl/template',       'CPLController::downloadTemplate');

    // PI
    $routes->get('pi',                'PiController::index');
    $routes->get('pi/data',           'PiController::getData');
    $routes->post('pi/store',         'PiController::store');
    $routes->get('pi/(:num)',         'PiController::show/$1');
    $routes->post('pi/update/(:num)', 'PiController::update/$1');
    $routes->post('pi/delete/(:num)', 'PiController::delete/$1');
    $routes->post('pi/import',        'PiController::import');
    $routes->get('pi/template',       'PiController::downloadTemplate');


    $routes->get('mapping_cpl',                       'MkCplPiController::index');
    $routes->get('mapping_cpl/data',                  'MkCplPiController::getData');
    $routes->post('mapping_cpl/store',                'MkCplPiController::store');
    $routes->get('mapping_cpl/(:num)',                'MkCplPiController::show/$1');
    $routes->post('mapping_cpl/update/(:num)',        'MkCplPiController::update/$1');
    $routes->post('mapping_cpl/delete/(:num)',        'MkCplPiController::delete/$1');
    $routes->post('mapping_cpl/import',               'MkCplPiController::import');
    $routes->get('mapping_cpl/template',              'MkCplPiController::downloadTemplate');
    // AJAX endpoint untuk filter CPL & PI
    $routes->get('mapping_cpl/cpl/(:num)',            'MkCplPiController::getCplByKurikulum/$1');
    $routes->get('mapping_cpl/pi/(:num)/(:num)',      'MkCplPiController::getPiByKurikulumProdi/$1/$2');

    // Perkuliahan
    $routes->get('perkuliahan',                       'PerkuliahanController::index');
    $routes->get('perkuliahan/data',                  'PerkuliahanController::getData');
    $routes->post('perkuliahan/store',                'PerkuliahanController::store');
    $routes->get('perkuliahan/(:num)',                'PerkuliahanController::show/$1');
    $routes->post('perkuliahan/update/(:num)',        'PerkuliahanController::update/$1');
    $routes->post('perkuliahan/delete/(:num)',        'PerkuliahanController::delete/$1');
    $routes->post('perkuliahan/import',               'PerkuliahanController::import');
    $routes->get('perkuliahan/template',              'PerkuliahanController::downloadTemplate');
});
