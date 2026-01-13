<?php

namespace App\Models\Backend\Santri;

use CodeIgniter\Model;
use App\Models\SantriBaruModel;

class VerifikasiSantriModel extends SantriBaruModel
{
    // Inherit from SantriBaruModel since we are working with the same table
    protected $table            = 'tbl_santri_baru';
    protected $primaryKey       = 'id';
    
    /**
     * Get list of santri for verification.
     * Can filter by status if needed, or get all.
     * Currently fetching all to display in the list with their status.
     * 
     * @param string|null $idTpq
     * @return array
     */
    public function getSantriForVerification($idTpq = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('
            tbl_santri_baru.id,
            tbl_santri_baru.IdSantri,
            tbl_santri_baru.PhotoProfil,
            tbl_santri_baru.NamaSantri,
            tbl_santri_baru.JenisKelamin,
            tbl_santri_baru.TempatLahirSantri,
            tbl_santri_baru.TanggalLahirSantri,
            tbl_santri_baru.NamaAyah,
            tbl_santri_baru.KelurahanDesaSantri,
            tbl_santri_baru.NoHpAyah,
            tbl_santri_baru.NoHpIbu,
            tbl_santri_baru.Status,
            tbl_tpq.NamaTpq
        ');
        $builder->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_santri_baru.IdTpq', 'left');
        
        if ($idTpq) {
            $builder->where('tbl_santri_baru.IdTpq', $idTpq);
        }

        // Group by primary key to prevent duplicates
        $builder->groupBy('tbl_santri_baru.id');
        
        // Order by latest updated
        $builder->orderBy('tbl_santri_baru.updated_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getSantriById($id)
    {
        return $this->find($id);
    }
}
