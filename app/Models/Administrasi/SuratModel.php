<?php

namespace App\Models\Administrasi;

use CodeIgniter\Model;

class SuratModel extends Model
{
    protected $table            = 'tbl_surat';
    protected $primaryKey       = 'Id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'IdTpq',
        'IdTemplate',
        'IdKategori',
        'JenisSurat',
        'NomorSurat',
        'UrutanNomor',
        'Tahun',
        'Bulan',
        'Tanggal',
        'Hal',
        'Lampiran',
        'IdTujuanMaster',
        'TujuanNama',
        'TujuanJabatan',
        'TujuanInstansi',
        'TujuanAlamat',
        'TujuanKota',
        'SalamPembuka',
        'MukadimahIsi',
        'IsiSurat',
        'Penutup',
        'SalamPenutup',
        'TempatTtd',
        'TanggalTtd',
        'IdPenandaTangan1',
        'Jabatan1',
        'ModeTtd1',
        'IdPenandaTangan2',
        'Jabatan2',
        'ModeTtd2',
        'PenandaTanganConfig',
        'AsalSurat',
        'TanggalTerima',
        'NoAgenda',
        'Status',
        'Catatan',
        'CreatedBy',
        'UpdatedBy',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'CreatedAt';
    protected $updatedField  = 'UpdatedAt';

    public function getByTpq($idTpq = null, $jenis = null, $limit = null): array
    {
        $builder = $this->select('tbl_surat.*, k.NamaKategori, k.Kode as KodeKategori, k.Warna as WarnaKategori, t.NamaTpq')
                        ->join('tbl_surat_kategori k', 'k.Id = tbl_surat.IdKategori', 'left')
                        ->join('tbl_tpq t', 't.IdTpq = tbl_surat.IdTpq', 'left');

        if (!empty($idTpq)) {
            $builder->where('tbl_surat.IdTpq', $idTpq);
        }

        if (!empty($jenis)) {
            $builder->where('tbl_surat.JenisSurat', $jenis);
        }

        $builder->orderBy('tbl_surat.Id', 'DESC');

        if (!empty($limit)) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function getStats($idTpq = null): array
    {
        $currentMonth = date('m');
        $currentYear  = date('Y');

        $builder = $this->select('
            COUNT(CASE WHEN JenisSurat = "Keluar" AND Status = "Final" AND MONTH(Tanggal) = "'.$currentMonth.'" AND YEAR(Tanggal) = "'.$currentYear.'" THEN 1 END) as TotalKeluarBulanIni,
            COUNT(CASE WHEN Status = "Draft" THEN 1 END) as TotalDraft,
            COUNT(CASE WHEN JenisSurat = "Masuk" THEN 1 END) as TotalMasuk,
            COUNT(CASE WHEN Status = "Final" THEN 1 END) as TotalFinal
        ');

        if (!empty($idTpq)) {
            $builder->where('IdTpq', $idTpq);
        }

        return $builder->first() ?? [
            'TotalKeluarBulanIni' => 0,
            'TotalDraft'          => 0,
            'TotalMasuk'          => 0,
            'TotalFinal'          => 0,
        ];
    }

    public function getMonthlyChartStats($idTpq = null, $year = null): array
    {
        $year = $year ?: date('Y');
        $keluar = array_fill(1, 12, 0);
        $masuk  = array_fill(1, 12, 0);

        $builder = $this->db->table($this->table)
            ->select('JenisSurat, MONTH(Tanggal) as bln, COUNT(Id) as total')
            ->where('YEAR(Tanggal)', $year)
            ->where('Status !=', 'Dibatalkan');

        if (!empty($idTpq)) {
            $builder->where('IdTpq', $idTpq);
        }

        $results = $builder->groupBy('JenisSurat, MONTH(Tanggal)')->get()->getResultArray();

        foreach ($results as $row) {
            $m = (int) $row['bln'];
            if ($row['JenisSurat'] === 'Keluar') {
                $keluar[$m] = (int) $row['total'];
            } else {
                $masuk[$m] = (int) $row['total'];
            }
        }

        return [
            'keluar' => array_values($keluar),
            'masuk'  => array_values($masuk),
            'year'   => $year,
        ];
    }

    public function getCategoryChartStats($idTpq = null): array
    {
        $builder = $this->db->table($this->table . ' s')
            ->select('COALESCE(k.NamaKategori, "Lainnya") as NamaKategori, COUNT(s.Id) as total')
            ->join('tbl_surat_kategori k', 'k.Id = s.IdKategori', 'left')
            ->where('s.Status !=', 'Dibatalkan');

        if (!empty($idTpq)) {
            $builder->where('s.IdTpq', $idTpq);
        }

        $results = $builder->groupBy('k.NamaKategori')->orderBy('total', 'DESC')->get()->getResultArray();

        $labels = [];
        $data   = [];
        foreach ($results as $r) {
            $labels[] = $r['NamaKategori'];
            $data[]   = (int) $r['total'];
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }
}
