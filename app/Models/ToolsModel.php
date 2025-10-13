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
     * Menangani konversi type data berdasarkan SettingType.
     *
     * @param string $idTpq ID TPQ spesifik atau 'default'.
     * @param string $settingKey Kunci pengaturan (e.g., 'Min', 'Max').
     * @return mixed Nilai pengaturan dengan type data yang sesuai atau null jika tidak ditemukan.
     */
    public function getSetting(string $idTpq, string $settingKey)
    {
        // Coba ambil pengaturan untuk IdTpq spesifik
        $setting = $this->where(['IdTpq' => $idTpq, 'SettingKey' => $settingKey])->first();

        // Jika tidak ditemukan, coba ambil pengaturan default
        if (empty($setting)) {
            $setting = $this->where(['IdTpq' => 'default', 'SettingKey' => $settingKey])->first();
        }

        // Kembalikan nilai pengaturan dengan type data yang sesuai atau null jika tidak ada
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
     * Get setting as specific data type
     * 
     * @param string $idTpq ID TPQ spesifik atau 'default'
     * @param string $settingKey Kunci pengaturan
     * @param string $expectedType Expected data type (number, boolean, json, text)
     * @return mixed Converted value with expected type or null if not found
     */
    public function getSettingAsType(string $idTpq, string $settingKey, string $expectedType = 'text')
    {
        $setting = $this->getSetting($idTpq, $settingKey);
        
        if ($setting === null) {
            return null;
        }
        
        // Force convert to expected type
        return $this->convertSettingValue($setting, $expectedType);
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
        $value = $this->getSettingAsType($idTpq, $settingKey, 'number');
        return $value !== null ? (int)$value : $default;
    }

    /**
     * Get setting as boolean
     * 
     * @param string $idTpq ID TPQ spesifik atau 'default'
     * @param string $settingKey Kunci pengaturan
     * @param bool $default Default value if not found
     * @return bool Setting value as boolean
     */
    public function getSettingAsBool(string $idTpq, string $settingKey, bool $default = false): bool
    {
        $value = $this->getSettingAsType($idTpq, $settingKey, 'boolean');
        return $value !== null ? (bool)$value : $default;
    }

    /**
     * Get setting as JSON array
     * 
     * @param string $idTpq ID TPQ spesifik atau 'default'
     * @param string $settingKey Kunci pengaturan
     * @param array $default Default value if not found
     * @return array Setting value as array
     */
    public function getSettingAsArray(string $idTpq, string $settingKey, array $default = []): array
    {
        $value = $this->getSettingAsType($idTpq, $settingKey, 'json');
        return is_array($value) ? $value : $default;
    }

    /**
     * Get setting as string
     * 
     * @param string $idTpq ID TPQ spesifik atau 'default'
     * @param string $settingKey Kunci pengaturan
     * @param string $default Default value if not found
     * @return string Setting value as string
     */
    public function getSettingAsString(string $idTpq, string $settingKey, string $default = ''): string
    {
        $value = $this->getSettingAsType($idTpq, $settingKey, 'text');
        return $value !== null ? (string)$value : $default;
    }
}
