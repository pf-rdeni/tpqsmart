<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaKriteriaModel extends Model
{
    protected $table = 'tbl_lomba_kriteria';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'cabang_id',
        'NamaKriteria',
        'Bobot',
        'NilaiMin',
        'NilaiMax',
        'Urutan'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'cabang_id'    => 'required|integer',
        'NamaKriteria' => 'required|max_length[255]',
        'Bobot'        => 'required|decimal',
    ];

    /**
     * Ambil kriteria berdasarkan cabang
     */
    public function getKriteriaByCabang($cabangId)
    {
        return $this->where('cabang_id', $cabangId)
                    ->orderBy('Urutan', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Ambil total bobot untuk cabang (seharusnya = 100%)
     */
    public function getTotalBobot($cabangId)
    {
        $result = $this->selectSum('Bobot')
                       ->where('cabang_id', $cabangId)
                       ->first();
        
        return (float) ($result['Bobot'] ?? 0);
    }

    /**
     * Validasi total bobot = 100%
     */
    public function validateTotalBobot($cabangId)
    {
        $total = $this->getTotalBobot($cabangId);
        return abs($total - 100) < 0.01; // Toleransi selisih floating point
    }

    /**
     * Ambil kriteria beserta info cabangnya
     */
    public function getKriteriaWithCabang($id)
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . '.*, c.NamaCabang, c.lomba_id');
        $builder->join('tbl_lomba_cabang c', 'c.id = ' . $this->table . '.cabang_id', 'left');
        $builder->where($this->table . '.id', $id);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Ambil kriteria yang boleh dinilai oleh juri tertentu
     * Jika juri tidak punya setting khusus, return semua kriteria cabang
     * 
     * @param int $juriId - ID juri (dari tbl_lomba_juri.id)
     * @param int $cabangId - ID cabang
     * @return array
     */
    public function getKriteriaForJuri($juriId, $cabangId)
    {
        $juriKriteriaModel = new LombaJuriKriteriaModel();
        
        // Cek apakah juri punya setting khusus
        if ($juriKriteriaModel->hasCustomKriteria($juriId)) {
            // Return hanya kriteria yang ditetapkan
            return $juriKriteriaModel->getKriteriaByJuri($juriId);
        }
        
        // Default: return semua kriteria cabang
        return $this->getKriteriaByCabang($cabangId);
    }
}
