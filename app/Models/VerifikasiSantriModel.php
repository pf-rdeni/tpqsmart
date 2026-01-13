<?php

namespace App\Models;

use CodeIgniter\Model;

class VerifikasiSantriModel extends SantriBaruModel
{
    // Inherit from SantriBaruModel since we are working with the same table
    protected $table            = 'tbl_santri_baru';
    protected $primaryKey       = 'id';
    
    /**
     * Get list of santri for verification.
     * Can filter by status if needed, or get all.
     * Currently fetching all to display in the list with their status.
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

        // Order by Status (Pending/Baru first usually, but definition says DESC updated_at in SantriBaruModel)
        // Let's order by latest updated
        $builder->orderBy('tbl_santri_baru.updated_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    public function getSantriById($id)
    {
        return $this->find($id);
    }
}
