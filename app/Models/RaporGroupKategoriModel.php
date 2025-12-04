<?php

namespace App\Models;

use CodeIgniter\Model;

class RaporGroupKategoriModel extends Model
{
    protected $table = 'tbl_rapor_group_kategori';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'IdTpq',
        'KategoriAsal',
        'NamaMateriBaru',
        'Status',
        'Urutan',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'IdTpq' => 'required',
        'KategoriAsal' => 'required|max_length[255]',
        'NamaMateriBaru' => 'required|max_length[255]',
        'Status' => 'required|in_list[Aktif,Tidak Aktif]',
        'Urutan' => 'permit_empty|integer'
    ];
    
    protected $validationMessages = [
        'KategoriAsal' => [
            'required' => 'Kategori Asal harus diisi',
            'max_length' => 'Kategori Asal maksimal 255 karakter'
        ],
        'NamaMateriBaru' => [
            'required' => 'Nama Materi Baru harus diisi',
            'max_length' => 'Nama Materi Baru maksimal 255 karakter'
        ],
        'Status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status harus Aktif atau Tidak Aktif'
        ]
    ];
    
    /**
     * Get active grouping config by IdTpq
     * Falls back to 'default' if not found
     * 
     * @param string $IdTpq ID TPQ spesifik atau 'default'
     * @return array Array of active grouping configurations
     */
    public function getActiveByTpq($IdTpq)
    {
        // Coba ambil untuk IdTpq spesifik
        $configs = $this->where(['IdTpq' => $IdTpq, 'Status' => 'Aktif'])
            ->orderBy('Urutan', 'ASC')
            ->orderBy('KategoriAsal', 'ASC')
            ->findAll();
        
        // Jika tidak ada, ambil dari default
        if (empty($configs) && $IdTpq !== 'default') {
            $configs = $this->where(['IdTpq' => 'default', 'Status' => 'Aktif'])
                ->orderBy('Urutan', 'ASC')
                ->orderBy('KategoriAsal', 'ASC')
                ->findAll();
        }
        
        return $configs;
    }
    
    /**
     * Get all configurations by IdTpq (including inactive)
     * Falls back to 'default' if not found
     * 
     * @param string $IdTpq ID TPQ spesifik atau 'default'
     * @return array Array of all grouping configurations
     */
    public function getByTpq($IdTpq = null)
    {
        // If IdTpq is 0 or null (admin), return all
        if (empty($IdTpq) || $IdTpq == 0 || $IdTpq == '0') {
            return $this->orderBy("CASE 
                    WHEN IdTpq = 'default' THEN 0 
                    WHEN IdTpq = '0' THEN 1 
                    ELSE 2 
                END", 'ASC', false)
                ->orderBy('IdTpq', 'ASC')
                ->orderBy('Urutan', 'ASC')
                ->orderBy('KategoriAsal', 'ASC')
                ->findAll();
        }

        // Get default template + specific IdTpq data
        return $this->whereIn('IdTpq', ['default', $IdTpq])
            ->orderBy("CASE 
                    WHEN IdTpq = 'default' THEN 0 
                    ELSE 1 
                END", 'ASC', false)
            ->orderBy('Urutan', 'ASC')
            ->orderBy('KategoriAsal', 'ASC')
            ->findAll();
    }
}

