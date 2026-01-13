<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;

class Dokumentasi extends BaseController
{
    public function alurAbsensiGuru()
    {
        $data = [
            'page_title' => 'Alur Absensi Guru',
            'menu_open' => 'dokumentasi',
            'menu_active' => 'alur-absensi-guru'
        ];
        return view('backend/dokumentasi/absensiGuru/AlurAbsensiGuru', $data);
    }

    public function prosesMembuatAbsensi()
    {
        $data = [
            'page_title' => 'Dokumentasi: Proses Membuat Absensi',
        ];
        return view('backend/dokumentasi/absensiGuru/ProsesMembuatAbsensi', $data);
    }

    public function perlombaan()
    {
        $data = [
            'page_title' => 'Dokumentasi: Modul Perlombaan',
        ];
        return view('backend/dokumentasi/perlombaan/index', $data);
    }

    public function perlombaanSetup()
    {
        $data = [
            'page_title' => 'Dokumentasi: Setup Perlombaan',
        ];
        return view('backend/dokumentasi/perlombaan/setup', $data);
    }

    public function perlombaanPelaksanaan()
    {
        $data = [
            'page_title' => 'Dokumentasi: Pelaksanaan Perlombaan',
        ];
        return view('backend/dokumentasi/perlombaan/pelaksanaan', $data);
    }

    public function perlombaanJuri()
    {
        $data = [
            'page_title' => 'Dokumentasi: Panduan Juri',
        ];
        return view('backend/dokumentasi/perlombaan/juri', $data);
    }

    public function perlombaanSertifikat()
    {
        $data = [
            'page_title' => 'Dokumentasi: Pembuatan Sertifikat',
        ];
        return view('backend/dokumentasi/perlombaan/sertifikat', $data);
    }

    // Munaqosah Documentation
    public function munaqosah()
    {
        $data = [
            'page_title' => 'Dokumentasi: Modul Munaqosah',
        ];
        return view('backend/dokumentasi/munaqosah/index', $data);
    }

    public function munaqosahSetup()
    {
        $data = [
            'page_title' => 'Dokumentasi: Setup Munaqosah',
        ];
        return view('backend/dokumentasi/munaqosah/setup', $data);
    }

    public function munaqosahRegistrasi()
    {
        $data = [
            'page_title' => 'Dokumentasi: Registrasi & Antrian',
        ];
        return view('backend/dokumentasi/munaqosah/registrasi', $data);
    }

    public function munaqosahPenilaian()
    {
        $data = [
            'page_title' => 'Dokumentasi: Penilaian Munaqosah',
        ];
        return view('backend/dokumentasi/munaqosah/penilaian', $data);
    }

    public function munaqosahKelulusan()
    {
        $data = [
            'page_title' => 'Dokumentasi: Cek Kelulusan Publik',
        ];
        return view('backend/dokumentasi/munaqosah/kelulusan_public', $data);
    }

    // Absensi Santri Documentation
    public function absensiSantriPublic()
    {
        $data = [
            'page_title' => 'Dokumentasi: Absensi Santri Public',
            'menu_open' => 'dokumentasi',
            'menu_active' => 'absensi-santri-public'
        ];
        return view('backend/dokumentasi/absensiSantri/AbsensiSantriPublic', $data);
    }

    // Santri Verifikasi Documentation
    public function santriVerifikasi()
    {
        $data = [
            'page_title' => 'Dokumentasi: Verifikasi Data Santri',
            'menu_open' => 'dokumentasi',
            'menu_active' => 'santri-verifikasi'
        ];
        return view('backend/dokumentasi/santri/verifikasi', $data);
    }
}
