<?php

namespace App\Models;

use CodeIgniter\Model;

class RombelWalikelasModel extends Model
{
    protected $table = 'tbl_rombel';
    protected $primaryKey = 'Id';
    protected $allowedFields = [
        'IdSantri',
        'IdTahunAjaran',
        'IdGuru',
        'IdKelas',
        'IdTpq',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Ambil mapping wali kelas untuk santri tertentu
     */
    public function getMappingBySantri($IdSantri, $IdTahunAjaran, $IdKelas, $IdTpq)
    {
        return $this->where([
            'IdSantri' => $IdSantri,
            'IdTahunAjaran' => $IdTahunAjaran,
            'IdKelas' => $IdKelas,
            'IdTpq' => $IdTpq
        ])->first();
    }

    /**
     * Ambil semua mapping untuk kelas tertentu
     */
    public function getMappingByKelas($IdKelas, $IdTahunAjaran, $IdTpq)
    {
        return $this->where([
            'IdKelas' => $IdKelas,
            'IdTahunAjaran' => $IdTahunAjaran,
            'IdTpq' => $IdTpq
        ])->findAll();
    }

    /**
     * Simpan atau update mapping
     */
    public function saveMapping($data)
    {
        // Cek apakah sudah ada mapping
        $existing = $this->where([
            'IdSantri' => $data['IdSantri'],
            'IdTahunAjaran' => $data['IdTahunAjaran'],
            'IdKelas' => $data['IdKelas'],
            'IdTpq' => $data['IdTpq']
        ])->first();

        if ($existing) {
            // Update mapping yang sudah ada
            $result = $this->update($existing['Id'], $data);
            return $result ? $existing['Id'] : false;
        } else {
            // Insert mapping baru
            $insertId = $this->insert($data);
            return $insertId ? $insertId : false;
        }
    }

    /**
     * Hapus mapping untuk santri tertentu
     */
    public function deleteMapping($IdSantri, $IdTahunAjaran, $IdKelas, $IdTpq)
    {
        return $this->where([
            'IdSantri' => $IdSantri,
            'IdTahunAjaran' => $IdTahunAjaran,
            'IdKelas' => $IdKelas,
            'IdTpq' => $IdTpq
        ])->delete();
    }

    /**
     * Mass save mapping (insert/update/delete dalam satu batch)
     * 
     * @param array $dataArray Array of mapping data
     * @return array Result dengan success, message, saved, updated, deleted
     */
    public function saveMappingBatch($dataArray)
    {
        if (empty($dataArray) || !is_array($dataArray)) {
            return [
                'success' => false,
                'message' => 'Data tidak valid',
                'saved' => 0,
                'updated' => 0,
                'deleted' => 0,
                'errors' => []
            ];
        }

        $saved = 0;
        $updated = 0;
        $deleted = 0;
        $errors = [];

        // Mulai transaction
        $this->db->transStart();

        try {
            foreach ($dataArray as $data) {
                $IdSantri = $data['IdSantri'] ?? null;
                $IdTahunAjaran = $data['IdTahunAjaran'] ?? null;
                $IdKelas = $data['IdKelas'] ?? null;
                $IdTpq = $data['IdTpq'] ?? null;
                $IdGuru = $data['IdGuru'] ?? null;

                // Validasi data wajib
                if (empty($IdSantri) || empty($IdTahunAjaran) || empty($IdKelas) || empty($IdTpq)) {
                    $errors[] = "Data tidak lengkap untuk IdSantri: {$IdSantri}";
                    continue;
                }

                // Cek apakah sudah ada mapping
                $existing = $this->where([
                    'IdSantri' => $IdSantri,
                    'IdTahunAjaran' => $IdTahunAjaran,
                    'IdKelas' => $IdKelas,
                    'IdTpq' => $IdTpq
                ])->first();

                if (empty($IdGuru)) {
                    // Jika IdGuru null/kosong, hapus mapping yang ada
                    if ($existing) {
                        $deleteResult = $this->delete($existing['Id']);
                        if ($deleteResult) {
                            $deleted++;
                        } else {
                            $errors[] = "Gagal menghapus mapping untuk IdSantri: {$IdSantri}";
                        }
                    }
                } else {
                    // Jika IdGuru ada, insert atau update
                    $mappingData = [
                        'IdSantri' => $IdSantri,
                        'IdTahunAjaran' => $IdTahunAjaran,
                        'IdGuru' => $IdGuru,
                        'IdKelas' => $IdKelas,
                        'IdTpq' => $IdTpq
                    ];

                    if ($existing) {
                        // Update mapping yang sudah ada
                        $updateResult = $this->update($existing['Id'], $mappingData);
                        if ($updateResult) {
                            $updated++;
                        } else {
                            $errors[] = "Gagal update mapping untuk IdSantri: {$IdSantri}";
                        }
                    } else {
                        // Insert mapping baru
                        $insertId = $this->insert($mappingData);
                        if ($insertId) {
                            $saved++;
                        } else {
                            $modelErrors = $this->errors();
                            $errorMsg = !empty($modelErrors) ? implode(', ', $modelErrors) : 'Unknown error';
                            $errors[] = "Gagal insert mapping untuk IdSantri: {$IdSantri} - {$errorMsg}";
                        }
                    }
                }
            }

            // Commit transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return [
                    'success' => false,
                    'message' => 'Transaksi gagal. Beberapa data mungkin tidak tersimpan.',
                    'saved' => $saved,
                    'updated' => $updated,
                    'deleted' => $deleted,
                    'errors' => $errors
                ];
            }

            $total = $saved + $updated + $deleted;
            $message = "Berhasil menyimpan {$total} mapping";
            if ($saved > 0) $message .= " ({$saved} baru";
            if ($updated > 0) $message .= $saved > 0 ? ", {$updated} diupdate" : " ({$updated} diupdate";
            if ($deleted > 0) $message .= ($saved > 0 || $updated > 0 ? ", {$deleted} dihapus" : " ({$deleted} dihapus");
            $message .= ")";

            return [
                'success' => true,
                'message' => $message,
                'saved' => $saved,
                'updated' => $updated,
                'deleted' => $deleted,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->transRollback();
            
            log_message('error', 'RombelWalikelasModel: saveMappingBatch - Exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'saved' => $saved,
                'updated' => $updated,
                'deleted' => $deleted,
                'errors' => array_merge($errors, [$e->getMessage()])
            ];
        }
    }
}

