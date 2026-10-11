<?php

namespace App\Models\Frontend\UjianMdta;

use CodeIgniter\Model;

class UjianMdtaNilaiKomponenModel extends Model
{
    protected $table            = 'tbl_ujian_mdta_nilai_komponen';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'IdTpq',
        'IdKelas',
        'IdSantri',
        'IdMateri',
        'IdTahunAjaran',
        'Semester',
        'IdJadwal',
        'IdSesi',
        'Kategori',
        'NamaKomponen',
        'Nilai',
        'Metode',
        'ModePenilaian',
        'Catatan',
        'CreatedBy',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `IdTpq` VARCHAR(50) NOT NULL,
                `IdKelas` VARCHAR(50) NOT NULL,
                `IdSantri` VARCHAR(50) NOT NULL,
                `IdMateri` VARCHAR(50) NOT NULL,
                `IdTahunAjaran` VARCHAR(20) NOT NULL,
                `Semester` VARCHAR(20) NOT NULL,
                `IdJadwal` INT NULL,
                `IdSesi` INT NULL,
                `Kategori` VARCHAR(20) NOT NULL DEFAULT 'harian',
                `NamaKomponen` VARCHAR(150) NOT NULL,
                `Nilai` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `Metode` VARCHAR(20) NOT NULL DEFAULT 'manual',
                `ModePenilaian` VARCHAR(30) NOT NULL DEFAULT 'langsung',
                `Catatan` VARCHAR(255) NULL,
                `CreatedBy` VARCHAR(50) NULL,
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL,
                INDEX idx_santri_materi (`IdSantri`, `IdMateri`, `Semester`, `IdTahunAjaran`),
                INDEX idx_kelas_tpq (`IdKelas`, `IdTpq`),
                INDEX idx_jadwal (`IdJadwal`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->db->query($sql);
        } catch (\Throwable $e) {
            log_message('error', 'Error creating table ' . $this->table . ': ' . $e->getMessage());
        }
    }

    /**
     * Ambil seluruh komponen nilai santri per kelas, materi, dan semester
     */
    public function getKomponenByKelasMateri(string $idTpq, string $idKelas, string $idMateri, string $idTahunAjaran, string $semester): array
    {
        return $this->where('IdTpq', $idTpq)
            ->where('IdKelas', $idKelas)
            ->where('IdMateri', $idMateri)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('Semester', $semester)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Simpan atau update nilai per santri pada sebuah komponen/jadwal tertentu
     */
    public function saveOrUpdateNilai(array $data): bool
    {
        $existing = null;
        if (!empty($data['IdJadwal'])) {
            $existing = $this->where('IdJadwal', $data['IdJadwal'])
                ->where('IdSantri', $data['IdSantri'])
                ->first();
        } else {
            $existing = $this->where('IdSantri', $data['IdSantri'])
                ->where('IdMateri', $data['IdMateri'])
                ->where('IdKelas', $data['IdKelas'])
                ->where('IdTahunAjaran', $data['IdTahunAjaran'])
                ->where('Semester', $data['Semester'])
                ->where('NamaKomponen', $data['NamaKomponen'])
                ->first();
        }

        if ($existing) {
            return $this->update($existing['id'], [
                'Nilai'         => $data['Nilai'],
                'Catatan'       => $data['Catatan'] ?? $existing['Catatan'],
                'CreatedBy'     => $data['CreatedBy'] ?? $existing['CreatedBy'],
                'ModePenilaian' => $data['ModePenilaian'] ?? $existing['ModePenilaian'],
                'updated_at'    => date('Y-m-d H:i:s')
            ]);
        }

        return (bool) $this->insert($data);
    }
}
