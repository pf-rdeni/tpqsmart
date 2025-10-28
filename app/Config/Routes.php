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
$routes->get('/', 'Auth::index');
$routes->post('auth/updateTahunAjaranDanKelas', 'Auth::updateTahunAjaranDanKelas');
//$routes->get('/', 'Backend\Pages::index');
//$routes->get('/', 'Frontend\Home::index');
$routes->get('program', 'Frontend\Program::index');
$routes->get('kontak', 'Frontend\Kontak::index');

// Routes untuk pendaftaran santri menggunakan controller SantriPendaftaran
// Public routes (tanpa login)
$routes->get('pendaftaran', 'SantriPendaftaran::createEmisStep/public');
$routes->post('pendaftaran/save', 'SantriPendaftaran::save/public');
$routes->get('pendaftaran/success/(:segment)', 'SantriPendaftaran::showSuccessEmisStep/$1/public');
$routes->get('pendaftaran/getNikSantri/(:segment)', 'SantriPendaftaran::getNikSantri/$1');
$routes->get('pendaftaran/generatePDFSantriBaru/(:segment)', 'SantriPendaftaran::generatePDFSantriBaru/$1');

// Admin routes (dengan login)
$routes->get('backend/santri', 'SantriPendaftaran::createEmisStep/admin');
$routes->post('backend/santri/save', 'SantriPendaftaran::save/admin');
$routes->get('backend/santri/showSuccessEmisStep/(:segment)', 'SantriPendaftaran::showSuccessEmisStep/$1/admin');
$routes->get('backend/santri/getNikSantri/(:segment)', 'SantriPendaftaran::getNikSantri/$1');
$routes->get('backend/santri/generatePDFSantriBaru/(:segment)', 'SantriPendaftaran::generatePDFSantriBaru/$1');
//Table Tpq
$routes->get('backend/tpq/tpq', 'Tpq::create');
$routes->delete('backend/tpq/(:num)', 'Tpq::delete/$1');
$routes->get('backend/tpq/profilLembaga', 'Backend\Tpq::profilLembaga');
$routes->get('backend/tpq/edit/(:segment)', 'Backend\Tpq::edit/$1');
$routes->post('backend/tpq/update/(:segment)', 'Backend\Tpq::update/$1');
$routes->post('backend/tpq/uploadLogo', 'Backend\Tpq::uploadLogo');
$routes->post('backend/tpq/uploadKop', 'Backend\Tpq::uploadKop');

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
$routes->get('backend/kelas/showListSantriPerKelas/(:any)', 'Backend\Kelas::showListSantriPerKelas/$1');
$routes->get('backend/kelas/updateNaikKelas/(:num)/(:num)', 'Backend\Kelas::updateNaikKelas/$1/$2');
$routes->get('backend/kelas/showSantriPerKelas/(:any)', 'Backend\Kelas::showSantriPerKelas/$1');
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
$routes->post('GuruKelas/store', 'GuruKelas::store');
$routes->get('edit/(:num)', 'GuruKelas::edit/$1');
$routes->post('update/(:num)', 'GuruKelas::update/$1');
$routes->get('backend/GuruKelas/delete/(:num)', 'Backend\GuruKelas::delete/$1');

//Table tbl_struktur_lembaga
$routes->get('backend/strukturlembaga', 'Backend\StrukturLembaga::index');
$routes->get('backend/strukturlembaga/create', 'Backend\StrukturLembaga::create');
$routes->post('backend/strukturlembaga/store', 'Backend\StrukturLembaga::store');
$routes->get('backend/strukturlembaga/edit/(:num)', 'Backend\StrukturLembaga::edit/$1');
$routes->post('backend/strukturlembaga/update/(:num)', 'Backend\StrukturLembaga::update/$1');
$routes->get('backend/strukturlembaga/delete/(:num)', 'Backend\StrukturLembaga::delete/$1');

$routes->group('backend', ['namespace' => 'App\Controllers\Backend'], function ($routes) {
    $routes->get('rapor/(:segment)', 'Rapor::index/$1');
    $routes->get('rapor/getSantriByKelas/(:num)', 'Rapor::getSantriByKelas/$1');
    $routes->get('rapor/previewRapor/(:num)', 'Rapor::previewRapor/$1');
    $routes->get('rapor/printPdf/(:num)/(:segment)', 'Rapor::printPdf/$1/$2');
    $routes->get('rapor/printPdfBulk/(:num)/(:segment)', 'Rapor::printPdfBulk/$1/$2');

    // QR Routes
    $routes->get('qr', 'Qr::index');
    $routes->get('qr/generate', 'Qr::generate');
    $routes->post('qr/generate', 'Qr::generate');
    $routes->get('qr/print', 'Qr::print');

    // Profil Santri
    $routes->get('santri/showProfilSantri', 'Santri::showProfilSantri');
    $routes->get('santri/profilDetailSantri/(:segment)', 'Santri::profilDetailSantri/$1');
    $routes->get('santri/generatePDFprofilSantriRaport/(:segment)', 'Santri::generatePDFprofilSantriRaport/$1');

    // Munaqosah Routes
    $routes->get('munaqosah', 'Munaqosah::index');
    $routes->get('munaqosah/nilai', 'Munaqosah::nilai');
    $routes->get('munaqosah/input-nilai', 'Munaqosah::inputNilai');
    $routes->post('munaqosah/save-nilai', 'Munaqosah::saveNilai');
    $routes->get('munaqosah/edit-nilai/(:num)', 'Munaqosah::editNilai/$1');
    $routes->post('munaqosah/update-nilai/(:num)', 'Munaqosah::updateNilai/$1');
    $routes->get('munaqosah/delete-nilai/(:num)', 'Munaqosah::deleteNilai/$1');

    $routes->get('munaqosah/antrian', 'Munaqosah::antrian');
    $routes->get('munaqosah/input-antrian', 'Munaqosah::inputAntrian');
    $routes->post('munaqosah/save-antrian', 'Munaqosah::saveAntrian');
    $routes->post('munaqosah/update-status-antrian/(:num)', 'Munaqosah::updateStatusAntrian/$1');
    $routes->get('munaqosah/delete-antrian/(:num)', 'Munaqosah::deleteAntrian/$1');

    $routes->get('munaqosah/bobot', 'Munaqosah::bobotNilai');
    $routes->post('munaqosah/save-bobot', 'Munaqosah::saveBobotNilai');
    $routes->post('munaqosah/update-bobot/(:num)', 'Munaqosah::updateBobotNilai/$1');
    $routes->post('munaqosah/delete-bobot/(:num)', 'Munaqosah::deleteBobotNilai/$1');

    $routes->get('munaqosah/peserta', 'Munaqosah::pesertaMunaqosah');
    $routes->post('munaqosah/save-peserta', 'Munaqosah::savePesertaMunaqosah');
    $routes->post('munaqosah/save-peserta-multiple', 'Munaqosah::savePesertaMunaqosahMultiple');
    $routes->get('munaqosah/check-data-terkait/(:num)', 'Munaqosah::checkDataTerkait/$1');
    $routes->delete('munaqosah/delete-peserta/(:num)', 'Munaqosah::deletePesertaMunaqosah/$1');
    $routes->delete('munaqosah/delete-peserta-by-santri/(:num)', 'Munaqosah::deletePesertaBySantri/$1');

    // Routes untuk registrasi peserta munaqosah
    $routes->get('munaqosah/registrasi-peserta', 'Munaqosah::registrasiPesertaMunaqosah');
    $routes->get('munaqosah/get-santri-for-registrasi', 'Munaqosah::getSantriForRegistrasi');
    $routes->post('munaqosah/process-registrasi-peserta', 'Munaqosah::processRegistrasiPeserta');
    $routes->post('munaqosah/print-kartu-ujian', 'Munaqosah::printKartuUjian');

    // Routes untuk edit peserta munaqosah
    $routes->post('munaqosah/get-detail-santri', 'Munaqosah::getDetailSantri');
    $routes->post('munaqosah/update-santri', 'Munaqosah::updateSantri');

    $routes->get('munaqosah/materi', 'Munaqosah::materiMunaqosah');
    $routes->post('munaqosah/save-materi', 'Munaqosah::saveMateriMunaqosah');
    $routes->post('munaqosah/save-materi-batch', 'Munaqosah::saveMateriBatch');
    $routes->post('munaqosah/save-materi-batch-confirm', 'Munaqosah::saveMateriBatchWithConfirmation');

    // Grup Materi Ujian routes
    $routes->get('munaqosah/grup-materi-ujian', 'Munaqosah::grupMateriUjian');
    $routes->post('munaqosah/save-grup-materi-ujian', 'Munaqosah::saveIdGrupMateriUjian');
    $routes->post('munaqosah/update-grup-materi-ujian/(:num)', 'Munaqosah::updateIdGrupMateriUjian/$1');
    $routes->post('munaqosah/delete-grup-materi-ujian/(:num)', 'Munaqosah::deleteIdGrupMateriUjian/$1');
    $routes->get('munaqosah/get-grup-materi-aktif', 'Munaqosah::getGrupMateriAktif');
    $routes->get('munaqosah/get-next-id-grup-materi-ujian', 'Munaqosah::getNextIdGrupMateriUjian');
    $routes->post('munaqosah/update-materi/(:num)', 'Munaqosah::updateMateriMunaqosah/$1');
    $routes->post('munaqosah/update-status-materi/(:num)', 'Munaqosah::updateStatusMateri/$1');
    $routes->post('munaqosah/update-grup-materi/(:num)', 'Munaqosah::updateGrupMateri/$1');
    $routes->post('munaqosah/save-bobot-batch', 'Munaqosah::saveBobotBatch');
    $routes->post('munaqosah/delete-bobot-by-tahun', 'Munaqosah::deleteBobotByTahun');
    $routes->get('munaqosah/get-default-bobot', 'Munaqosah::getDefaultBobot');
    $routes->get('munaqosah/get-bobot-by-tahun/(:any)', 'Munaqosah::getBobotByTahun/$1');
    $routes->get('munaqosah/get-tahun-ajaran-options', 'Munaqosah::getTahunAjaranOptions');
    $routes->post('munaqosah/duplicate-bobot-data', 'Munaqosah::duplicateBobotData');
    $routes->post('munaqosah/duplicate-default-bobot', 'Munaqosah::duplicateDefaultBobot');
    $routes->post('munaqosah/delete-materi/(:num)', 'Munaqosah::deleteMateriMunaqosah/$1');

    // API Routes
    $routes->get('munaqosah/api/santri/(:num)/(:num)', 'Munaqosah::getSantriData/$1/$2');
    $routes->get('munaqosah/api/tpq', 'Munaqosah::getTpqData');
    $routes->get('munaqosah/api/guru', 'Munaqosah::getGuruData');
    $routes->get('munaqosah/api/materi', 'Munaqosah::getMateriData');
    $routes->get('munaqosah/api/statistik', 'Munaqosah::getStatistikData');
    $routes->get('munaqosah/api/nilai/(:segment)', 'Munaqosah::getNilaiByPeserta/$1');
    $routes->get('munaqosah/api/antrian/(:segment)', 'Munaqosah::getAntrianByStatus/$1');
    $routes->get('munaqosah/api/bobot/(:segment)', 'Munaqosah::getBobotByTahunAjaran/$1');
    $routes->get('munaqosah/api/peserta/(:segment)', 'Munaqosah::getPesertaByTpq/$1');

    // API Routes untuk data master
    $routes->get('backend/tpq/get-all', 'Tpq::getAll');
});

$routes->get('signature/validateSignature/(:segment)', 'Frontend\\Signature::validateSignature/$1');
$routes->get('signature/santri/(:num)', 'Frontend\\Signature::getSignaturesBySantri/$1');
$routes->get('signature/guru/(:num)', 'Frontend\\Signature::getSignaturesByGuru/$1');
$routes->get('signature/tpq/(:num)', 'Frontend\\Signature::getSignaturesByTpq/$1');

$routes->get('logout', 'Auth::logout');

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
