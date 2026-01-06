<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaRegistrasiModel extends Model
{
    protected $table = 'tbl_lomba_registrasi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'cabang_id',
        'NoPeserta',
        'TipePeserta',
        'NamaKelompok',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Generate nomor peserta berurut berikutnya per cabang (dimulai 100)
     */
    public function getNextNoPeserta($cabangId)
    {
        // Ambil nomor terakhir yang digunakan di cabang ini
        $lastNumber = $this->where('cabang_id', $cabangId)
                          ->orderBy('CAST(NoPeserta AS UNSIGNED)', 'DESC')
                          ->first();
        
        if ($lastNumber) {
            return (int) $lastNumber['NoPeserta'] + 1;
        }
        
        // Jika belum ada, mulai dari 100
        return 100;
    }

    /**
     * Generate nomor peserta berurut untuk bulk registrasi
     * Nomor berurut (100, 101, 102...), peserta yang diacak
     */
    public function generateSequentialNoPeserta($cabangId, $count)
    {
        $startNumber = $this->getNextNoPeserta($cabangId);
        
        // Cek apakah masih cukup nomor (max 999)
        if ($startNumber + $count - 1 > 999) {
            return null; // Tidak cukup nomor tersedia
        }
        
        // Generate nomor berurut
        $nomorList = [];
        for ($i = 0; $i < $count; $i++) {
            $nomorList[] = $startNumber + $i;
        }
        
        return $nomorList;
    }

    /**
     * Ambil registrasi berdasarkan cabang
     */
    public function getRegistrasiByCabang($cabangId)
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select('r.*');
        $builder->where('r.cabang_id', $cabangId);
        $builder->orderBy('r.NoPeserta', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil registrasi dengan info santri/kelompok untuk ranking
     */
    public function getRegistrasiListByCabang($cabangId)
    {
        $builder = $this->db->table($this->table . ' r');
        $builder->select("r.id, r.NoPeserta, r.TipePeserta, r.NamaKelompok, r.cabang_id");
        // Untuk individu: ambil nama santri dari anggota pertama
        // Untuk kelompok: gunakan NamaKelompok
        $builder->select("COALESCE(s.NamaSantri, r.NamaKelompok) as NamaSantri", false);
        $builder->select("t.NamaTpq");
        $builder->join('tbl_lomba_registrasi_anggota ra', 'ra.registrasi_id = r.id', 'left');
        $builder->join('tbl_lomba_peserta p', 'p.id = ra.peserta_id', 'left');
        $builder->join('tbl_santri_baru s', 's.IdSantri = p.IdSantri', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = p.IdTpq', 'left');
        $builder->where('r.cabang_id', $cabangId);
        $builder->groupBy('r.id');
        $builder->orderBy('r.NoPeserta', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil registrasi dengan detail anggota
     */
    public function getRegistrasiWithAnggota($cabangId)
    {
        $registrasiList = $this->getRegistrasiByCabang($cabangId);
        
        $anggotaModel = new LombaRegistrasiAnggotaModel();
        
        foreach ($registrasiList as &$reg) {
            $reg['anggota'] = $anggotaModel->getAnggotaByRegistrasi($reg['id']);
        }
        
        return $registrasiList;
    }

    /**
     * Ambil registrasi berdasarkan nomor peserta dan cabang
     */
    public function getByNoPeserta($noPeserta, $cabangId)
    {
        return $this->where('NoPeserta', $noPeserta)
                    ->where('cabang_id', $cabangId)
                    ->first();
    }

    /**
     * Ambil registrasi dengan info santri berdasarkan NoPeserta dan cabang
     * Untuk input nilai juri
     */
    public function getByNoPesertaWithSantri($noPeserta, $cabangId)
    {
        $registrasi = $this->getByNoPeserta($noPeserta, $cabangId);
        
        if (!$registrasi) {
            return null;
        }
        
        // Ambil anggota (untuk individu = 1 anggota, untuk kelompok = banyak)
        $anggotaModel = new LombaRegistrasiAnggotaModel();
        $anggota = $anggotaModel->getAnggotaByRegistrasi($registrasi['id']);
        
        if (empty($anggota)) {
            return null;
        }
        
        // Ambil info cabang
        $cabang = $this->db->table('tbl_lomba_cabang c')
                          ->select('c.NamaCabang, c.lomba_id, l.NamaLomba')
                          ->join('tbl_lomba_master l', 'l.id = c.lomba_id', 'left')
                          ->where('c.id', $cabangId)
                          ->get()->getRowArray();
        
        // Untuk individu, ambil data santri pertama
        $firstAnggota = $anggota[0];
        
        return [
            'id'               => $registrasi['id'],
            'cabang_id'        => $registrasi['cabang_id'],
            'NoPeserta'        => $registrasi['NoPeserta'],
            'TipePeserta'      => $registrasi['TipePeserta'],
            'NamaKelompok'     => $registrasi['NamaKelompok'],
            'NamaSantri'       => $registrasi['TipePeserta'] === 'Kelompok' 
                                  ? $registrasi['NamaKelompok'] 
                                  : $firstAnggota['NamaSantri'],
            'JenisKelamin'     => $firstAnggota['JenisKelamin'] ?? null,
            'PhotoProfil'      => $firstAnggota['PhotoProfil'] ?? null,
            'NamaTpq'          => $firstAnggota['NamaTpq'] ?? null,
            'NamaCabang'       => $cabang['NamaCabang'] ?? null,
            'NamaLomba'        => $cabang['NamaLomba'] ?? null,
            'anggota'          => $anggota,
        ];
    }

    /**
     * Generate nama kelompok otomatis
     */
    public function generateNamaKelompok($cabangId)
    {
        // Ambil singkatan cabang
        $cabang = $this->db->table('tbl_lomba_cabang')
                          ->where('id', $cabangId)
                          ->get()->getRow();
        
        if (!$cabang) {
            return 'Tim-1';
        }
        
        // Buat singkatan dari nama cabang
        $words = explode(' ', $cabang->NamaCabang);
        $singkatan = '';
        foreach ($words as $word) {
            $singkatan .= strtoupper(substr($word, 0, 1));
        }
        
        // Hitung jumlah kelompok yang sudah ada
        $existingCount = $this->where('cabang_id', $cabangId)
                              ->where('TipePeserta', 'Kelompok')
                              ->countAllResults();
        
        return $singkatan . '-Tim' . ($existingCount + 1);
    }

    /**
     * Bulk registrasi untuk individu
     * Peserta diacak, nomor berurut (100, 101, 102...)
     */
    public function bulkRegistrasiIndividu($cabangId, $pesertaIds)
    {
        $count = count($pesertaIds);
        
        if ($count === 0) {
            return ['success' => false, 'message' => 'Tidak ada peserta yang dipilih'];
        }
        
        $nomorList = $this->generateSequentialNoPeserta($cabangId, $count);
        
        if ($nomorList === null) {
            return ['success' => false, 'message' => 'Tidak cukup nomor peserta tersedia (max 999)'];
        }
        
        // ACAK PESERTA, bukan nomornya
        $shuffledPesertaIds = $pesertaIds;
        shuffle($shuffledPesertaIds);
        // Reset index setelah shuffle
        $shuffledPesertaIds = array_values($shuffledPesertaIds);
        
        $anggotaModel = new LombaRegistrasiAnggotaModel();
        $results = [];
        
        $this->db->transStart();
        
        try {
            for ($i = 0; $i < $count; $i++) {
                $pesertaId = $shuffledPesertaIds[$i];
                $noPeserta = (string) $nomorList[$i];
                
                $registrasiData = [
                    'cabang_id'    => $cabangId,
                    'NoPeserta'    => $noPeserta,
                    'TipePeserta'  => 'Individu',
                    'NamaKelompok' => null,
                ];
                
                if (!$this->insert($registrasiData)) {
                    throw new \Exception('Gagal insert registrasi: ' . json_encode($this->errors()));
                }
                
                $registrasiId = $this->getInsertID();
                
                if (!$registrasiId) {
                    throw new \Exception('Gagal mendapatkan ID registrasi');
                }
                
                // Tambahkan anggota (1 peserta)
                if (!$anggotaModel->insert([
                    'registrasi_id' => $registrasiId,
                    'peserta_id'    => $pesertaId,
                ])) {
                    throw new \Exception('Gagal insert anggota: ' . json_encode($anggotaModel->errors()));
                }
                
                $results[] = [
                    'peserta_id'    => $pesertaId,
                    'registrasi_id' => $registrasiId,
                    'NoPeserta'     => $noPeserta,
                ];
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return ['success' => false, 'message' => 'Transaksi database gagal'];
            }
            
            return ['success' => true, 'data' => $results];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Bulk registrasi untuk kelompok
     * Kelompok diacak, nomor berurut (100, 101, 102...)
     * $kelompokList = array of ['peserta_ids' => [...], 'nama_kelompok' => '...']
     */
    public function bulkRegistrasiKelompok($cabangId, $kelompokList)
    {
        $count = count($kelompokList);
        $nomorList = $this->generateSequentialNoPeserta($cabangId, $count);
        
        if ($nomorList === null) {
            return ['success' => false, 'message' => 'Tidak cukup nomor peserta tersedia (max 999)'];
        }
        
        // ACAK KELOMPOK, bukan nomornya
        $shuffledKelompokList = $kelompokList;
        shuffle($shuffledKelompokList);
        
        $anggotaModel = new LombaRegistrasiAnggotaModel();
        $results = [];
        
        $this->db->transStart();
        
        try {
            foreach ($shuffledKelompokList as $i => $kelompok) {
                $namaKelompok = !empty($kelompok['nama_kelompok']) 
                    ? $kelompok['nama_kelompok'] 
                    : $this->generateNamaKelompok($cabangId);
                
                $registrasiData = [
                    'cabang_id'    => $cabangId,
                    'NoPeserta'    => (string) $nomorList[$i],
                    'TipePeserta'  => 'Kelompok',
                    'NamaKelompok' => $namaKelompok,
                ];
                
                if (!$this->insert($registrasiData)) {
                    throw new \Exception('Gagal insert registrasi kelompok');
                }
                
                $registrasiId = $this->getInsertID();
                
                // Tambahkan semua anggota kelompok
                foreach ($kelompok['peserta_ids'] as $pesertaId) {
                    if (!$anggotaModel->insert([
                        'registrasi_id' => $registrasiId,
                        'peserta_id'    => $pesertaId,
                    ])) {
                        throw new \Exception('Gagal insert anggota kelompok');
                    }
                }
                
                $results[] = [
                    'registrasi_id' => $registrasiId,
                    'NoPeserta'     => (string) $nomorList[$i],
                    'NamaKelompok'  => $namaKelompok,
                    'anggota_count' => count($kelompok['peserta_ids']),
                ];
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return ['success' => false, 'message' => 'Gagal menyimpan registrasi'];
            }
            
            return ['success' => true, 'data' => $results];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Hitung jumlah peserta yang sudah teregistrasi per cabang
     */
    public function countRegistrasiByCabang($cabangId)
    {
        return $this->where('cabang_id', $cabangId)->countAllResults();
    }
}
