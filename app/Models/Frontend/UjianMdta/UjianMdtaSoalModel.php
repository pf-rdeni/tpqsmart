<?php

namespace App\Models\Frontend\UjianMdta;

use CodeIgniter\Model;

class UjianMdtaSoalModel extends Model
{
    protected $table      = 'tbl_ujian_mdta_soal';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'IdPaket',
        'NomorSoal',
        'JenisSoal',
        'UraianSoal',
        'TingkatKesulitan',
        'AudioSoal',
        'AcakSoal',
        'TimeLimitSoal',
        'Pembahasan',
        'IndikatorCapaian',
        'LampiranFile',
        'Status',
    ];

    /**
     * Ambil semua soal aktif dalam sebuah paket, urut NomorSoal.
     *
     * @param int $idPaket
     * @return array
     */
    public function getSoalByPaket(int $idPaket): array
    {
        return $this->where('IdPaket', $idPaket)
                    ->where('Status', 'aktif')
                    ->orderBy('NomorSoal', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil soal dengan pilihan jawabannya sekaligus.
     *
     * @param int $id
     * @return array|null
     */
    public function getSoalWithPilihan(int $id): ?array
    {
        $soal = $this->find($id);
        if (!$soal) {
            return null;
        }
        $pilihanModel  = new UjianMdtaPilihanModel();
        $soal['pilihan'] = $pilihanModel->getPilihanBySoal($id);
        return $soal;
    }

    /**
     * Hitung jumlah soal aktif dalam sebuah paket.
     *
     * @param int $idPaket
     * @return int
     */
    public function countByPaket(int $idPaket): int
    {
        return $this->where('IdPaket', $idPaket)
                    ->where('Status', 'aktif')
                    ->countAllResults();
    }

    /**
     * Ambil soal secara acak dari paket sejumlah $jumlah soal.
     * Hanya soal yang aktif.
     *
     * @param int $idPaket
     * @param int $jumlah
     * @return array
     */
    public function getSoalAcak(int $idPaket, int $jumlah): array
    {
        return $this->where('IdPaket', $idPaket)
                    ->where('Status', 'aktif')
                    ->orderBy('RAND()')
                    ->limit($jumlah)
                    ->findAll();
    }

    /**
     * Re-order NomorSoal setelah soal dihapus atau diurutkan ulang.
     * Assign ulang nomor 1, 2, 3, ... berurutan.
     *
     * @param int $idPaket
     * @return void
     */
    public function reorderNomor(int $idPaket): void
    {
        $soalList = $this->where('IdPaket', $idPaket)
                         ->where('Status', 'aktif')
                         ->orderBy('NomorSoal', 'ASC')
                         ->findAll();

        foreach ($soalList as $index => $soal) {
            $this->update($soal['id'], ['NomorSoal' => $index + 1]);
        }
    }

    /**
     * Ambil nomor soal berikutnya dalam paket (untuk auto-increment NomorSoal).
     *
     * @param int $idPaket
     * @return int
     */
    public function getNextNomor(int $idPaket): int
    {
        $last = $this->where('IdPaket', $idPaket)
                     ->orderBy('NomorSoal', 'DESC')
                     ->first();
        return $last ? ($last['NomorSoal'] + 1) : 1;
    }
}
