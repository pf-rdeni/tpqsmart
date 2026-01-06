<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaPesertaModel extends Model
{
    protected $table = 'tbl_lomba_peserta';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'NoPeserta',
        'lomba_id',
        'cabang_id',
        'IdSantri',
        'IdTpq',
        'StatusPendaftaran',
        'Catatan',
        'TipePendaftaran',
        'NamaGrup',
        'GrupUrut'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'lomba_id'  => 'required|integer',
        'cabang_id' => 'required|integer',
        'IdSantri'  => 'required',
    ];

    /**
     * Ambil peserta berdasarkan cabang
     */
    public function getPesertaByCabang($cabangId, $status = null)
    {
        $builder = $this->where('cabang_id', $cabangId);
        
        if ($status !== null) {
            $builder->where('StatusPendaftaran', $status);
        }
        
        return $builder->orderBy('NoPeserta', 'ASC')->findAll();
    }

    /**
     * Ambil peserta berdasarkan NoPeserta
     */
    public function getPesertaByNoPeserta($noPeserta)
    {
        return $this->where('NoPeserta', $noPeserta)->first();
    }

    /**
     * Ambil peserta beserta info santri
     */
    public function getPesertaWithSantri($id)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, s.NamaSantri, s.JenisKelamin, s.TanggalLahirSantri, s.PhotoProfil, c.NamaCabang, l.NamaLomba');
        $builder->join('tbl_santri_baru s', 's.IdSantri = p.IdSantri', 'left');
        $builder->join('tbl_lomba_cabang c', 'c.id = p.cabang_id', 'left');
        $builder->join('tbl_lomba_master l', 'l.id = p.lomba_id', 'left');
        $builder->where('p.id', $id);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Ambil peserta berdasarkan NoPeserta beserta info santri
     */
    public function getPesertaByNoPesertaWithSantri($noPeserta)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, s.NamaSantri, s.JenisKelamin, s.TanggalLahirSantri, s.PhotoProfil, c.NamaCabang, c.Kategori, c.Tipe, l.NamaLomba');
        $builder->join('tbl_santri_baru s', 's.IdSantri = p.IdSantri', 'left');
        $builder->join('tbl_lomba_cabang c', 'c.id = p.cabang_id', 'left');
        $builder->join('tbl_lomba_master l', 'l.id = p.lomba_id', 'left');
        $builder->where('p.NoPeserta', $noPeserta);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Generate nomor peserta unik untuk cabang
     * Format: [KodeCabang][CabangId]-[NomorUrut]
     */
    public function generateNoPeserta($cabangId)
    {
        $cabangModel = new LombaCabangModel();
        $cabang = $cabangModel->find($cabangId);
        
        if (!$cabang) {
            return null;
        }

        // Buat kode cabang dengan cabang_id untuk keunikan
        // Format: 3 huruf pertama + cabang_id (contoh: ADZ1, ADZ2)
        $cabangCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $cabang['NamaCabang']), 0, 3));
        if (strlen($cabangCode) < 3) {
            $cabangCode = str_pad($cabangCode, 3, 'X');
        }
        $prefix = $cabangCode . $cabangId;

        // Cari nomor urut terakhir untuk prefix ini secara global
        $lastPeserta = $this->where('NoPeserta LIKE', $prefix . '-%')
                            ->orderBy('NoPeserta', 'DESC')
                            ->first();

        $sequence = 1;
        if ($lastPeserta && preg_match('/-(\d+)$/', $lastPeserta['NoPeserta'], $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Cek apakah santri sudah terdaftar di cabang ini
     */
    public function isAlreadyRegistered($idSantri, $cabangId)
    {
        return $this->where('IdSantri', $idSantri)
                    ->where('cabang_id', $cabangId)
                    ->countAllResults() > 0;
    }

    /**
     * Ambil daftar peserta beserta info santri untuk cabang tertentu
     */
    public function getPesertaListByCabang($cabangId, $status = null, $idTpq = null)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.id, p.NoPeserta, p.lomba_id, p.cabang_id, p.IdSantri, p.IdTpq, p.StatusPendaftaran, p.Catatan, p.TipePendaftaran, p.NamaGrup, p.GrupUrut, p.created_at, p.updated_at, s.NamaSantri, s.JenisKelamin, s.TanggalLahirSantri, s.PhotoProfil, t.NamaTpq');
        $builder->join('tbl_santri_baru s', 's.IdSantri = p.IdSantri', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = p.IdTpq', 'left');
        $builder->where('p.cabang_id', $cabangId);
        
        if ($status !== null) {
            $builder->where('p.StatusPendaftaran', $status);
        }

        if ($idTpq !== null) {
            $builder->where('p.IdTpq', $idTpq);
        }
        
        $builder->groupBy('p.id');
        $builder->orderBy('p.NoPeserta', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Hitung jumlah peserta individu yang didaftarkan TPQ untuk cabang tertentu
     */
    public function countRegisteredByTpq($cabangId, $idTpq)
    {
        return $this->where('cabang_id', $cabangId)
                    ->where('IdTpq', $idTpq)
                    ->where('TipePendaftaran', 'individu')
                    ->countAllResults();
    }

    /**
     * Hitung jumlah grup yang didaftarkan TPQ untuk cabang tertentu
     */
    public function countGroupsByTpq($cabangId, $idTpq)
    {
        return $this->where('cabang_id', $cabangId)
                    ->where('IdTpq', $idTpq)
                    ->where('TipePendaftaran', 'kelompok')
                    ->selectMax('GrupUrut')
                    ->first()['GrupUrut'] ?? 0;
    }

    /**
     * Generate urutan grup berikutnya untuk TPQ di cabang tertentu
     */
    public function getNextGrupUrut($cabangId, $idTpq)
    {
        $max = $this->countGroupsByTpq($cabangId, $idTpq);
        return $max + 1;
    }

    /**
     * Hitung kuota terpakai (individu atau grup tergantung tipe cabang)
     */
    public function getQuotaUsedByTpq($cabangId, $idTpq, $tipePeserta = 'Individu')
    {
        if (strtolower($tipePeserta) === 'kelompok') {
            return $this->countGroupsByTpq($cabangId, $idTpq);
        }
        return $this->countRegisteredByTpq($cabangId, $idTpq);
    }
}
