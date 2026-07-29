<?php

namespace App\Models\Frontend\UjianMdta;

use CodeIgniter\Model;

class UjianMdtaPilihanModel extends Model
{
    protected $table      = 'tbl_ujian_mdta_pilihan';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'IdSoal',
        'HurufPilihan',
        'TeksPilihan',
        'IsBenar',
        'Urutan',
    ];

    /**
     * Ambil semua pilihan jawaban untuk sebuah soal, urut by Urutan.
     *
     * @param int $idSoal
     * @return array
     */
    public function getPilihanBySoal(int $idSoal): array
    {
        return $this->where('IdSoal', $idSoal)
                    ->orderBy('Urutan', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil pilihan yang benar untuk sebuah soal.
     *
     * @param int $idSoal
     * @return array|null
     */
    public function getJawabanBenar(int $idSoal): ?array
    {
        return $this->where('IdSoal', $idSoal)
                    ->where('IsBenar', 1)
                    ->first();
    }

    /**
     * Simpan seluruh pilihan jawaban untuk sebuah soal.
     * Hapus pilihan lama terlebih dahulu, lalu insert semua pilihan baru.
     *
     * Format $pilihanArray:
     * [
     *   ['HurufPilihan' => 'A', 'TeksPilihan' => '<p>...</p>', 'IsBenar' => 0],
     *   ['HurufPilihan' => 'B', 'TeksPilihan' => '<p>...</p>', 'IsBenar' => 1],
     *   ...
     * ]
     *
     * @param int   $idSoal
     * @param array $pilihanArray
     * @return bool
     */
    public function saveAllPilihan(int $idSoal, array $pilihanArray): bool
    {
        // Hapus semua pilihan lama milik soal ini
        $this->where('IdSoal', $idSoal)->delete();

        if (empty($pilihanArray)) {
            return true;
        }

        // Siapkan data untuk batch insert
        $data = [];
        foreach ($pilihanArray as $index => $pilihan) {
            $data[] = [
                'IdSoal'       => $idSoal,
                'HurufPilihan' => $pilihan['HurufPilihan'] ?? chr(65 + $index), // A, B, C, D
                'TeksPilihan'  => $pilihan['TeksPilihan'] ?? '',
                'IsBenar'      => (int)($pilihan['IsBenar'] ?? 0),
                'Urutan'       => $index + 1,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];
        }

        return $this->insertBatch($data) !== false;
    }

    /**
     * Ambil pilihan jawaban untuk banyak soal sekaligus (batch query).
     * Mengembalikan array dengan IdSoal sebagai key.
     *
     * @param array $idSoalList
     * @return array ['idSoal' => [pilihan...], ...]
     */
    public function getPilihanBySoalBatch(array $idSoalList): array
    {
        if (empty($idSoalList)) {
            return [];
        }

        $rows = $this->whereIn('IdSoal', $idSoalList)
                     ->orderBy('IdSoal', 'ASC')
                     ->orderBy('Urutan', 'ASC')
                     ->findAll();

        // Group by IdSoal
        $result = [];
        foreach ($rows as $row) {
            $result[$row['IdSoal']][] = $row;
        }
        return $result;
    }
}
