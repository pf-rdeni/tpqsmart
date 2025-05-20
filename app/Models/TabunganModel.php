<?php

namespace App\Models;

use CodeIgniter\Model;

class TabunganModel extends Model
{
    protected $table = 'tbl_tabungan_santri';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'IdSantri',
        'IdKelas',
        'IdTpq',
        'IdTahunAjaran',
        'IdGuru',
        'TanggalTransaksi',
        'JenisTransaksi',
        'Nominal',
        'Keterangan',
        'CreatedAt',
        'UpdatedAt'
    ];

    // If you want to automatically handle createdAt and updatedAt fields
    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    // Method to retrieve all transactions for a specific Santri
    public function getTabunganBySantri($idSantri)
    {
        return $this->where('IdSantri', $idSantri)->findAll();
    }

    // Method to calculate the balance of a specific Santri
    public function calculateBalance($idSantri)
    {
        $setoran = $this->where('IdSantri', $idSantri)
                         ->where('JenisTransaksi', 'Setoran')
                         ->selectSum('Nominal')
                         ->first();

        $penarikan = $this->where('IdSantri', $idSantri)
                            ->where('JenisTransaksi', 'Penarikan')
                            ->selectSum('Nominal')
                            ->first();

        $totalDeposit = $setoran['Nominal'] ?? 0;
        $totalWithdrawal = $penarikan['Nominal'] ?? 0;

        return $totalDeposit - $totalWithdrawal;
    }

    // Method to retrive list all dataSantri with their balance in tabungan table join with tbl_kelas_santri and tbl_kelas and tbl_guru_kelas filter by IdTahunAjaran and IdGuru IdKelas and IdTpq and IdSantri = null
    public function getSantriWithBalance($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru)
    {

        $santriModel = new SantriModel();
        $santriList = $santriModel->GetDataSantriPerKelas($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);

        $santriWithBalance = [];
        foreach ($santriList as $santri) {
            $balance = $this->calculateBalance($santri->IdSantri);
            $santri->Balance = $balance;
            $santriWithBalance[] = $santri;
        }

        return $santriWithBalance;
    }

    // fungsi untuk mengambil saldo individual santri atau semua santri ketika sntri != null Total saldo adalah total kategori setoran - total kategori penarikan
    public function getSaldoTabunganSantri($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru)
    {
        $santriModel = new SantriModel();
        $santriList = $santriModel->GetDataSantriPerKelas($IdTpq, $IdTahunAjaran, $IdKelas, $IdGuru);

        $SaldoTabunagan = 0;
        foreach ($santriList as $santri) {
            $SaldoTabunagan += $this->calculateBalance($santri->IdSantri);
        }
        return $SaldoTabunagan;
    }
}
