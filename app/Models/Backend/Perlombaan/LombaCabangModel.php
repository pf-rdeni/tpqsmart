<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaCabangModel extends Model
{
    protected $table = 'tbl_lomba_cabang';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'lomba_id',
        'NamaCabang',
        'Kategori',
        'Tipe',
        'TipePeserta',
        'MaxAnggotaKelompok',
        'UsiaMin',
        'UsiaMax',
        'KelasMin',
        'KelasMax',
        'MaxPeserta',
        'MaxPerTpq',
        'Status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'lomba_id'   => 'required|integer',
        'NamaCabang' => 'required|max_length[255]',
    ];

    /**
     * Ambil cabang berdasarkan lomba
     */
    public function getCabangByLomba($lombaId)
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->select('c.*, 
            kmin.NamaKelas as NamaKelasMin, 
            kmax.NamaKelas as NamaKelasMax,
            (SELECT COUNT(*) FROM tbl_lomba_peserta p WHERE p.cabang_id = c.id AND p.StatusPendaftaran = "valid") as total_peserta,
            (SELECT COUNT(*) FROM tbl_lomba_juri j WHERE j.cabang_id = c.id AND j.Status = "Aktif") as total_juri,
            (SELECT COUNT(*) FROM tbl_lomba_kriteria k WHERE k.cabang_id = c.id) as total_kriteria
        ');
        $builder->join('tbl_kelas kmin', 'kmin.IdKelas = c.KelasMin', 'left');
        $builder->join('tbl_kelas kmax', 'kmax.IdKelas = c.KelasMax', 'left');
        $builder->where('c.lomba_id', $lombaId);
        $builder->orderBy('c.NamaCabang', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil cabang beserta kriterianya
     */
    public function getCabangWithKriteria($id)
    {
        $cabang = $this->find($id);
        if (!$cabang) {
            return null;
        }

        $kriteriaModel = new LombaKriteriaModel();
        $cabang['kriteria'] = $kriteriaModel->getKriteriaByCabang($id);

        return $cabang;
    }

    /**
     * Validasi apakah santri memenuhi syarat untuk cabang ini
     * @param int $cabangId
     * @param array $santriData - harus berisi JenisKelamin dan TanggalLahir atau Usia
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validatePeserta($cabangId, $santriData)
    {
        $cabang = $this->find($cabangId);
        if (!$cabang) {
            return ['valid' => false, 'message' => 'Cabang tidak ditemukan'];
        }

        // Validasi jenis kelamin
        $jenisKelamin = $santriData['JenisKelamin'] ?? null;
        if ($cabang['Kategori'] !== 'Campuran') {
            if ($cabang['Kategori'] === 'Putra' && $jenisKelamin !== 'Laki-laki') {
                return ['valid' => false, 'message' => 'Cabang ini hanya untuk peserta Putra'];
            }
            if ($cabang['Kategori'] === 'Putri' && $jenisKelamin !== 'Perempuan') {
                return ['valid' => false, 'message' => 'Cabang ini hanya untuk peserta Putri'];
            }
        }

        // Hitung usia
        $usia = null;
        if (isset($santriData['Usia'])) {
            $usia = (int) $santriData['Usia'];
        } elseif (isset($santriData['TanggalLahir']) && !empty($santriData['TanggalLahir'])) {
            $birthDate = new \DateTime($santriData['TanggalLahir']);
            $now = new \DateTime();
            $usia = $now->diff($birthDate)->y;
        }

        // Validasi usia
        if ($usia !== null) {
            if ($usia < $cabang['UsiaMin']) {
                return ['valid' => false, 'message' => "Usia minimal peserta adalah {$cabang['UsiaMin']} tahun"];
            }
            if ($usia > $cabang['UsiaMax']) {
                return ['valid' => false, 'message' => "Usia maksimal peserta adalah {$cabang['UsiaMax']} tahun"];
            }
        }
        
        // Validasi Kelas (Jika diset di cabang)
        // Logic ini aktif hanya jika KelasMin/KelasMax diset > 0
        if ((!empty($cabang['KelasMin']) && $cabang['KelasMin'] > 0) || (!empty($cabang['KelasMax']) && $cabang['KelasMax'] > 0)) {
            $santriKelasId = $santriData['IdKelas'] ?? null;
            if (!$santriKelasId) {
                return ['valid' => false, 'message' => 'Data kelas peserta tidak ditemukan'];
            }

            // Ambil Order Kelas
            $db = \Config\Database::connect();
            $kelasList = $db->table('tbl_kelas')->orderBy('NamaKelas', 'ASC')->get()->getResultArray();
            $kelasOrder = [];
            foreach ($kelasList as $index => $k) {
                $kelasOrder[$k['IdKelas']] = $index;
            }

            $santriIndex = $kelasOrder[$santriKelasId] ?? -1;
            
            // Cek Min
            if (!empty($cabang['KelasMin']) && $cabang['KelasMin'] > 0) {
                 $minIndex = $kelasOrder[$cabang['KelasMin']] ?? -1;
                 // Jika setting invalid (kelas gak ketemu), fail safe allow? No, restrict.
                 if ($minIndex !== -1 && $santriIndex < $minIndex) {
                     return ['valid' => false, 'message' => 'Kelas peserta di bawah batas minimal (' . $cabang['KelasMin'] . ')']; 
                     // Note: Error message shows ID, ideally show NamaKelas but requires extra query. 
                     // For now showing generically.
                     // Better: 'Kelas peserta belum mencukupi batas minimal'
                     return ['valid' => false, 'message' => 'Kelas peserta belum memenuhi syarat minimal cabang ini'];
                 }
            }
            
            // Cek Max
            if (!empty($cabang['KelasMax']) && $cabang['KelasMax'] > 0) {
                 $maxIndex = $kelasOrder[$cabang['KelasMax']] ?? 99999;
                 if ($maxIndex !== 99999 && $santriIndex > $maxIndex) {
                     return ['valid' => false, 'message' => 'Kelas peserta melebihi batas maksimal cabang ini'];
                 }
            }
        }

        return ['valid' => true, 'message' => 'Memenuhi syarat'];
    }

    /**
     * Ambil cabang beserta statistiknya
     */
    public function getCabangWithStats($id)
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->select('c.*, kmin.NamaKelas as NamaKelasMin, kmax.NamaKelas as NamaKelasMax');
        $builder->join('tbl_kelas kmin', 'kmin.IdKelas = c.KelasMin', 'left');
        $builder->join('tbl_kelas kmax', 'kmax.IdKelas = c.KelasMax', 'left');
        $builder->where('c.id', $id);
        $cabang = $builder->get()->getRowArray();

        if (!$cabang) {
            return null;
        }

        $db = \Config\Database::connect();

        // Hitung peserta
        $pesertaCount = $db->table('tbl_lomba_peserta')
                           ->where('cabang_id', $id)
                           ->where('StatusPendaftaran', 'valid')
                           ->countAllResults();

        // Hitung juri
        $juriCount = $db->table('tbl_lomba_juri')
                        ->where('cabang_id', $id)
                        ->where('Status', 'Aktif')
                        ->countAllResults();

        // Hitung kriteria
        $kriteriaCount = $db->table('tbl_lomba_kriteria')
                            ->where('cabang_id', $id)
                            ->countAllResults();

        $cabang['total_peserta'] = $pesertaCount;
        $cabang['total_juri'] = $juriCount;
        $cabang['total_kriteria'] = $kriteriaCount;

        return $cabang;
    }

    /**
     * Ambil semua cabang beserta info lomba
     */
    public function getAllCabangWithLomba()
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->select('c.*, l.NamaLomba, l.TanggalMulai, l.TanggalSelesai, l.Status as StatusLomba, l.IdTpq,
            k_min.NamaKelas as NamaKelasMin, k_max.NamaKelas as NamaKelasMax,
            (CASE 
                WHEN c.Tipe = "Kelompok" OR c.TipePeserta = "Kelompok" THEN 
                    (SELECT COUNT(DISTINCT CONCAT(p.IdTpq, "-", p.GrupUrut)) FROM tbl_lomba_peserta p WHERE p.cabang_id = c.id AND p.StatusPendaftaran = "valid")
                ELSE 
                    (SELECT COUNT(*) FROM tbl_lomba_peserta p WHERE p.cabang_id = c.id AND p.StatusPendaftaran = "valid")
            END) as total_peserta,
            (SELECT COUNT(*) FROM tbl_lomba_registrasi r WHERE r.cabang_id = c.id) as total_teregistrasi');
        $builder->join('tbl_lomba_master l', 'l.id = c.lomba_id', 'left');
        $builder->join('tbl_kelas k_min', 'k_min.IdKelas = c.KelasMin', 'left');
        $builder->join('tbl_kelas k_max', 'k_max.IdKelas = c.KelasMax', 'left');
        $builder->where('l.Status', 'aktif');
        $builder->where('c.Status', 'aktif');
        $builder->orderBy('l.NamaLomba', 'ASC');
        $builder->orderBy('c.NamaCabang', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil cabang dengan info lomba
     */
    public function getCabangWithLomba($cabangId)
    {
        $builder = $this->db->table($this->table . ' c');
        $builder->select('c.*, l.NamaLomba, l.TanggalMulai, l.TanggalSelesai, l.Status as StatusLomba, l.IdTpq');
        $builder->join('tbl_lomba_master l', 'l.id = c.lomba_id', 'left');
        $builder->where('c.id', $cabangId);
        
        return $builder->get()->getRowArray();
    }
    /**
     * Tentukan display mode berdasarkan pengaturan juri & kriteria
     * 1 = Juri tunggal
     * 2 = Juri banyak, kriteria sama (show rata-rata)
     * 3 = Juri banyak, kriteria dibagi (split)
     */
    public function getDisplayMode($cabangId)
    {
        $db = \Config\Database::connect();
        
        // Count active juris
        $juriCount = $db->table('tbl_lomba_juri')
                        ->where('cabang_id', $cabangId)
                        ->where('Status', 'Aktif')
                        ->countAllResults();
        
        if ($juriCount <= 1) return 1;
        
        // Check if any juri has custom kriteria settings
        $hasCustomKriteria = $db->table('tbl_lomba_juri_kriteria jk')
                               ->join('tbl_lomba_juri j', 'j.id = jk.juri_id')
                               ->where('j.cabang_id', $cabangId)
                               ->where('j.Status', 'Aktif')
                               ->countAllResults() > 0;
        
        return $hasCustomKriteria ? 3 : 2;
    }
}
