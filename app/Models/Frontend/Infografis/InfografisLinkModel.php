<?php

namespace App\Models\Frontend\Infografis;

use CodeIgniter\Model;

class InfografisLinkModel extends Model
{
    protected $table            = 'tbl_infografis_link';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'IdTpq',
        'IdTahunAjaran',
        'HashKey',
        'NamaLink',
        'SlideshowInterval',
        'RefreshInterval',
        'IsActive',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get link by HashKey
     */
    public function getLinkByHash(string $hashKey)
    {
        return $this->where('HashKey', $hashKey)
                    ->where('IsActive', 1)
                    ->first();
    }

    /**
     * Get links by IdTpq
     * IdTpq = 0 atau null -> milik FKPQ
     * IdTpq = spesifik -> milik TPQ tertentu
     */
    public function getLinksByTpq($idTpq)
    {
        if (empty($idTpq) || $idTpq == '0' || $idTpq == 0) {
            return $this->groupStart()
                        ->where('IdTpq', '0')
                        ->orWhere('IdTpq', null)
                        ->orWhere('IdTpq', '')
                        ->groupEnd()
                        ->orderBy('created_at', 'DESC')
                        ->findAll();
        }

        return $this->where('IdTpq', $idTpq)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Generate unique HashKey
     */
    public function generateHashKey(): string
    {
        do {
            $hashKey = bin2hex(random_bytes(16));
        } while ($this->where('HashKey', $hashKey)->first());

        return $hashKey;
    }

    /**
     * Check ownership - apakah link ini milik IdTpq tertentu
     */
    public function isOwnedBy(int $linkId, $idTpq): bool
    {
        $link = $this->find($linkId);
        if (!$link) return false;

        $linkIdTpq = $link['IdTpq'];

        // FKPQ case
        if (empty($idTpq) || $idTpq == '0' || $idTpq == 0) {
            return empty($linkIdTpq) || $linkIdTpq == '0' || $linkIdTpq == 0;
        }

        return $linkIdTpq == $idTpq;
    }
}
