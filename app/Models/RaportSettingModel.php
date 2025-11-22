<?php

namespace App\Models;

use CodeIgniter\Model;

class RaportSettingModel extends Model
{
    protected $table            = 'tbl_raport_setting';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'IdSantri',
        'IdKelas',
        'IdTpq',
        'IdTahunAjaran',
        'Semester',
        'ShowAbsensi',
        'AbsensiData',
        'ShowCatatan',
        'CatatanData',
        'SettingData',
        'created_at',
        'updated_at'
    ];

    // Aktifkan timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'IdSantri' => 'required',
        'IdTpq' => 'required',
        'IdTahunAjaran' => 'required',
        'Semester' => 'required|in_list[Ganjil,Genap]'
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil data setting rapor untuk santri
     */
    public function getDataBySantri($IdSantri, $IdTahunAjaran, $semester)
    {
        $data = $this->where('IdSantri', $IdSantri)
            ->where('IdTahunAjaran', $IdTahunAjaran)
            ->where('Semester', $semester)
            ->first();

        if ($data) {
            // Decode JSON data
            if (!empty($data['AbsensiData'])) {
                $data['AbsensiData'] = json_decode($data['AbsensiData'], true) ?? [];
            } else {
                $data['AbsensiData'] = [];
            }

            if (!empty($data['CatatanData'])) {
                $data['CatatanData'] = json_decode($data['CatatanData'], true) ?? [];
            } else {
                $data['CatatanData'] = [];
            }

            if (!empty($data['SettingData'])) {
                $data['SettingData'] = json_decode($data['SettingData'], true) ?? [];
            } else {
                $data['SettingData'] = [];
            }
        }

        return $data;
    }

    /**
     * Ambil data setting rapor untuk banyak santri sekaligus (batch query)
     * @param array $santriIds Array of IdSantri
     * @param mixed $IdTahunAjaran
     * @param string $semester
     * @return array Mapping dengan key: IdSantri_Semester
     */
    public function getDataBySantriBatch($santriIds, $IdTahunAjaran, $semester)
    {
        if (empty($santriIds)) {
            return [];
        }

        $data = $this->whereIn('IdSantri', $santriIds)
            ->where('IdTahunAjaran', $IdTahunAjaran)
            ->where('Semester', $semester)
            ->findAll();

        $result = [];
        foreach ($data as $row) {
            $key = $row['IdSantri'] . '_' . $semester;
            
            // Decode JSON data
            if (!empty($row['AbsensiData'])) {
                $row['AbsensiData'] = json_decode($row['AbsensiData'], true) ?? [];
            } else {
                $row['AbsensiData'] = [];
            }

            if (!empty($row['CatatanData'])) {
                $row['CatatanData'] = json_decode($row['CatatanData'], true) ?? [];
            } else {
                $row['CatatanData'] = [];
            }

            if (!empty($row['SettingData'])) {
                $row['SettingData'] = json_decode($row['SettingData'], true) ?? [];
            } else {
                $row['SettingData'] = [];
            }

            $result[$key] = $row;
        }

        return $result;
    }

    /**
     * Ambil atau buat data setting rapor untuk santri
     */
    public function getOrCreateData($IdSantri, $IdKelas, $IdTpq, $IdTahunAjaran, $semester)
    {
        $data = $this->getDataBySantri($IdSantri, $IdTahunAjaran, $semester);
        
        if (!$data) {
            // Buat data baru (timestamps akan otomatis di-set oleh CodeIgniter)
            $newData = [
                'IdSantri' => $IdSantri,
                'IdKelas' => $IdKelas,
                'IdTpq' => $IdTpq,
                'IdTahunAjaran' => $IdTahunAjaran,
                'Semester' => $semester,
                'ShowAbsensi' => 0,
                'ShowCatatan' => 0,
                'AbsensiData' => json_encode([]),
                'CatatanData' => json_encode([]),
                'SettingData' => json_encode([])
            ];
            $this->insert($newData);
            return $this->getDataBySantri($IdSantri, $IdTahunAjaran, $semester);
        }
        
        return $data;
    }

    /**
     * Simpan data absensi (format JSON)
     */
    public function saveAbsensiData($IdSantri, $IdTahunAjaran, $semester, $absensiData)
    {
        $data = $this->getDataBySantri($IdSantri, $IdTahunAjaran, $semester);
        
        if (!$data) {
            return false;
        }

        // updated_at akan otomatis di-set oleh CodeIgniter
        return $this->update($data['id'], [
            'ShowAbsensi' => $absensiData['ShowAbsensi'] ?? 0,
            'AbsensiData' => json_encode([
                'jumlahTidakMasuk' => $absensiData['jumlahTidakMasuk'] ?? 0,
                'jumlahIzin' => $absensiData['jumlahIzin'] ?? 0,
                'jumlahAlfa' => $absensiData['jumlahAlfa'] ?? 0,
                'jumlahSakit' => $absensiData['jumlahSakit'] ?? 0,
                'alasanIzin' => $absensiData['alasanIzin'] ?? ''
            ])
        ]);
    }

    /**
     * Simpan data catatan (format JSON)
     */
    public function saveCatatanData($IdSantri, $IdTahunAjaran, $semester, $catatanData)
    {
        $data = $this->getDataBySantri($IdSantri, $IdTahunAjaran, $semester);
        
        if (!$data) {
            return false;
        }

        $catatanDefault = $catatanData['catatanDefault'] ?? '';
        $catatanKhusus = $catatanData['catatanKhusus'] ?? '';
        $selectedCatatanId = $catatanData['selectedCatatanId'] ?? null;
        
        // Gabungkan catatan default dan khusus
        $catatanFinal = trim($catatanDefault);
        if (!empty($catatanKhusus)) {
            $catatanFinal .= (!empty($catatanFinal) ? "\n\n" : '') . trim($catatanKhusus);
        }

        // updated_at akan otomatis di-set oleh CodeIgniter
        return $this->update($data['id'], [
            'ShowCatatan' => $catatanData['ShowCatatan'] ?? 0,
            'CatatanData' => json_encode([
                'catatanDefault' => $catatanDefault,
                'catatanKhusus' => $catatanKhusus,
                'catatanFinal' => $catatanFinal,
                'selectedCatatanId' => $selectedCatatanId
            ])
        ]);
    }

    /**
     * Ambil setting tambahan dari JSON SettingData
     */
    public function getSettingData($IdSantri, $IdTahunAjaran, $semester, $key = null)
    {
        $data = $this->getDataBySantri($IdSantri, $IdTahunAjaran, $semester);
        
        if (!$data || empty($data['SettingData'])) {
            return null;
        }
        
        $settingData = $data['SettingData'];
        
        if ($key === null) {
            return $settingData;
        }
        
        return $settingData[$key] ?? null;
    }

    /**
     * Simpan setting tambahan ke JSON SettingData
     */
    public function saveSettingData($IdSantri, $IdTahunAjaran, $semester, $key, $value)
    {
        $data = $this->getDataBySantri($IdSantri, $IdTahunAjaran, $semester);
        
        if (!$data) {
            return false;
        }
        
        $settingData = $data['SettingData'] ?? [];
        $settingData[$key] = $value;
        
        // updated_at akan otomatis di-set oleh CodeIgniter
        return $this->update($data['id'], [
            'SettingData' => json_encode($settingData)
        ]);
    }
}

