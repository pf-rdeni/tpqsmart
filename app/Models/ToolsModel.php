<?php

namespace App\Models;

use CodeIgniter\Model;

class ToolsModel extends Model
{
    protected $table = 'tbl_tools';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'IdTpq',
        'SettingKey',
        'SettingValue',
        'SettingType',
        'Description',
        'CreatedAt',
        'UpdatedAt'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'CreatedAt';
    protected $updatedField = 'UpdatedAt';

    protected $validationRules = [
        'IdTpq' => 'required',
        'SettingKey' => 'required|min_length[3]',
        'SettingValue' => 'required',
        'SettingType' => 'required',
        'Description' => 'permit_empty'
    ];

    /**
     * Mengambil nilai pengaturan berdasarkan IdTpq dan SettingKey.
     * Jika tidak ditemukan pada IdTpq spesifik, akan mencari pada IdTpq 'default'.
     *
     * @param string $idTpq ID TPQ spesifik atau 'default'.
     * @param string $settingKey Kunci pengaturan (e.g., 'Min', 'Max').
     * @return mixed Nilai pengaturan atau null jika tidak ditemukan.
     */
    public function getSetting(string $idTpq, string $settingKey)
    {
        // Coba ambil pengaturan untuk IdTpq spesifik
        $setting = $this->where(['IdTpq' => $idTpq, 'SettingKey' => $settingKey])->first();

        // Jika tidak ditemukan, coba ambil pengaturan default
        if (empty($setting)) {
            $setting = $this->where(['IdTpq' => 'default', 'SettingKey' => $settingKey])->first();
        }

        // Kembalikan nilai pengaturan atau null jika tidak ada
        return $setting ? $setting['SettingValue'] : null;
    }
}
