<?php

namespace App\Models\Frontend\UjianMdta;

use CodeIgniter\Model;

class UjianMdtaBobotRaportModel extends Model
{
    protected $table            = 'tbl_ujian_mdta_bobot_raport';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'IdTpq',
        'IdKelas',
        'BobotHarian',
        'BobotUTS',
        'BobotUAS',
        'RumusPerhitungan',
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
                `IdKelas` VARCHAR(50) NULL,
                `BobotHarian` INT NOT NULL DEFAULT 40,
                `BobotUTS` INT NOT NULL DEFAULT 30,
                `BobotUAS` INT NOT NULL DEFAULT 30,
                `RumusPerhitungan` VARCHAR(50) DEFAULT 'standar',
                `created_at` DATETIME NULL,
                `updated_at` DATETIME NULL,
                INDEX idx_tpq_kelas (`IdTpq`, `IdKelas`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->db->query($sql);
        } catch (\Throwable $e) {
            log_message('error', 'Error creating table ' . $this->table . ': ' . $e->getMessage());
        }
    }

    /**
     * Dapatkan konfigurasi bobot aktif untuk TPQ dan Kelas tertentu.
     * Menggunakan setting spesifik kelas jika ada, atau fallback ke setting TPQ, atau default 40:30:30.
     */
    public function getBobotAktif(string $idTpq, ?string $idKelas = null): array
    {
        // Cek setting spesifik kelas jika $idKelas diberikan
        if (!empty($idKelas)) {
            $settingKelas = $this->where('IdTpq', $idTpq)->where('IdKelas', $idKelas)->first();
            if ($settingKelas) {
                return $settingKelas;
            }
        }

        // Cek setting umum level TPQ (IdKelas NULL atau kosong)
        $settingTpq = $this->where('IdTpq', $idTpq)
            ->groupStart()
                ->where('IdKelas', null)
                ->orWhere('IdKelas', '')
            ->groupEnd()
            ->first();

        if ($settingTpq) {
            return $settingTpq;
        }

        // Nilai Default Standar
        return [
            'IdTpq'            => $idTpq,
            'IdKelas'          => $idKelas,
            'BobotHarian'      => 40,
            'BobotUTS'         => 30,
            'BobotUAS'         => 30,
            'RumusPerhitungan' => 'standar'
        ];
    }
}
