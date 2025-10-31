<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahKonfigurasiModel extends Model
{
    protected $table = 'tbl_munaqosah_konfigurasi';
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
     * Get all configuration data
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->orderBy('IdTpq', 'ASC')
                    ->orderBy('SettingKey', 'ASC')
                    ->findAll();
    }

    /**
     * Get configuration by IdTpq
     * If IdTpq exists, return default template + IdTpq data
     * If IdTpq = 0 or null, return all
     * 
     * Order: 'default' first, then '0', then others
     * 
     * @param string|null $idTpq
     * @return array
     */
    public function getByTpq($idTpq = null)
    {
        // If IdTpq is 0 or null (admin), return all
        if (empty($idTpq) || $idTpq == 0 || $idTpq == '0') {
            return $this->orderBy("CASE 
                    WHEN IdTpq = 'default' THEN 0 
                    WHEN IdTpq = '0' THEN 1 
                    ELSE 2 
                END", 'ASC', false)
                        ->orderBy('IdTpq', 'ASC')
                        ->orderBy('SettingKey', 'ASC')
                        ->findAll();
        }

        // Get default template + specific IdTpq data
        return $this->whereIn('IdTpq', ['default', $idTpq])
                    ->orderBy("CASE 
                    WHEN IdTpq = 'default' THEN 0 
                    ELSE 1 
                END", 'ASC', false)
                    ->orderBy('SettingKey', 'ASC')
                    ->findAll();
    }

    /**
     * Get setting value by IdTpq and SettingKey
     * If not found for specific IdTpq, fallback to default
     * 
     * @param string $idTpq
     * @param string $settingKey
     * @return mixed
     */
    public function getSetting(string $idTpq, string $settingKey)
    {
        // Try to get setting for specific IdTpq
        $setting = $this->where(['IdTpq' => $idTpq, 'SettingKey' => $settingKey])->first();

        // If not found, try to get default setting
        if (empty($setting)) {
            $setting = $this->where(['IdTpq' => 'default', 'SettingKey' => $settingKey])->first();
        }

        // Return setting value with proper type conversion or null if not found
        if ($setting) {
            return $this->convertSettingValue($setting['SettingValue'], $setting['SettingType']);
        }
        
        return null;
    }

    /**
     * Convert SettingValue based on SettingType
     * 
     * @param string $value Raw setting value from database
     * @param string $type Setting type (text, number, boolean, json)
     * @return mixed Converted value with proper data type
     */
    private function convertSettingValue($value, $type)
    {
        switch (strtolower($type)) {
            case 'number':
                // Convert to integer or float
                return is_numeric($value) ? (int)$value : 0;
                
            case 'boolean':
                // Convert to boolean
                if (is_bool($value)) {
                    return $value;
                }
                
                $lowerValue = strtolower(trim($value));
                return in_array($lowerValue, ['true', '1', 'yes', 'on', 'enabled', 'active']);
                
            case 'json':
                // Decode JSON string
                $decoded = json_decode($value, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
                
            case 'text':
            default:
                // Return as string
                return (string)$value;
        }
    }

    /**
     * Get setting as integer
     * 
     * @param string $idTpq ID TPQ spesifik atau 'default'
     * @param string $settingKey Kunci pengaturan
     * @param int $default Default value if not found
     * @return int Setting value as integer
     */
    public function getSettingAsInt(string $idTpq, string $settingKey, int $default = 0): int
    {
        $value = $this->getSetting($idTpq, $settingKey);
        return $value !== null ? (int)$value : $default;
    }
}

