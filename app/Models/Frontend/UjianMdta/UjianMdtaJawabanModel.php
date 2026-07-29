<?php

namespace App\Models\Frontend\UjianMdta;

use CodeIgniter\Model;

class UjianMdtaJawabanModel extends Model
{
    protected $table      = 'tbl_ujian_mdta_jawaban';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'IdSesi',
        'IdSoal',
        'IdPilihan',
        'JawabanEsai',
        'IsBenar',
        'NilaiEsai',
        'IsRagu',
    ];

    /**
     * Simpan atau update jawaban santri untuk sebuah soal dalam sesi (PG maupun Esai/Uraian).
     *
     * @param int         $idSesi
     * @param int         $idSoal
     * @param int|null    $idPilihan
     * @param string|null $jawabanEsai
     * @return bool
     */
    public function simpanJawaban(int $idSesi, int $idSoal, ?int $idPilihan = null, ?string $jawabanEsai = null): bool
    {
        $existing = $this->where('IdSesi', $idSesi)
                         ->where('IdSoal', $idSoal)
                         ->first();

        $dataUpdate = [];
        if ($idPilihan !== null) {
            $dataUpdate['IdPilihan'] = $idPilihan;
        }
        if ($jawabanEsai !== null) {
            $dataUpdate['JawabanEsai'] = $jawabanEsai;
        }

        if ($existing) {
            if (!empty($dataUpdate)) {
                return $this->update($existing['id'], $dataUpdate) !== false;
            }
            return true;
        }

        return $this->insert([
            'IdSesi'      => $idSesi,
            'IdSoal'      => $idSoal,
            'IdPilihan'   => $idPilihan,
            'JawabanEsai' => $jawabanEsai,
            'IsBenar'     => null,
        ]) !== false;
    }

    /**
     * Ambil semua jawaban untuk sebuah sesi.
     *
     * @param int $idSesi
     * @return array
     */
    public function getJawabanBySesi(int $idSesi): array
    {
        return $this->where('IdSesi', $idSesi)->findAll();
    }

    /**
     * Hitung dan update field IsBenar untuk semua jawaban dalam sebuah sesi.
     * Dipanggil saat santri submit atau timeout.
     *
     * @param int $idSesi
     * @return int Jumlah jawaban yang benar
     */
    public function evaluasiJawaban(int $idSesi): int
    {
        $daftarJawaban = $this->getJawabanBySesi($idSesi);
        $pilihanModel  = new UjianMdtaPilihanModel();
        $jumlahBenar   = 0;

        foreach ($daftarJawaban as $jawaban) {
            if ($jawaban['IdPilihan'] === null) {
                // Tidak dijawab → IsBenar = 0
                $this->update($jawaban['id'], ['IsBenar' => 0]);
                continue;
            }

            $pilihan = $pilihanModel->find($jawaban['IdPilihan']);
            $benar   = ($pilihan && $pilihan['IsBenar'] == 1) ? 1 : 0;

            $this->update($jawaban['id'], ['IsBenar' => $benar]);

            if ($benar) {
                $jumlahBenar++;
            }
        }

        return $jumlahBenar;
    }

    /**
     * Hitung jumlah jawaban benar dalam sebuah sesi (setelah evaluasiJawaban dipanggil).
     *
     * @param int $idSesi
     * @return int
     */
    public function countBenarBySesi(int $idSesi): int
    {
        return $this->where('IdSesi', $idSesi)
                    ->where('IsBenar', 1)
                    ->countAllResults();
    }

    /**
     * Ambil jawaban santri beserta info soal dan pilihan (untuk halaman hasil ujian).
     *
     * @param int $idSesi
     * @return array
     */
    public function getDetailHasilBySesi(int $idSesi): array
    {
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.IdSoal, j.IdPilihan, j.JawabanEsai, j.NilaiEsai, j.IsBenar, 
                          soal.UraianSoal, soal.NomorSoal, soal.Pembahasan, soal.JenisSoal,
                          pilihan.HurufPilihan as JawabanDipilih,
                          (SELECT p2.HurufPilihan FROM tbl_ujian_mdta_pilihan p2 
                           WHERE p2.IdSoal = j.IdSoal AND p2.IsBenar = 1 LIMIT 1) as JawabanBenar');
        $builder->join('tbl_ujian_mdta_soal soal', 'soal.id = j.IdSoal', 'left');
        $builder->join('tbl_ujian_mdta_pilihan pilihan', 'pilihan.id = j.IdPilihan', 'left');
        $builder->where('j.IdSesi', $idSesi);
        $builder->orderBy('soal.NomorSoal', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil peta IdSoal → Nilai Jawaban (idPilihan atau teks esai) untuk tracking indikator tombol di UI CBT.
     *
     * @param int $idSesi
     * @return array ['idSoal' => 'idPilihan'|'textEsai'|null, ...]
     */
    public function getJawabanMap(int $idSesi): array
    {
        $rows = $this->select('IdSoal, IdPilihan, JawabanEsai')
                     ->where('IdSesi', $idSesi)
                     ->findAll();

        $map = [];
        foreach ($rows as $row) {
            if (!empty($row['IdPilihan'])) {
                $map[$row['IdSoal']] = $row['IdPilihan'];
            } else if (!empty(trim($row['JawabanEsai'] ?? ''))) {
                $map[$row['IdSoal']] = $row['JawabanEsai'];
            }
        }
        return $map;
    }

    /**
     * Ambil peta khusus IdSoal → JawabanEsai (Teks) untuk pre-fill textarea esai di UI CBT.
     */
    public function getJawabanEsaiMap(int $idSesi): array
    {
        $rows = $this->select('IdSoal, JawabanEsai')
                     ->where('IdSesi', $idSesi)
                     ->where('JawabanEsai IS NOT NULL')
                     ->findAll();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['IdSoal']] = $row['JawabanEsai'];
        }
        return $map;
    }

    /**
     * Simpan status Ragu-Ragu untuk soal tertentu.
     */
    public function simpanRagu(int $idSesi, int $idSoal, int $isRagu): bool
    {
        $existing = $this->where('IdSesi', $idSesi)
                         ->where('IdSoal', $idSoal)
                         ->first();

        if ($existing) {
            return $this->update($existing['id'], ['IsRagu' => $isRagu]) !== false;
        }

        return $this->insert([
            'IdSesi'    => $idSesi,
            'IdSoal'    => $idSoal,
            'IdPilihan' => null,
            'IsBenar'   => null,
            'IsRagu'    => $isRagu,
        ]) !== false;
    }

    /**
     * Ambil peta IdSoal → IsRagu (1 atau 0) untuk sebuah sesi.
     */
    public function getRaguMap(int $idSesi): array
    {
        $rows = $this->select('IdSoal, IsRagu')
                     ->where('IdSesi', $idSesi)
                     ->findAll();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['IdSoal']] = (int)($row['IsRagu'] ?? 0);
        }
        return $map;
    }
}
