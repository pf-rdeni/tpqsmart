<?php

namespace App\Models\Frontend\Infografis;

use CodeIgniter\Model;

class InfografisConfigModel extends Model
{
    protected $table            = 'tbl_infografis_config';
    protected $primaryKey       = 'Id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'IdInfografisLink',
        'BlockKey',
        'IsActive',
        'SortOrder',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Default block cards dengan urutan
     */
    public const DEFAULT_BLOCKS = [
        ['BlockKey' => 'home',           'IsActive' => 1, 'SortOrder' => 1],
        ['BlockKey' => 'keadaan_santri', 'IsActive' => 1, 'SortOrder' => 2],
        ['BlockKey' => 'keadaan_guru',   'IsActive' => 1, 'SortOrder' => 3],
        ['BlockKey' => 'absensi_santri', 'IsActive' => 1, 'SortOrder' => 4],
        ['BlockKey' => 'absensi_guru',   'IsActive' => 1, 'SortOrder' => 5],
        ['BlockKey' => 'jadwal_sholat',  'IsActive' => 1, 'SortOrder' => 6],
        ['BlockKey' => 'galeri',         'IsActive' => 1, 'SortOrder' => 7],
        ['BlockKey' => 'agenda',         'IsActive' => 1, 'SortOrder' => 8],
    ];

    /**
     * Label nama untuk setiap block
     */
    public const BLOCK_LABELS = [
        'home'           => '🏠 Home - Ringkasan',
        'keadaan_santri' => '📊 Keadaan Santri',
        'keadaan_guru'   => '👨‍🏫 Keadaan Guru',
        'absensi_santri' => '📈 Absensi Santri',
        'absensi_guru'   => '📉 Absensi Guru',
        'jadwal_sholat'  => '🕌 Jadwal Sholat',
        'galeri'         => '🖼️ Galeri Kegiatan',
        'agenda'         => '📅 Agenda Mendatang',
    ];

    /**
     * Get active blocks untuk link tertentu, ordered by SortOrder
     */
    public function getActiveBlocks(int $idLink): array
    {
        return $this->where('IdInfografisLink', $idLink)
                    ->where('IsActive', 1)
                    ->orderBy('SortOrder', 'ASC')
                    ->findAll();
    }

    /**
     * Get all blocks untuk link tertentu (aktif & nonaktif)
     */
    public function getAllBlocks(int $idLink): array
    {
        return $this->where('IdInfografisLink', $idLink)
                    ->orderBy('SortOrder', 'ASC')
                    ->findAll();
    }

    /**
     * Inisialisasi default blocks untuk link baru
     */
    public function initDefaultBlocks(int $idLink): void
    {
        foreach (self::DEFAULT_BLOCKS as $block) {
            $this->insert([
                'IdInfografisLink' => $idLink,
                'BlockKey'         => $block['BlockKey'],
                'IsActive'         => $block['IsActive'],
                'SortOrder'        => $block['SortOrder'],
            ]);
        }
    }

    /**
     * Save block config (update IsActive dan SortOrder untuk semua blocks)
     * 
     * @param int $idLink
     * @param array $blocks Array of ['BlockKey' => string, 'IsActive' => int, 'SortOrder' => int]
     */
    public function saveBlockConfig(int $idLink, array $blocks): void
    {
        foreach ($blocks as $block) {
            $this->where('IdInfografisLink', $idLink)
                 ->where('BlockKey', $block['BlockKey'])
                 ->set([
                     'IsActive'  => $block['IsActive'] ?? 0,
                     'SortOrder' => $block['SortOrder'] ?? 0,
                 ])
                 ->update();
        }
    }
}
