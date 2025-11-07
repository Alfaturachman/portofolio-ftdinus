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
    $routes->get('rancangan-soal', 'Portofolio::rancangan_soal');
    $routes->get('nilai-soal', 'Portofolio::nilai_soal');
    $routes->get('pelaksanaan-perkuliahan', 'Portofolio::pelaksanaan_perkuliahan');
    $routes->get('hasil-asesmen', 'Portofolio::hasil_asesmen');
    $routes->get('evaluasi-perkuliahan', 'Portofolio::evaluasi_perkuliahan');
    $routes->get('tes-cetak', 'Portofolio::tes_cetak');
    $routes->get('daftar/(:segment)/(:segment)/(:segment)', 'Portofolio::daftar/$1/$2/$3');
    $routes->get('getMahasiswaByKelas/(:segment)', 'Portofolio::getMahasiswaByKelas/$1');

    // Rute POST
    $routes->post('saveUploadRps', 'Portofolio::saveUploadRps');
    $routes->post('saveInfoMatkul', 'Portofolio::saveInfoMatkul');
    $routes->post('saveTopikPerkuliahan', 'Portofolio::saveTopikPerkuliahan');

    $routes->post('saveCPMKToSession', 'Portofolio::saveCPMKToSession');
    $routes->get('getCPMKFromSession', 'Portofolio::getCPMKFromSession');

    $routes->post('saveMappingToSession', 'Portofolio::saveMappingToSession');
    $routes->post('saveAssessmentToSession', 'Portofolio::saveAssessmentToSession');
    $routes->post('saveAssessmentWithFiles', 'Portofolio::saveAssessmentWithFiles');
    $routes->post('saveSoalMapping', 'Portofolio::saveSoalMapping');
    $routes->post('saveNilaiSoal', 'Portofolio::saveNilaiSoal');
    $routes->post('savePelaksanaanPerkuliahan', 'Portofolio::savePelaksanaanPerkuliahan');
    $routes->post('saveHasilAsesmen', 'Portofolio::saveHasilAsesmen');
    $routes->post('saveEvaluasiPerkuliahan', 'Portofolio::saveEvaluasiPerkuliahan');

    $routes->post('save-portofolio', 'Portofolio::savePortofolio');
});

// Rute untuk mengakses file PDF yang diupload
$routes->get('uploads/rps/(:segment)', 'Portofolio::view_uploaded_pdf/$1');

// Rute form portofolio
$routes->group('portofolio-detail', function ($routes) {
    // Rute GET
    $routes->get('/', 'PortofolioDetail::index');
});

// Rute cetak PDF
$routes->get('/view-pdf/(:segment)', 'Cetak::index/$1');
$routes->get('/cetak-pdf/(:segment)', 'Cetak::generatePdf/$1');
$routes->get('files/pdf/(:segment)', 'Cetak::show/$1');

$routes->get('/cetak', 'Cetak::cetakPortofolioPdf');

$routes->get('/generate-pdf', 'Cetak::generatePdf');

$routes->group('import-data', function ($routes) {
    // Rute GET
    $routes->get('/', 'ImportData::index');

    // Rute POST
    $routes->post('saveImportCplPi', 'ImportData::saveImportCplPi');
    $routes->post('saveImportMatkul', 'ImportData::saveImportMatkul');
    $routes->post('saveImportMatkulDiampu', 'ImportData::saveImportMatkulDiampu');
    $routes->post('saveMahasiswaKelas', 'ImportData::saveMahasiswaKelas');
});

$routes->get('downloads/template_cpl_pi.xlsx', 'ImportData::downloadTemplateCplPi');
$routes->get('downloads/template_matkul.xlsx', 'ImportData::downloadTemplateMatkul');
$routes->get('downloads/template_matkul_diampu.xlsx', 'ImportData::downloadTemplateMatkulDiampu');
