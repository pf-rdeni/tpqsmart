<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratTemplateModel extends Model
{
    protected $table            = 'tbl_surat_template';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'IdTpq',
        'ShareAllTpq',
        'IdKategori',
        'NamaTemplate',
        'Deskripsi',
        'UkuranKertas',
        'Orientasi',
        'MarginAtas',
        'MarginBawah',
        'MarginKiri',
        'MarginKanan',
        'FontFamily',
        'FontSize',
        'SumberKop',
        'IdKopCustom',
        'ShowKopLembaga',
        'ShowGarisPemisah',
        'ShowNomorSurat',
        'ShowLampiran',
        'ShowHal',
        'ShowTujuan',
        'ShowSalam',
        'ShowIsi',
        'ShowPenutup',
        'ShowTempatTanggal',
        'ShowMengetahui',
        'ShowQrCode',
        'SalamPembuka',
        'MukadimahIsi',
        'IsiSurat',
        'Penutup',
        'SalamPenutup',
        'FormatNomor',
        'IsDefault',
        'IsActive',
        'CreatedBy',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    public function getByTpq($idTpq = null): array
    {
        $builder = $this->select('tbl_surat_template.*, k.NamaKategori, k.Kode as KodeKategori, k.Warna as WarnaKategori, kop.NamaKop as NamaKopCustom, tpq.NamaTpq')
                        ->join('tbl_surat_kategori k', 'k.Id = tbl_surat_template.IdKategori', 'left')
                        ->join('tbl_surat_kop_custom kop', 'kop.Id = tbl_surat_template.IdKopCustom', 'left')
                        ->join('tbl_tpq tpq', 'tpq.IdTpq = tbl_surat_template.IdTpq', 'left');

        if ($idTpq !== null && $idTpq !== '' && (int)$idTpq !== 0) {
            $builder->groupStart()
                    ->where('tbl_surat_template.IdTpq', (int)$idTpq)
                    ->orGroupStart()
                        ->where('tbl_surat_template.IdTpq', 0)
                        ->where('tbl_surat_template.ShareAllTpq', 1)
                    ->groupEnd()
                    ->groupEnd();
        }

        return $builder->orderBy('tbl_surat_template.Id', 'DESC')->findAll();
    }

    public function getActiveByTpq($idTpq = null): array
    {
        $builder = $this->where('tbl_surat_template.IsActive', 1)
                        ->select('tbl_surat_template.*, k.NamaKategori, k.Kode as KodeKategori, tpq.NamaTpq')
                        ->join('tbl_surat_kategori k', 'k.Id = tbl_surat_template.IdKategori', 'left')
                        ->join('tbl_tpq tpq', 'tpq.IdTpq = tbl_surat_template.IdTpq', 'left');

        if ($idTpq !== null && $idTpq !== '' && (int)$idTpq !== 0) {
            $builder->groupStart()
                    ->where('tbl_surat_template.IdTpq', (int)$idTpq)
                    ->orGroupStart()
                        ->where('tbl_surat_template.IdTpq', 0)
                        ->where('tbl_surat_template.ShareAllTpq', 1)
                        ->where('tbl_surat_template.IsActive', 1)
                    ->groupEnd()
                    ->groupEnd();
        }

        return $builder->orderBy('tbl_surat_template.NamaTemplate', 'ASC')->findAll();
    }

    public function setDefault(int $id, int $idTpq, int $idKategori): bool
    {
        $this->db->transStart();
        // Reset default template lain untuk TPQ dan kategori yang sama
        $this->where('IdTpq', $idTpq)
             ->where('IdKategori', $idKategori)
             ->set(['IsDefault' => 0])
             ->update();

        // Set template terpilih jadi default
        $this->update($id, ['IsDefault' => 1]);
        $this->db->transComplete();

        return $this->db->transStatus();
    }

    public function toggleAktif(int $id): bool
    {
        $data = $this->find($id);
        if (!$data) {
            return false;
        }
        return $this->update($id, ['IsActive' => $data['IsActive'] ? 0 : 1]);
    }
}
