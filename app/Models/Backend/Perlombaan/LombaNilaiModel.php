<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaNilaiModel extends Model
{
    protected $table = 'tbl_lomba_nilai';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'registrasi_id',
        'cabang_id',
        'kriteria_id',
        'IdJuri',
        'Nilai',
        'Catatan'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'registrasi_id' => 'required|integer',
        'cabang_id'     => 'required|integer',
        'kriteria_id'   => 'required|integer',
        'IdJuri'        => 'required',
        'Nilai'         => 'required|decimal',
    ];

    /**
     * Ambil nilai berdasarkan registrasi
     */
    public function getNilaiByRegistrasi($registrasiId)
    {
        $builder = $this->db->table($this->table . ' n');
        $builder->select('n.*, k.NamaKriteria, k.Bobot, j.NamaJuri, j.UsernameJuri');
        $builder->join('tbl_lomba_kriteria k', 'k.id = n.kriteria_id', 'left');
        $builder->join('tbl_lomba_juri j', 'j.IdJuri = n.IdJuri', 'left');
        $builder->where('n.registrasi_id', $registrasiId);
        $builder->orderBy('k.Urutan', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil nilai yang diinput oleh juri tertentu
     */
    public function getNilaiByJuri($idJuri, $cabangId = null)
    {
        $builder = $this->db->table($this->table . ' n');
        $builder->select('n.*, k.NamaKriteria, k.Bobot, r.NoPeserta, r.TipePeserta, r.NamaKelompok');
        // Join untuk mendapatkan NamaSantri, handle Kelompok vs Individu
        $builder->select("CASE WHEN r.TipePeserta = 'Kelompok' THEN r.NamaKelompok ELSE s.NamaSantri END as NamaSantriReal", false);
        $builder->join('tbl_lomba_kriteria k', 'k.id = n.kriteria_id', 'left');
        $builder->join('tbl_lomba_registrasi r', 'r.id = n.registrasi_id', 'left');
        $builder->join('tbl_lomba_registrasi_anggota ra', 'ra.registrasi_id = r.id', 'left');
        $builder->join('tbl_lomba_peserta p', 'p.id = ra.peserta_id', 'left');
        $builder->join('tbl_santri_baru s', 's.IdSantri = p.IdSantri', 'left');
        $builder->where('n.IdJuri', $idJuri);
        $builder->groupBy('n.id'); // Cegah duplikasi row jika ada banyak anggota kelompok
        
        if ($cabangId !== null) {
            $builder->where('n.cabang_id', $cabangId);
        }
        
        $builder->orderBy('n.updated_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Cek apakah juri sudah menilai peserta ini untuk semua kriteria (legacy - pakai peserta_id)
     */
    public function checkJuriAlreadyScored($pesertaId, $idJuri, $cabangId)
    {
        $kriteriaModel = new LombaKriteriaModel();
        $totalKriteria = count($kriteriaModel->getKriteriaByCabang($cabangId));
        
        $scoredCount = $this->where('peserta_id', $pesertaId)
                            ->where('IdJuri', $idJuri)
                            ->where('cabang_id', $cabangId)
                            ->countAllResults();
        
        return $scoredCount >= $totalKriteria;
    }

    /**
     * Cek apakah juri sudah menilai registrasi ini untuk semua kriteria (pakai registrasi_id)
     */
    public function checkRegistrasiAlreadyScored($registrasiId, $idJuri, $cabangId)
    {
        $kriteriaModel = new LombaKriteriaModel();
        $totalKriteria = count($kriteriaModel->getKriteriaByCabang($cabangId));
        
        $scoredCount = $this->where('registrasi_id', $registrasiId)
                            ->where('IdJuri', $idJuri)
                            ->where('cabang_id', $cabangId)
                            ->countAllResults();
        
        return $scoredCount >= $totalKriteria;
    }

    /**
     * Cek apakah juri sudah menilai kriteria tertentu (menggunakan registrasi_id)
     */
    public function checkJuriScoredKriteria($registrasiId, $idJuri, $kriteriaId)
    {
        return $this->where('registrasi_id', $registrasiId)
                    ->where('IdJuri', $idJuri)
                    ->where('kriteria_id', $kriteriaId)
                    ->countAllResults() > 0;
    }

    /**
     * Ambil nilai berdasarkan peserta (via registrasi_id)
     */
    public function getNilaiByPeserta($pesertaId)
    {
        // Cari registrasi_id dari peserta
        $builder = $this->db->table($this->table . ' n');
        $builder->select('n.*, k.NamaKriteria, k.Bobot, j.NamaJuri, j.UsernameJuri');
        $builder->join('tbl_lomba_kriteria k', 'k.id = n.kriteria_id', 'left');
        $builder->join('tbl_lomba_juri j', 'j.IdJuri = n.IdJuri', 'left');
        $builder->join('tbl_lomba_registrasi_anggota ra', 'ra.registrasi_id = n.registrasi_id', 'left');
        $builder->where('ra.peserta_id', $pesertaId);
        $builder->orderBy('k.Urutan', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Hitung nilai akhir berbobot untuk peserta
     * Mengembalikan rata-rata nilai dari semua juri dengan bobot diterapkan
     */
    public function calculateNilaiAkhir($pesertaId)
    {
        $nilai = $this->getNilaiByPeserta($pesertaId);
        
        if (empty($nilai)) {
            return 0;
        }

        // Kelompokkan berdasarkan kriteria dan hitung rata-rata per kriteria
        $kriteriaScores = [];
        foreach ($nilai as $n) {
            $kriteriaId = $n['kriteria_id'];
            if (!isset($kriteriaScores[$kriteriaId])) {
                $kriteriaScores[$kriteriaId] = [
                    'bobot' => (float) $n['Bobot'],
                    'scores' => []
                ];
            }
            $kriteriaScores[$kriteriaId]['scores'][] = (float) $n['Nilai'];
        }

        // Hitung rata-rata berbobot
        $totalWeightedScore = 0;
        $totalBobot = 0;
        
        foreach ($kriteriaScores as $ks) {
            $avgScore = array_sum($ks['scores']) / count($ks['scores']);
            $weightedScore = $avgScore * ($ks['bobot'] / 100);
            $totalWeightedScore += $weightedScore;
            $totalBobot += $ks['bobot'];
        }

        // Normalisasi jika total bobot != 100
        if ($totalBobot > 0 && $totalBobot != 100) {
            $totalWeightedScore = ($totalWeightedScore / $totalBobot) * 100;
        }

        return round($totalWeightedScore, 2);
    }

    /**
     * Ambil peringkat untuk cabang tertentu
     */
    public function getPeringkat($cabangId, $limit = null)
    {
        // Tentukan jumlah juri yang diharapkan per kriteria berdasarkan display mode
        $cabangModel = new LombaCabangModel();
        $displayMode = $cabangModel->getDisplayMode($cabangId);
        
        $juriModel = new LombaJuriModel();
        $juriCount = $juriModel->where('cabang_id', $cabangId)->where('Status', 'Aktif')->countAllResults();
        
        // Mode 2: Semua juri harus menilai kriteria yang sama
        // Mode 1 & 3: Tiap kriteria hanya butuh 1 juri (Mode 3 juri dibagi per kriteria)
        $expectedJuriCount = ($displayMode == 2) ? $juriCount : 1;

        // Ambil semua registrasi yang sudah dinilai untuk cabang ini
        $registrasiModel = new LombaRegistrasiModel();
        $registrasiList = $registrasiModel->getRegistrasiListByCabang($cabangId);

        $ranking = [];
        foreach ($registrasiList as $reg) {
            $summary = $this->getSkorTerpusat($reg['id'], $cabangId, $expectedJuriCount);
            if ($summary['nilai_akhir'] > 0) { // Hanya tampilkan yang sudah ada nilai
                $reg['NilaiAkhir'] = $summary['nilai_akhir'];
                $reg['TotalNilai'] = $summary['total_nilai'];
                $reg['StatusLabel'] = $summary['status_label'];
                $ranking[] = $reg;
            }
        }

        // Urutkan berdasarkan NilaiAkhir menurun
        usort($ranking, function($a, $b) {
            return $b['NilaiAkhir'] <=> $a['NilaiAkhir'];
        });

        // Tambahkan peringkat
        foreach ($ranking as $i => &$r) {
            $r['Peringkat'] = $i + 1;
        }

        if ($limit !== null) {
            return array_slice($ranking, 0, $limit);
        }

        return $ranking;
    }

    /**
     * Fungsi terpusat untuk menghitung skor (digunakan oleh Monitoring & Peringkat)
     * Mengembalikan array: [total_nilai, nilai_akhir, kriteria_data, is_complete, status_label]
     */
    public function getSkorTerpusat($registrasiId, $cabangId = null, $expectedJuriCount = 1)
    {
        $nilai = $this->getNilaiByRegistrasi($registrasiId);
        
        if (empty($nilai)) {
            return [
                'total_nilai' => 0,
                'nilai_akhir' => 0,
                'kriteria_data' => [],
                'is_complete' => false,
                'status_label' => '<span class="badge badge-secondary">Belum ada nilai</span>'
            ];
        }

        // Jika cabangId tidak diberikan, ambil dari salah satu baris nilai
        if (!$cabangId && !empty($nilai)) {
            $cabangId = $nilai[0]['cabang_id'];
        }

        // Ambil list kriteria lengkap untuk cabang ini agar konsisten
        $kriteriaModel = new LombaKriteriaModel();
        $allKriteria = $kriteriaModel->getKriteriaByCabang($cabangId);

        // Kelompokkan skor berdasarkan kriteria
        $scoresGrouped = [];
        foreach ($nilai as $n) {
            $scoresGrouped[$n['kriteria_id']][] = (float) $n['Nilai'];
        }

        $totalNilai = 0;
        $nilaiAkhir = 0; // Ini adalah total terbobot
        $kriteriaData = [];
        $isComplete = true;

        foreach ($allKriteria as $k) {
            $kId = $k['id'];
            $scores = $scoresGrouped[$kId] ?? [];
            
            // Hitung rata-rata jika ada lebih dari 1 juri untuk kriteria ini
            $avgNilai = !empty($scores) ? array_sum($scores) / count($scores) : 0;
            $bobotNilai = $avgNilai * ($k['Bobot'] / 100);

            $totalNilai += $avgNilai;
            $nilaiAkhir += $bobotNilai;

            // Cek kelengkapan (apakah semua juri sudah menilai kriteria ini?)
            $kriteriaIsComplete = count($scores) >= $expectedJuriCount;
            if (!$kriteriaIsComplete) $isComplete = false;

            $kriteriaData[$kId] = [
                'nama' => $k['NamaKriteria'],
                'bobot_persen' => (float) $k['Bobot'],
                'nilai_rata' => $avgNilai,
                'nilai_bobot' => $bobotNilai,
                'is_complete' => $kriteriaIsComplete,
                'juri_count' => count($scores)
            ];
        }

        $statusLabel = $isComplete 
            ? '<span class="badge badge-success">Selesai dinilai</span>' 
            : '<span class="badge badge-warning">Progress</span>';

        return [
            'total_nilai' => round($totalNilai, 2),
            'nilai_akhir' => round($nilaiAkhir, 2),
            'kriteria_data' => $kriteriaData,
            'is_complete' => $isComplete,
            'status_label' => $statusLabel
        ];
    }

    /**
     * Hitung nilai akhir berbobot untuk registrasi
     * Sekarang menggunakan fungsi terpusat
     */
    public function calculateNilaiAkhirByRegistrasi($registrasiId)
    {
        $summary = $this->getSkorTerpusat($registrasiId);
        return $summary['nilai_akhir'];
    }

    /**
     * Ambil daftar registrasi unik yang sudah dinilai oleh juri
     */
    public function getRegistrasiScoredByJuri($idJuri, $cabangId)
    {
        $builder = $this->db->table($this->table . ' n');
        $builder->select('n.registrasi_id, r.NoPeserta, r.TipePeserta, r.NamaKelompok, MAX(n.updated_at) as updated_at');
        // Join untuk Individu - ambil nama santri
        $builder->select('COALESCE(s.NamaSantri, r.NamaKelompok) as NamaSantri', false);
        $builder->join('tbl_lomba_registrasi r', 'r.id = n.registrasi_id', 'left');
        $builder->join('tbl_lomba_registrasi_anggota ra', 'ra.registrasi_id = r.id', 'left');
        $builder->join('tbl_lomba_peserta p', 'p.id = ra.peserta_id', 'left');
        $builder->join('tbl_santri_baru s', 's.IdSantri = p.IdSantri', 'left');
        $builder->where('n.IdJuri', $idJuri);
        $builder->where('n.cabang_id', $cabangId);
        $builder->groupBy('n.registrasi_id, r.NoPeserta, r.TipePeserta, r.NamaKelompok');
        $builder->orderBy('updated_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil data peringkat untuk satu registrasi spesifik
     * Menggunakan getPeringkat untuk kalkulasi konsisten
     */
    public function getPeringkatById($registrasiId)
    {
        $registrasiModel = new LombaRegistrasiModel();
        $registrasi = $registrasiModel->find($registrasiId);
        
        if (!$registrasi) {
            return null;
        }

        $cabangId = $registrasi['cabang_id'];
        
        // Ambil semua peringkat untuk cabang ini
        $allPeringkat = $this->getPeringkat($cabangId);
        
        // Cari registrasi yang dimaksud
        foreach ($allPeringkat as $p) {
            if ($p['id'] == $registrasiId) {
                return $p;
            }
        }
        
        return null;
    }
}
