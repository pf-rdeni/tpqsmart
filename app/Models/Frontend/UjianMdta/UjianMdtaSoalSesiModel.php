<?php

namespace App\Models\Frontend\UjianMdta;

use CodeIgniter\Model;

class UjianMdtaSoalSesiModel extends Model
{
    protected $table      = 'tbl_ujian_mdta_soal_sesi';
    protected $primaryKey = 'id';
    protected $useTimestamps = false; // hanya ada created_at

    protected $allowedFields = [
        'IdSesi',
        'IdSoal',
        'UrutanSoal',
        'UrutanPilihan',
        'created_at',
    ];

    /**
     * Generate dan simpan distribusi soal acak untuk sebuah sesi santri.
     * 
     * Proses:
     * 1. Ambil JumlahSoal soal aktif secara acak dari paket (via jadwal)
     * 2. Acak urutan soal (jika AcakSoal = 1)
     * 3. Untuk setiap soal, acak urutan pilihan jawaban (jika AcakJawaban = 1)
     * 4. Simpan hasilnya ke tbl_ujian_mdta_soal_sesi
     * 5. Inisialisasi tabel jawaban (kosong) untuk semua soal
     *
     * @param int $idSesi
     * @param int $idJadwal
     * @return bool
     */
    public function generateDistribusi(int $idSesi, int $idJadwal): bool
    {
        $jadwalModel  = new UjianMdtaJadwalModel();
        $soalModel    = new UjianMdtaSoalModel();
        $pilihanModel = new UjianMdtaPilihanModel();
        $jawabanModel = new UjianMdtaJawabanModel();

        $jadwal = $jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return false;
        }

        // Ambil soal acak dari paket sejumlah JumlahSoal
        $daftarSoal = $soalModel->getSoalAcak((int)$jadwal['IdPaket'], (int)$jadwal['JumlahSoal']);
        if (empty($daftarSoal)) {
            return false;
        }

        // Acak urutan soal jika diaktifkan
        if ($jadwal['AcakSoal']) {
            shuffle($daftarSoal);
        }

        $dataSoalSesi  = [];
        $dataJawaban   = [];
        $now           = date('Y-m-d H:i:s');

        foreach ($daftarSoal as $urutan => $soal) {
            $urutanPilihanJson = null;

            // Acak urutan pilihan jawaban jika diaktifkan
            if (!empty($jadwal['AcakJawaban'])) {
                $pilihan    = $pilihanModel->getPilihanBySoal((int)$soal['id']);
                $maxPilihan = (int)($jadwal['JumlahPilihan'] ?? 4);

                if ($maxPilihan > 0 && count($pilihan) > $maxPilihan) {
                    $correctHuruf = [];
                    $wrongHuruf   = [];
                    foreach ($pilihan as $p) {
                        if (!empty($p['IsBenar']) && (int)$p['IsBenar'] === 1) {
                            $correctHuruf[] = $p['HurufPilihan'];
                        } else {
                            $wrongHuruf[]   = $p['HurufPilihan'];
                        }
                    }
                    shuffle($wrongHuruf);
                    $neededWrong   = max(1, $maxPilihan - count($correctHuruf));
                    $selectedWrong = array_slice($wrongHuruf, 0, $neededWrong);
                    $hurufList     = array_merge($correctHuruf, $selectedWrong);
                    shuffle($hurufList);
                } else {
                    $hurufList = array_column($pilihan, 'HurufPilihan');
                    shuffle($hurufList);
                }

                $urutanPilihanJson = json_encode($hurufList);
            }

            $dataSoalSesi[] = [
                'IdSesi'        => $idSesi,
                'IdSoal'        => $soal['id'],
                'UrutanSoal'    => $urutan + 1,
                'UrutanPilihan' => $urutanPilihanJson,
                'created_at'    => $now,
            ];

            // Inisialisasi baris jawaban kosong (belum dijawab)
            $dataJawaban[] = [
                'IdSesi'      => $idSesi,
                'IdSoal'      => $soal['id'],
                'IdPilihan'   => null,
                'IsBenar'     => null,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        // Batch insert distribusi soal
        if (!empty($dataSoalSesi)) {
            $this->db->table($this->table)->insertBatch($dataSoalSesi);
        }

        // Batch insert jawaban kosong (untuk tracking soal yang belum dijawab)
        if (!empty($dataJawaban)) {
            $this->db->table('tbl_ujian_mdta_jawaban')->insertBatch($dataJawaban);
        }

        return true;
    }

    /**
     * Ambil distribusi soal untuk sebuah sesi, urut UrutanSoal.
     * Include data soal dan pilihan jawaban sesuai urutan acak santri.
     *
     * @param int $idSesi
     * @return array
     */
    public function getDistribusiBySesi(int $idSesi): array
    {
        $builder = $this->db->table($this->table . ' ss');
        $builder->select('ss.id, ss.UrutanSoal, ss.UrutanPilihan, ss.IdSoal,
                          soal.UraianSoal, soal.NomorSoal, soal.TingkatKesulitan,
                          soal.AudioSoal, soal.TimeLimitSoal, soal.JenisSoal');
        $builder->join('tbl_ujian_mdta_soal soal', 'soal.id = ss.IdSoal', 'left');
        $builder->where('ss.IdSesi', $idSesi);
        $builder->orderBy('ss.UrutanSoal', 'ASC');
        $rows = $builder->get()->getResultArray();

        // Ambil semua pilihan untuk soal-soal ini sekaligus (batch query)
        $idSoalList   = array_column($rows, 'IdSoal');
        $pilihanModel = new UjianMdtaPilihanModel();
        $semuaPilihan = $pilihanModel->getPilihanBySoalBatch($idSoalList);

        // Attach pilihan ke tiap soal, urutkan sesuai UrutanPilihan acak santri
        foreach ($rows as &$row) {
            $pilihanSoal = $semuaPilihan[$row['IdSoal']] ?? [];

            if ($row['UrutanPilihan']) {
                // Susun ulang pilihan sesuai urutan acak santri ini
                $urutanHuruf = json_decode($row['UrutanPilihan'], true);
                $pilihanMap  = [];
                foreach ($pilihanSoal as $p) {
                    $pilihanMap[$p['HurufPilihan']] = $p;
                }
                $pilihanSorted = [];
                foreach ($urutanHuruf as $huruf) {
                    if (isset($pilihanMap[$huruf])) {
                        $pilihanSorted[] = $pilihanMap[$huruf];
                    }
                }
                $row['pilihan'] = $pilihanSorted;
            } else {
                $row['pilihan'] = $pilihanSoal;
            }
        }

        return $rows;
    }

    /**
     * Cek apakah distribusi soal sudah di-generate untuk sesi ini.
     *
     * @param int $idSesi
     * @return bool
     */
    public function sudahDigenerate(int $idSesi): bool
    {
        return $this->where('IdSesi', $idSesi)->countAllResults() > 0;
    }
}
