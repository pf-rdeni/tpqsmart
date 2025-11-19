<?php

namespace App\Models;

use CodeIgniter\Model;

class SertifikasiGuruModel extends Model
{
    protected $table = 'tbl_sertifikasi_guru';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'NoPeserta',
        'NoRek',
        'Nama',
        'NamaTpq',
        'JenisKelamin',
        'Kecamatan',
        'Note',
    ];

    protected $validationRules = [
        'NoPeserta' => 'required|max_length[50]',
        'NoRek' => 'permit_empty|max_length[50]',
        'Nama' => 'required|max_length[255]',
        'NamaTpq' => 'permit_empty|max_length[255]',
        'JenisKelamin' => 'permit_empty|max_length[20]',
        'Kecamatan' => 'permit_empty|max_length[255]',
        'Note' => 'permit_empty',
    ];

    protected $validationMessages = [
        'NoPeserta' => [
            'required' => 'Nomor peserta harus diisi',
            'max_length' => 'Nomor peserta maksimal 50 karakter'
        ],
        'Nama' => [
            'required' => 'Nama harus diisi',
            'max_length' => 'Nama maksimal 255 karakter'
        ],
        'NoRek' => [
            'max_length' => 'No Rek maksimal 50 karakter'
        ],
        'NamaTpq' => [
            'max_length' => 'Nama TPQ maksimal 255 karakter'
        ],
        'JenisKelamin' => [
            'max_length' => 'Jenis Kelamin maksimal 20 karakter'
        ],
        'Kecamatan' => [
            'max_length' => 'Kecamatan maksimal 255 karakter'
        ],
    ];

    /**
     * Get guru by NoPeserta
     */
    public function getGuruByNoPeserta($noPeserta)
    {
        return $this->where('NoPeserta', $noPeserta)->first();
    }

    /**
     * Get guru by noTest (backward compatibility)
     */
    public function getGuruByNoTest($noTest)
    {
        return $this->where('NoPeserta', $noTest)->first();
    }

    /**
     * Get all guru
     */
    public function getAllGuru()
    {
        return $this->orderBy('Nama', 'ASC')->findAll();
    }

    /**
     * Check if NoPeserta already exists
     * 
     * @param string $noPeserta
     * @param int|null $excludeId Exclude this ID from check (for update)
     * @return bool
     */
    public function isNoPesertaExists($noPeserta, $excludeId = null)
    {
        $builder = $this->where('NoPeserta', $noPeserta);
        
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->first() !== null;
    }

    /**
     * Insert new peserta sertifikasi
     * 
     * @param array $data
     * @return int|false Returns inserted ID on success, false on failure
     */
    public function insertPeserta($data)
    {
        // Validate required fields
        if (empty($data['NoPeserta']) || empty($data['Nama'])) {
            return false;
        }

        // Check for duplicate NoPeserta
        if ($this->isNoPesertaExists($data['NoPeserta'])) {
            return false;
        }

        // Clean empty strings to null
        $data = $this->cleanEmptyStrings($data);

        // Insert data
        if ($this->save($data)) {
            return $this->getInsertID();
        }

        return false;
    }

    /**
     * Update peserta sertifikasi
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updatePeserta($id, $data)
    {
        // Check if record exists
        $existing = $this->find($id);
        if (!$existing) {
            return false;
        }

        // Check for duplicate NoPeserta if NoPeserta is being changed
        if (isset($data['NoPeserta']) && $data['NoPeserta'] !== $existing['NoPeserta']) {
            if ($this->isNoPesertaExists($data['NoPeserta'], $id)) {
                return false;
            }
        }

        // Clean empty strings to null
        $data = $this->cleanEmptyStrings($data);

        // Update data
        return $this->update($id, $data);
    }

    /**
     * Clean empty strings and convert to null
     * 
     * @param array $data
     * @return array
     */
    private function cleanEmptyStrings($data)
    {
        $optionalFields = ['NoRek', 'NamaTpq', 'JenisKelamin', 'Kecamatan', 'Note'];
        
        foreach ($optionalFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }

    /**
     * Get peserta with pagination
     * 
     * @param int $perPage
     * @param int $page
     * @return array
     */
    public function getPesertaPaginated($perPage = 25, $page = 1)
    {
        return $this->orderBy('Nama', 'ASC')
                    ->paginate($perPage, 'default', $page);
    }

    /**
     * Search peserta by keyword
     * 
     * @param string $keyword
     * @return array
     */
    public function searchPeserta($keyword)
    {
        return $this->groupStart()
                    ->like('NoPeserta', $keyword)
                    ->orLike('Nama', $keyword)
                    ->orLike('NamaTpq', $keyword)
                    ->orLike('Kecamatan', $keyword)
                    ->groupEnd()
                    ->orderBy('Nama', 'ASC')
                    ->findAll();
    }

    /**
     * Generate next NoPeserta
     * Mencari nomor terakhir yang ada, kemudian increment
     * Jika tidak ada data, mulai dari 100
     * 
     * @param int $startFrom Nomor awal (default: 100)
     * @param int $maxRange Nomor maksimal (default: 999)
     * @return string|false Returns next NoPeserta or false if max range reached
     */
    public function generateNextNoPeserta($startFrom = 100, $maxRange = 999)
    {
        // Ambil semua NoPeserta yang ada, convert ke integer dan sort
        $existingNoPeserta = $this->select('NoPeserta')
                                   ->findAll();
        
        $noPesertaNumbers = [];
        foreach ($existingNoPeserta as $peserta) {
            $noPeserta = $peserta['NoPeserta'];
            // Coba convert ke integer jika memungkinkan
            if (is_numeric($noPeserta)) {
                $noPesertaInt = (int)$noPeserta;
                // Hanya ambil yang dalam range
                if ($noPesertaInt >= $startFrom && $noPesertaInt <= $maxRange) {
                    $noPesertaNumbers[] = $noPesertaInt;
                }
            }
        }

        // Jika ada nomor yang sudah ada, cari yang terbesar dan increment
        if (!empty($noPesertaNumbers)) {
            $maxNoPeserta = max($noPesertaNumbers);
            $nextNoPeserta = $maxNoPeserta + 1;
            
            // Pastikan tidak melebihi max range
            if ($nextNoPeserta > $maxRange) {
                // Cari nomor yang kosong di antara startFrom dan maxRange
                $nextNoPeserta = $this->findAvailableNoPeserta($startFrom, $maxRange, $noPesertaNumbers);
                
                if ($nextNoPeserta === false) {
                    return false; // Semua nomor sudah terpakai
                }
            }
        } else {
            // Jika tidak ada data, mulai dari startFrom
            $nextNoPeserta = $startFrom;
        }

        // Pastikan nomor belum ada (double check)
        $attempts = 0;
        $maxAttempts = ($maxRange - $startFrom) + 1; // Maksimal attempts sesuai range
        while ($this->isNoPesertaExists((string)$nextNoPeserta) && $attempts < $maxAttempts) {
            $nextNoPeserta++;
            if ($nextNoPeserta > $maxRange) {
                // Cari nomor yang kosong dari awal
                $nextNoPeserta = $this->findAvailableNoPeserta($startFrom, $maxRange, $noPesertaNumbers);
                if ($nextNoPeserta === false) {
                    return false;
                }
                break; // Jika sudah ketemu yang kosong, keluar dari loop
            }
            $attempts++;
        }

        if ($attempts >= $maxAttempts) {
            return false;
        }

        // Final check: pastikan nomor belum ada
        if ($this->isNoPesertaExists((string)$nextNoPeserta)) {
            // Jika masih ada, cari yang benar-benar kosong
            $nextNoPeserta = $this->findAvailableNoPeserta($startFrom, $maxRange, $noPesertaNumbers);
            if ($nextNoPeserta === false) {
                return false;
            }
        }

        return (string)$nextNoPeserta;
    }

    /**
     * Find available NoPeserta in range
     * 
     * @param int $startFrom
     * @param int $maxRange
     * @param array $usedNumbers
     * @return int|false
     */
    private function findAvailableNoPeserta($startFrom, $maxRange, $usedNumbers)
    {
        for ($i = $startFrom; $i <= $maxRange; $i++) {
            if (!in_array($i, $usedNumbers)) {
                // Double check di database
                if (!$this->isNoPesertaExists((string)$i)) {
                    return $i;
                }
            }
        }
        return false;
    }

    /**
     * Get max NoPeserta (numeric)
     * 
     * @return int
     */
    public function getMaxNoPeserta()
    {
        $result = $this->select('NoPeserta')
                       ->orderBy('CAST(NoPeserta AS UNSIGNED)', 'DESC')
                       ->first();
        
        if ($result && is_numeric($result['NoPeserta'])) {
            return (int)$result['NoPeserta'];
        }
        
        return 0;
    }
}

