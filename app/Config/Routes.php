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

// Override Myth Auth routes untuk menggunakan custom AuthController
// Route ini harus didefinisikan SEBELUM Myth Auth routes dimuat agar memiliki prioritas lebih tinggi
$routes->get('login', 'AuthController::login', ['as' => 'login']);
$routes->post('login', 'AuthController::attemptLogin');

$routes->get('/', 'Dashboard::index');
$routes->get('backend/dashboard/select-role', 'Dashboard::selectRole');
$routes->post('backend/dashboard/switch-role', 'Dashboard::switchRole');
$routes->get('backend/dashboard/guru', 'Dashboard::dashboardGuru');
$routes->get('backend/dashboard/operator', 'Dashboard::dashboardOperator');
$routes->get('backend/dashboard/kepala-tpq', 'Dashboard::dashboardKepalaTpq');
$routes->get('backend/dashboard/admin', 'Dashboard::dashboardAdmin');
$routes->post('dashboard/updateTahunAjaranDanKelas', 'Dashboard::updateTahunAjaranDanKelas');
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
$routes->get('pendaftaran/checkMdaStatus/(:any)', 'SantriPendaftaran::checkMdaStatus/$1');
$routes->get('pendaftaran/generatePDFSantriBaru/(:segment)', 'SantriPendaftaran::generatePDFSantriBaru/$1');

// Routes untuk cek status munaqosah (public access)
// Route baru (simplified)
$routes->get('cek-status', 'Frontend\StatusUjianMunaqosah::index');
$routes->get('cek-status/(:segment)', 'Frontend\StatusUjianMunaqosah::index/$1');
// Route lama (backward compatibility)
$routes->get('munaqosah/cek-status', 'Frontend\StatusUjianMunaqosah::index');
$routes->get('munaqosah/cek-status/(:segment)', 'Frontend\StatusUjianMunaqosah::index/$1');
$routes->post('munaqosah/verify-hashkey', 'Frontend\StatusUjianMunaqosah::verifyHashKey');
$routes->get('munaqosah/konfirmasi-data', 'Frontend\StatusUjianMunaqosah::konfirmasiData');
$routes->post('munaqosah/process-konfirmasi', 'Frontend\StatusUjianMunaqosah::processKonfirmasi');
$routes->post('munaqosah/verifikasi-data', 'Frontend\StatusUjianMunaqosah::verifikasiData');
$routes->get('munaqosah/status-proses', 'Frontend\StatusUjianMunaqosah::statusProses');
$routes->get('munaqosah/kelulusan', 'Frontend\StatusUjianMunaqosah::kelulusan');
$routes->get('munaqosah/generate-surat-kelulusan', 'Frontend\StatusUjianMunaqosah::generateSuratKelulusan');

// Admin routes (dengan login)
$routes->get('backend/santri', 'SantriPendaftaran::createEmisStep/admin');
$routes->post('backend/santri/save', 'SantriPendaftaran::save/admin');
$routes->get('backend/santri/showSuccessEmisStep/(:segment)', 'SantriPendaftaran::showSuccessEmisStep/$1/admin');
$routes->get('backend/santri/getNikSantri/(:segment)', 'SantriPendaftaran::getNikSantri/$1');
$routes->get('backend/santri/checkMdaStatus/(:any)', 'Backend\Santri::checkMdaStatus/$1');
$routes->get('backend/santri/generatePDFSantriBaru/(:segment)', 'SantriPendaftaran::generatePDFSantriBaru/$1');
//Table Tpq
$routes->get('backend/tpq/tpq', 'Tpq::create');
$routes->delete('backend/tpq/(:num)', 'Tpq::delete/$1');
$routes->get('backend/tpq/profilLembaga', 'Backend\Tpq::profilLembaga');
$routes->get('backend/tpq/edit/(:segment)', 'Backend\Tpq::edit/$1');
$routes->post('backend/tpq/update/(:segment)', 'Backend\Tpq::update/$1');
$routes->post('backend/tpq/uploadLogo', 'Backend\Tpq::uploadLogo');
$routes->post('backend/tpq/uploadKop', 'Backend\Tpq::uploadKop');

// Routes untuk MDA
$routes->get('backend/mda/show', 'Backend\Mda::show');
$routes->get('backend/mda/create', 'Backend\Mda::create');
$routes->post('backend/mda/save', 'Backend\Mda::save');
$routes->get('backend/mda/edit/(:segment)', 'Backend\Mda::edit/$1');
$routes->post('backend/mda/update/(:segment)', 'Backend\Mda::update/$1');
$routes->post('backend/mda/delete/(:num)', 'Backend\Mda::delete/$1');
$routes->post('backend/mda/uploadLogo', 'Backend\Mda::uploadLogo');
$routes->post('backend/mda/uploadKop', 'Backend\Mda::uploadKop');

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
$routes->get('backend/kelas/showCheckDuplikasiKelasSantri', 'Backend\Kelas::showCheckDuplikasiKelasSantri');
$routes->post('backend/kelas/checkDuplikasiKelasSantri', 'Backend\Kelas::checkDuplikasiKelasSantri');
$routes->post('backend/kelas/normalisasiDuplikasiKelasSantri', 'Backend\Kelas::normalisasiDuplikasiKelasSantri');
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
$routes->get('backend/GuruKelas/getDataByTahunAjaran', 'Backend\GuruKelas::getDataByTahunAjaran');
$routes->get('backend/GuruKelas/getFilterOptions', 'Backend\GuruKelas::getFilterOptions');

//Table tbl_struktur_lembaga
$routes->get('backend/strukturlembaga', 'Backend\StrukturLembaga::index');
$routes->get('backend/strukturlembaga/create', 'Backend\StrukturLembaga::create');
$routes->post('backend/strukturlembaga/store', 'Backend\StrukturLembaga::store');
$routes->get('backend/strukturlembaga/edit/(:num)', 'Backend\StrukturLembaga::edit/$1');
$routes->post('backend/strukturlembaga/update/(:num)', 'Backend\StrukturLembaga::update/$1');
$routes->get('backend/strukturlembaga/delete/(:num)', 'Backend\StrukturLembaga::delete/$1');

$routes->group('backend', ['namespace' => 'App\Controllers\Backend'], function ($routes) {
    // Log Viewer Routes
    $routes->get('logviewer', 'LogViewer::index');
    $routes->post('logviewer/getLogContentByDate', 'LogViewer::getLogContentByDate');
    $routes->get('logviewer/download', 'LogViewer::download');
    $routes->post('logviewer/cleanup', 'LogViewer::cleanup');

    // Routes untuk Kriteria Catatan Raport (harus didefinisikan SEBELUM route umum)
    $routes->get('rapor/kriteriaCatatanRapor', 'Rapor::kriteriaCatatanRapor');
    $routes->post('rapor/saveKriteriaCatatanRapor', 'Rapor::saveKriteriaCatatanRapor');
    $routes->post('rapor/getKriteriaCatatanRapor', 'Rapor::getKriteriaCatatanRapor');
    $routes->post('rapor/updateKriteriaCatatanRapor/(:num)', 'Rapor::updateKriteriaCatatanRapor/$1');
    $routes->post('rapor/deleteKriteriaCatatanRapor/(:num)', 'Rapor::deleteKriteriaCatatanRapor/$1');
    $routes->post('rapor/duplicateKriteriaCatatanRapor', 'Rapor::duplicateKriteriaCatatanRapor');

    // Routes untuk Catatan dan Absensi Rapor
    $routes->post('rapor/getCatatanAbsensi', 'Rapor::getCatatanAbsensi');
    $routes->post('rapor/saveAbsensi', 'Rapor::saveAbsensi');
    $routes->post('rapor/saveCatatan', 'Rapor::saveCatatan');
    $routes->post('rapor/getCatatanDefaultByNilai', 'Rapor::getCatatanDefaultByNilai');
    $routes->post('rapor/getAbsensiFromTable', 'Rapor::getAbsensiFromTable');

    // Routes untuk Mapping Wali Kelas
    $routes->get('rapor/settingMappingWaliKelas', 'Rapor::settingMappingWaliKelas');
    $routes->get('rapor/settingMappingWaliKelas/(:num)', 'Rapor::settingMappingWaliKelas/$1');
    $routes->post('rapor/saveMappingWaliKelas', 'Rapor::saveMappingWaliKelas');
    $routes->post('rapor/deleteMappingWaliKelas', 'Rapor::deleteMappingWaliKelas');

    // Routes untuk Rapor (route umum harus setelah route spesifik)
    // Route untuk semester Ganjil dan Genap
    $routes->get('rapor/Ganjil', 'Rapor::index/Ganjil');
    $routes->get('rapor/Genap', 'Rapor::index/Genap');
    // Route umum untuk segment lainnya (kecuali yang sudah didefinisikan di atas)
    $routes->get('rapor/(:segment)', 'Rapor::index/$1');
    $routes->get('rapor/getSantriByKelas/(:num)', 'Rapor::getSantriByKelas/$1');
    $routes->get('rapor/previewRapor/(:num)', 'Rapor::previewRapor/$1');
    $routes->get('rapor/printPdf/(:num)/(:segment)', 'Rapor::printPdf/$1/$2');
    $routes->get('rapor/printPdfBulk/(:num)/(:segment)', 'Rapor::printPdfBulk/$1/$2');

    // Absensi Routes
    $routes->get('absensi', 'Absensi::index');
    $routes->post('absensi/simpanAbsensi', 'Absensi::simpanAbsensi');
    $routes->get('absensi/getSantriByKelasDanTanggal', 'Absensi::getSantriByKelasDanTanggal');
    $routes->get('absensi/statistikKehadiran', 'Absensi::statistikKehadiran');
    $routes->get('absensi/getStatistikData', 'Absensi::getStatistikData');
    $routes->get('absensi/getStatistikPerSemester', 'Absensi::getStatistikPerSemester');
    $routes->get('absensi/getListSantriStatistik', 'Absensi::getListSantriStatistik');
    $routes->get('absensi/getKehadiranPerKelasPerHari', 'Absensi::getKehadiranPerKelasPerHari');

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
    $routes->get('munaqosah/nilai', 'Munaqosah::nilai');
    $routes->get('munaqosah/input-nilai', 'Munaqosah::inputNilai');
    $routes->post('munaqosah/save-nilai', 'Munaqosah::saveNilai');
    $routes->get('munaqosah/edit-nilai/(:num)', 'Munaqosah::editNilai/$1');
    $routes->post('munaqosah/update-nilai/(:num)', 'Munaqosah::updateNilai/$1');
    $routes->get('munaqosah/delete-nilai/(:num)', 'Munaqosah::deleteNilai/$1');

    $routes->get('munaqosah/antrian', 'Munaqosah::antrian');
    $routes->get('munaqosah/monitoring-status-antrian', 'Munaqosah::monitoringStatusAntrian');
    $routes->get('munaqosah/monitoring-antrian-peserta-ruangan-juri', 'Munaqosah::monitoringAntrianPesertaRuanganJuri');
    $routes->get('munaqosah/check-status-antrian-juri', 'Munaqosah::checkStatusAntrianJuri');
    $routes->get('munaqosah/get-next-peserta-from-antrian', 'Munaqosah::getNextPesertaFromAntrian');
    $routes->get('munaqosah/input-registrasi-antrian', 'Munaqosah::inputRegistrasiAntrian');
    $routes->post('munaqosah/register-antrian-ajax', 'Munaqosah::registerAntrianAjax');
    $routes->post('munaqosah/auto-assign-room-ajax/(:num)', 'Munaqosah::autoAssignRoomAjax/$1');
    $routes->post('munaqosah/update-status-antrian-ajax/(:num)', 'Munaqosah::updateStatusAntrianAjax/$1');
    $routes->post('munaqosah/update-status-antrian/(:num)', 'Munaqosah::updateStatusAntrian/$1');
    $routes->get('munaqosah/delete-antrian/(:num)', 'Munaqosah::deleteAntrian/$1');

    $routes->get('munaqosah/bobot', 'Munaqosah::bobotNilai');
    $routes->post('munaqosah/save-bobot', 'Munaqosah::saveBobotNilai');
    $routes->post('munaqosah/update-bobot/(:num)', 'Munaqosah::updateBobotNilai/$1');
    $routes->post('munaqosah/delete-bobot/(:num)', 'Munaqosah::deleteBobotNilai/$1');

    $routes->get('munaqosah/peserta', 'Munaqosah::pesertaMunaqosah');
    $routes->post('munaqosah/save-peserta', 'Munaqosah::savePesertaMunaqosah');
    $routes->post('munaqosah/save-peserta-multiple', 'Munaqosah::savePesertaMunaqosahMultiple');
    $routes->post('munaqosah/konfirmasi-perbaikan-peserta', 'Munaqosah::konfirmasiPerbaikanPeserta');
    $routes->get('munaqosah/check-data-terkait/(:num)', 'Munaqosah::checkDataTerkait/$1');
    $routes->delete('munaqosah/delete-peserta/(:num)', 'Munaqosah::deletePesertaMunaqosah/$1');
    $routes->delete('munaqosah/delete-peserta-by-santri/(:num)', 'Munaqosah::deletePesertaBySantri/$1');

    // Routes untuk registrasi peserta munaqosah
    $routes->get('munaqosah/registrasi-peserta', 'Munaqosah::registrasiPesertaMunaqosah');
    $routes->get('munaqosah/get-santri-for-registrasi', 'Munaqosah::getSantriForRegistrasi');
    $routes->get('munaqosah/get-list-tpq-with-peserta', 'Munaqosah::getListTpqWithPeserta');
    $routes->post('munaqosah/print-kartu-ujian-per-tpq', 'Munaqosah::printKartuUjianPerTpq');
    $routes->post('munaqosah/process-registrasi-peserta', 'Munaqosah::processRegistrasiPeserta');
    $routes->post('munaqosah/print-kartu-ujian', 'Munaqosah::printKartuUjian');

    // Routes untuk edit peserta munaqosah
    $routes->post('munaqosah/get-detail-santri', 'Munaqosah::getDetailSantri');
    $routes->post('munaqosah/update-santri', 'Munaqosah::updateSantri');

    // Routes untuk input nilai juri
    $routes->get('munaqosah/input-nilai-juri', 'Munaqosah::inputNilaiJuri');
    $routes->get('munaqosah/data-nilai-juri', 'Munaqosah::dataNilaiJuri');
    $routes->post('munaqosah/get-data-nilai-juri', 'Munaqosah::getDataNilaiJuri');
    $routes->post('munaqosah/get-detail-nilai', 'Munaqosah::getDetailNilai');
    $routes->post('munaqosah/update-nilai', 'Munaqosah::updateNilai');
    $routes->post('munaqosah/verify-edit-nilai-credentials', 'Munaqosah::verifyEditNilaiCredentials');
    $routes->post('munaqosah/get-peserta-for-edit-nilai', 'Munaqosah::getPesertaForEditNilai');
    $routes->post('munaqosah/update-nilai-with-reason', 'Munaqosah::updateNilaiWithReason');
    $routes->get('munaqosah/get-current-tahun-ajaran', 'Munaqosah::getCurrentTahunAjaran');
    $routes->post('munaqosah/getAyahByMateri', 'Munaqosah::getAyahByMateri');
    $routes->get('munaqosah/getAyahByMateri', 'Munaqosah::getAyahByMateri');
    $routes->post('munaqosah/cek-peserta', 'Munaqosah::cekPeserta');
    $routes->post('munaqosah/simpan-nilai-juri', 'Munaqosah::simpanNilaiJuri');
    $routes->post('munaqosah/verify-admin-credentials', 'Munaqosah::verifyAdminCredentials');

    // Monitoring Munaqosah
    $routes->get('munaqosah/dashboard-munaqosah', 'Munaqosah::dashboardMunaqosah');
    $routes->get('munaqosah/monitoring', 'Munaqosah::monitoringMunaqosah');
    $routes->get('munaqosah/monitoring-data', 'Munaqosah::getMonitoringData');
    $routes->get('munaqosah/dashboard-monitoring', 'Munaqosah::dashboardMonitoring');
    $routes->get('munaqosah/get-statistik-group-peserta', 'Munaqosah::getStatistikGroupPeserta');
    $routes->get('munaqosah/get-statistik-per-group-materi', 'Munaqosah::getStatistikPerGroupMateri');
    $routes->get('munaqosah/get-statistik-penilaian-per-juri', 'Munaqosah::getStatistikPenilaianPerJuri');
    $routes->get('munaqosah/get-statistik-penilaian-per-grup-materi-ruangan', 'Munaqosah::getStatistikPenilaianPerGrupMateriRuangan');
    $routes->get('munaqosah/kelulusan', 'Munaqosah::kelulusanUjian');
    $routes->get('munaqosah/kelulusan-data', 'Munaqosah::getKelulusanData');
    $routes->get('munaqosah/kelulusan-peserta', 'Munaqosah::kelulusanPesertaUjian');
    $routes->get('munaqosah/export-hasil-munaqosah', 'Munaqosah::exportHasilMunaqosah');
    $routes->get('munaqosah/export-hasil-munaqosah-data', 'Munaqosah::getExportHasilMunaqosahData');
    $routes->get('munaqosah/printKelulusanPesertaUjian', 'Munaqosah::printKelulusanPesertaUjian');

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

    // Juri Munaqosah Routes
    $routes->get('munaqosah/juri', 'Munaqosah::listUserJuriMunaqosah');
    $routes->get('munaqosah/get-juri-data', 'Munaqosah::getJuriData');
    $routes->get('munaqosah/get-grup-materi-ujian', 'Munaqosah::getGrupMateriUjian');
    $routes->get('munaqosah/get-tpq-data-juri', 'Munaqosah::getTpqDataForJuri');
    $routes->post('munaqosah/generate-username-juri', 'Munaqosah::generateUsernameJuri');
    $routes->post('munaqosah/save-juri', 'Munaqosah::saveJuri');
    $routes->post('munaqosah/update-room-juri/(:num)', 'Munaqosah::updateRoomJuri/$1');
    $routes->post('munaqosah/updateRoomJuri/(:num)', 'Munaqosah::updateRoomJuri/$1');
    $routes->post('munaqosah/delete-juri/(:num)', 'Munaqosah::deleteJuri/$1');
    $routes->post('munaqosah/update-password-juri/(:num)', 'Munaqosah::updatePasswordJuri/$1');

    // Panitia Munaqosah Routes
    $routes->post('munaqosah/generate-username-panitia', 'Munaqosah::generateUsernamePanitia');
    $routes->post('munaqosah/save-panitia', 'Munaqosah::savePanitia');
    $routes->post('munaqosah/update-room-panitia/(:num)', 'Munaqosah::updateRoomPanitia/$1');
    $routes->post('munaqosah/delete-panitia/(:num)', 'Munaqosah::deletePanitia/$1');
    $routes->post('munaqosah/update-password-panitia/(:num)', 'Munaqosah::updatePasswordPanitia/$1');

    // Kategori Materi Routes
    $routes->get('kategori-materi', 'KategoriMateri::index');
    $routes->get('kategori-materi/get-kategori-materi', 'KategoriMateri::getKategoriMateri');
    $routes->post('kategori-materi/saveKategoriMateri', 'KategoriMateri::saveKategoriMateri');
    $routes->post('kategori-materi/updateKategoriMateri/(:num)', 'KategoriMateri::updateKategoriMateri/$1');
    $routes->delete('kategori-materi/deleteKategoriMateri/(:num)', 'KategoriMateri::deleteKategoriMateri/$1');
    $routes->get('kategori-materi/get-kategori-materi-dropdown', 'KategoriMateri::getKategoriMateriForDropdown');

    // Kelas Materi Pelajaran Routes
    $routes->get('kelasMateriPelajaran/getStatistik', 'KelasMateriPelajaran::getStatistik');
    $routes->post('kelasMateriPelajaran/checkUrutanMateri', 'KelasMateriPelajaran::checkUrutanMateri');

    // Kategori Kesalahan Routes
    $routes->get('munaqosah/list-kategori-kesalahan', 'Munaqosah::listKategoriKesalahan');
    $routes->get('munaqosah/get-kategori-kesalahan', 'Munaqosah::getKategoriKesalahan');
    $routes->get('munaqosah/get-kategori-materi-dropdown', 'KategoriMateri::getKategoriMateriForDropdown');
    $routes->post('munaqosah/save-kategori-kesalahan', 'Munaqosah::saveKategoriKesalahan');
    $routes->post('munaqosah/update-kategori-kesalahan/(:num)', 'Munaqosah::updateKategoriKesalahan/$1');
    $routes->delete('munaqosah/delete-kategori-kesalahan/(:num)', 'Munaqosah::deleteKategoriKesalahan/$1');
    $routes->get('munaqosah/get-error-categories-by-kategori', 'Munaqosah::getErrorCategoriesByKategori');

    // Konfigurasi Munaqosah Routes
    $routes->get('munaqosah/list-konfigurasi-munaqosah', 'Munaqosah::listKonfigurasiMunaqosah');
    $routes->post('munaqosah/save-konfigurasi', 'Munaqosah::saveKonfigurasi');
    $routes->post('munaqosah/update-konfigurasi/(:num)', 'Munaqosah::updateKonfigurasi/$1');
    $routes->post('munaqosah/duplicate-konfigurasi', 'Munaqosah::duplicateKonfigurasi');
    $routes->post('munaqosah/delete-konfigurasi/(:num)', 'Munaqosah::deleteKonfigurasi/$1');
    $routes->post('munaqosah/toggle-aktive-tombol-kelulusan', 'Munaqosah::toggleAktiveTombolKelulusan');

    // Tools Setting Routes
    $routes->post('tools/save-tools', 'Tools::saveTools');
    $routes->post('tools/update-tools/(:num)', 'Tools::updateTools/$1');
    $routes->post('tools/duplicate-tools', 'Tools::duplicateTools');
    $routes->post('tools/delete-tools/(:num)', 'Tools::deleteTools/$1');

    // Jadwal Peserta Ujian Routes
    $routes->get('munaqosah/jadwal-peserta-ujian', 'Munaqosah::jadwalPesertaUjian');
    $routes->get('munaqosah/get-jadwal-peserta-ujian', 'Munaqosah::getJadwalPesertaUjian');
    $routes->post('munaqosah/save-jadwal-peserta-ujian', 'Munaqosah::saveJadwalPesertaUjian');
    $routes->post('munaqosah/update-jadwal-peserta-ujian/(:num)', 'Munaqosah::updateJadwalPesertaUjian/$1');
    $routes->get('munaqosah/delete-jadwal-peserta-ujian/(:num)', 'Munaqosah::deleteJadwalPesertaUjian/$1');
    $routes->get('munaqosah/get-tpq-from-peserta', 'Munaqosah::getTpqFromPeserta');
    $routes->get('munaqosah/print-jadwal-peserta', 'Munaqosah::printJadwalPeserta');
    $routes->get('munaqosah/printInstruksiVerifikasi/(:num)', 'Munaqosah::printInstruksiVerifikasi/$1');
    $routes->get('munaqosah/printInstruksiVerifikasiAll', 'Munaqosah::printInstruksiVerifikasiAll');

    // API Routes untuk data master
    $routes->get('backend/tpq/get-all', 'Tpq::getAll');

    // Reset Nilai Routes
    $routes->get('nilai/resetNilaiIndex', 'ResetNilaiIndex::index');
    $routes->get('nilai/resetNilai', 'ResetNilai::index');
    $routes->get('resetNilai', 'ResetNilai::index');
    $routes->post('resetNilai/getCount', 'ResetNilai::getCount');
    $routes->post('resetNilai/reset', 'ResetNilai::reset');

    // Reset Nilai Munaqosah Routes
    $routes->get('nilai/resetNilaiMunaqosah', 'ResetNilaiMunaqosah::index');
    $routes->post('nilai/resetNilaiMunaqosah/getCount', 'ResetNilaiMunaqosah::getCount');
    $routes->post('nilai/resetNilaiMunaqosah/delete', 'ResetNilaiMunaqosah::reset');

    // Reset Nilai Sertifikasi Routes
    $routes->get('nilai/resetNilaiSertifikasi', 'ResetNilaiSertifikasi::index');
    $routes->post('nilai/resetNilaiSertifikasi/getCount', 'ResetNilaiSertifikasi::getCount');
    $routes->post('nilai/resetNilaiSertifikasi/delete', 'ResetNilaiSertifikasi::delete');

    // Sertifikasi Routes
    $routes->get('sertifikasi/dashboard', 'Sertifikasi::dashboard');
    $routes->get('sertifikasi/dashboard-admin', 'Sertifikasi::dashboardAdmin');
    $routes->get('sertifikasi/dashboardPanitiaSertifikasi', 'Sertifikasi::dashboardPanitiaSertifikasi');
    $routes->get('sertifikasi/inputNilaiSertifikasi', 'Sertifikasi::inputNilaiSertifikasi');
    $routes->get('sertifikasi/nilaiPesertaSertifikasi', 'Sertifikasi::nilaiPesertaSertifikasi');
    $routes->get('sertifikasi/listPesertaSertifikasi', 'Sertifikasi::listPesertaSertifikasi');
    $routes->get('sertifikasi/getNextNoPeserta', 'Sertifikasi::getNextNoPeserta');
    $routes->post('sertifikasi/storePesertaSertifikasi', 'Sertifikasi::storePesertaSertifikasi');
    $routes->get('sertifikasi/listNilaiSertifikasi', 'Sertifikasi::listNilaiSertifikasi');
    $routes->get('sertifikasi/listJuriSertifikasi', 'Sertifikasi::listJuriSertifikasi');
    $routes->get('sertifikasi/createJuriSertifikasi', 'Sertifikasi::createJuriSertifikasi');
    $routes->get('sertifikasi/editJuriSertifikasi/(:num)', 'Sertifikasi::editJuriSertifikasi/$1');
    $routes->post('sertifikasi/cekPeserta', 'Sertifikasi::cekPeserta');
    $routes->post('sertifikasi/simpanNilai', 'Sertifikasi::simpanNilai');
    $routes->post('sertifikasi/updateNilai', 'Sertifikasi::updateNilai');
    $routes->post('sertifikasi/restoreNilai', 'Sertifikasi::restoreNilai');
    $routes->post('sertifikasi/getCatatan', 'Sertifikasi::getCatatan');
    $routes->get('sertifikasi/getCatatan', 'Sertifikasi::getCatatan');
    $routes->post('sertifikasi/generateNextUsernameJuri', 'Sertifikasi::generateNextUsernameJuri');
    $routes->post('sertifikasi/storeJuriSertifikasi', 'Sertifikasi::storeJuriSertifikasi');
    $routes->post('sertifikasi/updateJuriSertifikasi/(:num)', 'Sertifikasi::updateJuriSertifikasi/$1');
    $routes->post('sertifikasi/deleteJuriSertifikasi/(:num)', 'Sertifikasi::deleteJuriSertifikasi/$1');

    // Profile Routes
    $routes->get('pages/profil', 'Pages::profil');
    $routes->post('pages/updateProfil', 'Pages::updateProfil');
    $routes->post('pages/resetPassword', 'Pages::resetPassword');
    $routes->post('pages/uploadPhotoProfil', 'Pages::uploadPhotoProfil');

    // Search Routes
    $routes->get('search', 'Search::index');
    $routes->get('search/index', 'Search::index');

    // Islamic API Routes (Jadwal Sholat & Al-Qur'an)
    $routes->get('jadwal-sholat', 'IslamicController::jadwalSholatByCity');
    $routes->get('jadwal-sholat/(:segment)', 'IslamicController::jadwalSholatByCity/$1');
    $routes->get('jadwal-sholat/(:segment)/(:segment)', 'IslamicController::jadwalSholatByCoordinate/$1/$2');
    $routes->get('surah', 'IslamicController::surah');
    $routes->get('surah/(:num)', 'IslamicController::surah/$1');
    $routes->get('surah/(:num)/(:num)/(:num)', 'IslamicController::ayah/$1/$2/$3'); // Range ayat: surah/start/end
    $routes->get('surah/(:num)/(:num)', 'IslamicController::ayah/$1/$2'); // Single ayat
    $routes->get('ayah', 'IslamicController::ayah');
    $routes->get('quran/search', 'IslamicController::searchQuran');
});

$routes->get('signature/validateSignature/(:segment)', 'Frontend\\Signature::validateSignature/$1');
$routes->get('signature/santri/(:num)', 'Frontend\\Signature::getSignaturesBySantri/$1');
$routes->get('signature/guru/(:num)', 'Frontend\\Signature::getSignaturesByGuru/$1');
$routes->get('signature/tpq/(:num)', 'Frontend\\Signature::getSignaturesByTpq/$1');

$routes->get('logout', 'Dashboard::logout');

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
