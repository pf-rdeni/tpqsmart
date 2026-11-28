<?php

namespace App\Models\Backend\Penghargaan;

use CodeIgniter\Model;

/**
 * Model template desain sertifikat berbasis background image.
 */
class PenghargaanTemplateModel extends Model
{
    protected $table          = 'tbl_penghargaan_template';
    protected $primaryKey     = 'Id';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'IdTpq',
        'IdJenisPenghargaan',
        'NamaTemplate',
        'BackgroundFile',
        'UkuranKertas',
        'Orientasi',
        'IsDefault',
        'AllowDuplicate',
        'Status',
        'CreatedBy',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    /**
     * Ambil daftar template yang tersedia untuk cetak (milik TPQ sendiri + Admin).
     */
    public function getAvailableByTpq(?string $idTpq = null, ?int $idJenis = null, ?string $status = 'aktif'): array
    {
        $builder = $this->builder();
        $builder->groupStart()
            ->where('IdTpq IS NULL')
            ->orWhere('IdTpq', '0');

        if ($idTpq !== null && $idTpq !== '' && $idTpq !== '0') {
            $builder->orWhere('IdTpq', $idTpq);
        }
        $builder->groupEnd();

        if ($idJenis !== null) {
            $builder->groupStart()
                ->where('IdJenisPenghargaan IS NULL')
                ->orWhere('IdJenisPenghargaan', $idJenis)
                ->groupEnd();
        }

        if ($status !== null) {
            $builder->where('Status', $status);
        }

        return $builder->orderBy('CASE WHEN IdJenisPenghargaan IS NOT NULL THEN 0 ELSE 1 END', 'ASC', false)
            ->orderBy("CASE WHEN IdTpq IS NOT NULL AND IdTpq != '0' THEN 0 ELSE 1 END", 'ASC', false)
            ->orderBy('IsDefault', 'DESC')
            ->orderBy('Id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil daftar template untuk halaman manajemen (Admin vs Operator).
     * - Admin: semua template umum (IdTpq IS NULL / '0').
     * - Operator: template milik TPQ sendiri + template Admin aktif yang diizinkan duplikasi (AllowDuplicate = 1).
     */
    public function getForManagement(string $idTpq, bool $isAdmin): array
    {
        $builder = $this->builder();

        if ($isAdmin) {
            $builder->groupStart()
                ->where('IdTpq IS NULL')
                ->orWhere('IdTpq', '0')
                ->groupEnd();
        } else {
            $builder->groupStart()
                ->where('IdTpq', $idTpq)
                ->orGroupStart()
                    ->groupStart()
                        ->where('IdTpq IS NULL')
                        ->orWhere('IdTpq', '0')
                    ->groupEnd()
                    ->where('Status', 'aktif')
                    ->where('AllowDuplicate', 1)
                ->groupEnd()
                ->groupEnd();
        }

        return $builder->orderBy('CASE WHEN IdJenisPenghargaan IS NOT NULL THEN 0 ELSE 1 END', 'ASC', false)
            ->orderBy("CASE WHEN IdTpq IS NOT NULL AND IdTpq != '0' THEN 0 ELSE 1 END", 'ASC', false)
            ->orderBy('IsDefault', 'DESC')
            ->orderBy('Id', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Tandai 1 template sebagai DIPILIH (IsDefault = 1) secara eksklusif pada grupnya
     * dalam lingkup pemiliknya (Admin = '0'/NULL, Operator = $idTpq sendiri).
     */
    public function setSelectedTemplate(int $idTemplate, string $idTpq, bool $isAdmin = false): bool
    {
        $target = $this->find($idTemplate);
        if (!$target) {
            return false;
        }

        $idJenis = $target['IdJenisPenghargaan'] ?? null;

        $resetBuilder = $this->builder();
        if ($isAdmin) {
            $resetBuilder->groupStart()
                ->where('IdTpq IS NULL')
                ->orWhere('IdTpq', '0')
                ->groupEnd();
        } else {
            $resetBuilder->where('IdTpq', $idTpq);
        }

        if ($idJenis !== null && $idJenis !== '') {
            $resetBuilder->where('IdJenisPenghargaan', (int) $idJenis);
        } else {
            $resetBuilder->where('IdJenisPenghargaan IS NULL');
        }

        $resetBuilder->update(['IsDefault' => 0]);

        return $this->update($idTemplate, [
            'IsDefault' => 1,
            'Status'    => 'aktif',
        ]);
    }
}
