<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
//$routes->get('/', 'Auth::index');
$routes->get('/', 'Backend\Pages::index');
//$routes->get('/', 'Frontend\Home::index');
$routes->get('program', 'Frontend\Program::index');
$routes->get('kontak', 'Frontend\Kontak::index');

$routes->get('backend/santri', 'Backend\Santri::createEmisStep');
//Table Tpq
$routes->get('backend/tpq/tpq', 'Tpq::create');
$routes->delete('backend/tpq/(:num)', 'Tpq::delete/$1');

//Table Nilai
$routes->get('nilai/showDetail/(:num)/(:num)', 'Nilai::showDetail/$1/$2');
$routes->get('nilai/showNilaiProfilDetail/(:any)', 'Nilai::showNilaiProfilDetail/$1');

//Table Kelas
$routes->get('kelas', 'Kelas::index');             // List all records (Read)
$routes->get('backend/kelas/create', 'Kelas::create');     // Show form to create a new record (Create)
$routes->post('backend/kelas/store', 'Kelas::store');      // Store new record in database (Create)
$routes->get('backend/kelas/edit/(:num)', 'Kelas::edit/$1');  // Show form to edit a specific record (Read/Update)
$routes->post('backend/kelas/update/(:num)', 'Kelas::update/$1');  // Update a specific record in database (Update)
$routes->get('kelas/delete/(:num)', 'Kelas::delete/$1');  // Delete a specific record from database (Delete)
$routes->get('backend/kelas/showListSantriPerKelas/(:any)', 'Kelas::showListSantriPerKelas/$1');
$routes->get('backend/kelas/updateNaikKelas/(:num)/(:num)', 'Kelas::updateNaikKelas/$1/$2');
$routes->get('backend/kelas/showSantriPerKelas/(:any)', 'Kelas::showSantriPerKelas/$1');
$routes->get('backend/santri/showSuccessEmisStep/(:segment)', 'Backend\Santri::showSuccessEmisStep/$1');

//Tabel Materi
$routes->get('materipelajaran', 'MateriPelajaran::index');
$routes->get('materipelajaran/create', 'MateriPelajaran::create');
$routes->post('materipelajaran/store', 'MateriPelajaran::store');
$routes->get('materipelajaran/edit/(:num)', 'MateriPelajaran::edit/$1');
$routes->post('materipelajaran/update/(:num)', 'MateriPelajaran::update/$1');
$routes->get('materipelajaran/delete/(:num)', 'MateriPelajaran::delete/$1');

//Tabel kelas Materi Pelajaran
$routes->get('kelasMateriPelajaran', 'KelasMateriPelajaran::index');
$routes->get('kelasMateriPelajaran/create', 'KelasMateriPelajaran::create');
$routes->post('kelasMateriPelajaran/store', 'KelasMateriPelajaran::store');
$routes->get('kelasMateriPelajaran/edit/(:num)', 'KelasMateriPelajaran::edit/$1');
$routes->post('kelasMateriPelajaran/update/(:num)', 'KelasMateriPelajaran::update/$1');
$routes->get('kelasMateriPelajaran/delete/(:num)', 'KelasMateriPelajaran::delete/$1');
$routes->get('kelasMateriPelajaran/kelasMateriPelajaranModel/(:segment)/(:segment)', 'KelasMateriPelajaran::kelasMateriPelajaranModel/$1/$2');

//Table tbl_guru_kelas
$routes->get('GuruKelas/show','GuruKelas::show');
$routes->get('GuruKelas/create', 'GuruKelas::create');
$routes->post('GuruKelas/store', 'GuruKelasC::store');
$routes->get('edit/(:num)', 'GuruKelas::edit/$1');
$routes->post('update/(:num)', 'GuruKelasr::update/$1');
$routes->post('delete/(:num)', 'GuruKelas::delete/$1');

$routes->group('backend', ['namespace' => 'App\Controllers\Backend'], function ($routes) {
    $routes->get('rapor/(:segment)', 'Rapor::index/$1');
    $routes->get('rapor/getSantriByKelas/(:num)', 'Rapor::getSantriByKelas/$1');
    $routes->get('rapor/previewRapor/(:num)', 'Rapor::previewRapor/$1');
    $routes->get('rapor/printPdf/(:num)/(:segment)', 'Rapor::printPdf/$1/$2');
    $routes->get('rapor/printPdfBulk/(:num)/(:segment)', 'Rapor::printPdfBulk/$1/$2');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
