<?php

namespace App\Models\Backend\Penghargaan;

use CodeIgniter\Model;

/**
 * Model konfigurasi global modul penghargaan per lembaga (TPQ).
 */
class PenghargaanSettingModel extends Model
{
    protected $table          = 'tbl_penghargaan_setting';
    protected $primaryKey     = 'Id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'IdTpq',
        'BobotNilaiDefault',
        'BobotHadirDefault',
        'ThresholdHadirSempurna',
        'JumlahPemenangDefault',
        'FormatNomorSeri',
        'WilayahSertifikat',
        'MomenAktif',
        'IsGroupByJenjang',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Ambil setting lembaga; buat record default otomatis jika belum ada.
     */
    public function getOrCreateByTpq(string $idTpq): array
    {
        $setting = $this->where('IdTpq', $idTpq)->first();
        if ($setting) {
            return $setting;
        }

        $defaults = [
            'IdTpq'                  => $idTpq,
            'BobotNilaiDefault'      => 70,
            'BobotHadirDefault'      => 30,
            'ThresholdHadirSempurna' => 100,
            'JumlahPemenangDefault'  => 3,
            'FormatNomorSeri'        => 'TPQ/SNRT/{TAHUN}/{NO}',
            'MomenAktif'             => 'ganjil',
            'IsGroupByJenjang'       => 1,
        ];

        $this->insert($defaults);

        return $this->where('IdTpq', $idTpq)->first() ?? $defaults;
    }
}
