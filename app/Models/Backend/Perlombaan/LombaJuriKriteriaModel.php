<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaJuriKriteriaModel extends Model
{
    protected $table = 'tbl_lomba_juri_kriteria';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'juri_id',
        'kriteria_id',
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Ambil kriteria IDs yang ditetapkan untuk juri
     */
    public function getKriteriaIdsByJuri($juriId)
    {
        $results = $this->where('juri_id', $juriId)->findAll();
        return array_column($results, 'kriteria_id');
    }

    /**
     * Ambil kriteria lengkap yang ditetapkan untuk juri
     */
    public function getKriteriaByJuri($juriId)
    {
        $builder = $this->db->table($this->table . ' jk');
        $builder->select('k.*');
        $builder->join('tbl_lomba_kriteria k', 'k.id = jk.kriteria_id');
        $builder->where('jk.juri_id', $juriId);
        $builder->orderBy('k.Urutan', 'ASC');
        $builder->orderBy('k.id', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Set kriteria untuk juri (replace all existing)
     * @param int $juriId
     * @param array $kriteriaIds - array of kriteria IDs, empty array = reset to default
     */
    public function setKriteriaForJuri($juriId, $kriteriaIds)
    {
        $this->db->transStart();
        
        // Hapus semua kriteria existing untuk juri ini
        $this->where('juri_id', $juriId)->delete();
        
        // Insert kriteria baru jika ada
        if (!empty($kriteriaIds)) {
            foreach ($kriteriaIds as $kriteriaId) {
                $this->insert([
                    'juri_id' => $juriId,
                    'kriteria_id' => $kriteriaId,
                ]);
            }
        }
        
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }

    /**
     * Hapus semua setting kriteria untuk juri (reset ke default)
     */
    public function clearKriteriaForJuri($juriId)
    {
        return $this->where('juri_id', $juriId)->delete();
    }

    /**
     * Cek apakah juri punya setting kriteria khusus
     */
    public function hasCustomKriteria($juriId)
    {
        return $this->where('juri_id', $juriId)->countAllResults() > 0;
    }

    /**
     * Hitung jumlah kriteria yang ditetapkan untuk juri
     */
    public function countKriteriaForJuri($juriId)
    {
        return $this->where('juri_id', $juriId)->countAllResults();
    }

    /**
     * Alias for getKriteriaIdsByJuri for consistency
     */
    public function getKriteriaIdsForJuri($juriId)
    {
        return $this->getKriteriaIdsByJuri($juriId);
    }
}
