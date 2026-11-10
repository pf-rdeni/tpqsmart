<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahPesertaModel extends Model
{
    protected $table = 'tbl_munaqosah_peserta';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'IdSantri',
        'IdTpq',
        'IdTahunAjaran',
        'HasKey',
        'status_verifikasi',
        'keterangan',
        'verified_at',
        'confirmed_at',
        'confirmed_by'
    ];

    protected $validationRules = [
        'IdSantri' => 'required|max_length[50]',
        'IdTpq' => 'required|max_length[50]',
        'IdTahunAjaran' => 'required|max_length[50]',
        'status_verifikasi' => 'permit_empty|in_list[valid,perlu_perbaikan,dikonfirmasi]',
        'keterangan' => 'permit_empty',
        'verified_at' => 'permit_empty|valid_date',
        'confirmed_at' => 'permit_empty|valid_date',
        'confirmed_by' => 'permit_empty|max_length[100]'
    ];

    protected $validationMessages = [
        'IdSantri' => [
            'required' => 'ID Santri harus diisi',
            'max_length' => 'ID Santri maksimal 50 karakter'
        ],
        'IdTpq' => [
            'required' => 'ID TPQ harus diisi',
            'max_length' => 'ID TPQ maksimal 50 karakter'
        ],
        'IdTahunAjaran' => [
            'required' => 'ID Tahun Ajaran harus diisi',
            'max_length' => 'ID Tahun Ajaran maksimal 50 karakter'
        ]
    ];

    public function getPesertaWithRelations($idTpq = null, $idKelas = null, $idTahunAjaran = null)
    {
        $builder = $this->db->table($this->table . ' pm');
        $builder->select('s.*, t.*, k.NamaKelas, COALESCE(ks.IdKelas, ks_aktif.IdKelas) as IdKelas, pm.*');
        $builder->join('tbl_santri_baru s', 's.IdSantri = pm.IdSantri', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = pm.IdTpq', 'left');
        $builder->join('tbl_kelas_santri ks', 'ks.IdSantri = pm.IdSantri AND ks.IdTahunAjaran = pm.IdTahunAjaran', 'left');
        $builder->join('tbl_kelas_santri ks_aktif', 'ks_aktif.IdSantri = pm.IdSantri AND ks_aktif.Status = 1', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = COALESCE(ks.IdKelas, ks_aktif.IdKelas)', 'left');

        if ($idTpq) {
            $builder->where('pm.IdTpq', $idTpq);
        }

        if ($idKelas) {
            $builder->where('COALESCE(ks.IdKelas, ks_aktif.IdKelas)', $idKelas);
        }

        if ($idTahunAjaran) {
            $builder->where('pm.IdTahunAjaran', $idTahunAjaran);
        }

        $builder->orderBy('pm.IdTpq', 'ASC');
        $builder->orderBy('s.NamaSantri', 'ASC');

        return $builder->get()->getResult();
    }

    public function getPesertaByTahunAjaran($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function getPesertaByTpq($idTpq, $idTahunAjaran)
    {
        return $this->where('IdTpq', $idTpq)
                   ->where('IdTahunAjaran', $idTahunAjaran)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function isPesertaExists($idSantri, $idTahunAjaran)
    {
        return $this->where('IdSantri', $idSantri)
                   ->where('IdTahunAjaran', $idTahunAjaran)
                   ->countAllResults() > 0;
    }

    /**
     * Get TPQ yang memiliki peserta, grouped by IdTpq
     * 
     * @param string|null $idTahunAjaran
     * @param int|null $idTpq Filter by IdTpq (optional)
     * @return array
     */
    public function getTpqFromPeserta($idTahunAjaran = null, $idTpq = null)
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.IdTpq, t.NamaTpq, t.KelurahanDesa, COUNT(DISTINCT p.IdSantri) as jumlah_peserta');
        $builder->join('tbl_tpq t', 't.IdTpq = p.IdTpq', 'left');

        if ($idTahunAjaran) {
            $builder->where('p.IdTahunAjaran', $idTahunAjaran);
        }

        if ($idTpq) {
            $builder->where('p.IdTpq', $idTpq);
        }

        $builder->groupBy('p.IdTpq');
        $builder->orderBy('t.NamaTpq', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get count peserta per TPQ
     * 
     * @param int $idTpq
     * @param string $idTahunAjaran
     * @return int
     */
    public function getCountPesertaByTpq($idTpq, $idTahunAjaran)
    {
        $result = $this->where('IdTpq', $idTpq)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->countAllResults();

        return (int)$result;
    }

    /**
     * Get peserta by status verifikasi
     * 
     * @param string $statusVerifikasi
     * @param string|null $idTahunAjaran
     * @return array
     */
    public function getPesertaByStatusVerifikasi($statusVerifikasi, $idTahunAjaran = null)
    {
        $builder = $this->where('status_verifikasi', $statusVerifikasi);

        if ($idTahunAjaran) {
            $builder->where('IdTahunAjaran', $idTahunAjaran);
        }

        return $builder->findAll();
    }

    /**
     * Update status verifikasi
     * 
     * @param int $id
     * @param string $statusVerifikasi
     * @param string|null $keterangan
     * @return bool
     */
    public function updateStatusVerifikasi($id, $statusVerifikasi, $keterangan = null)
    {
        $data = [
            'status_verifikasi' => $statusVerifikasi,
            'verified_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($keterangan)) {
            $data['keterangan'] = $keterangan;
        }

        return $this->update($id, $data);
    }

    /**
     * Konfirmasi perbaikan oleh operator/panitia
     * 
     * @param int $id
     * @param string $confirmedBy
     * @param string|null $keterangan
     * @return bool
     */
    public function konfirmasiPerbaikan($id, $confirmedBy, $keterangan = null)
    {
        $data = [
            'status_verifikasi' => 'valid',
            'confirmed_at' => date('Y-m-d H:i:s'),
            'confirmed_by' => $confirmedBy
        ];

        if (!empty($keterangan)) {
            $data['keterangan'] = $keterangan;
        }

        return $this->update($id, $data);
    }

    /**
     * Parse data pembatalan dari kolom keterangan
     *
     * @param string|null $keterangan
     * @return array ['is_batal' => bool, 'data' => array|null]
     */
    public function parsePembatalanData($keterangan)
    {
        if (empty($keterangan) || strpos($keterangan, '[Data Pembatalan JSON]') === false) {
            return ['is_batal' => false, 'data' => null];
        }

        if (preg_match('/\[Data Pembatalan JSON\]\s*\n([\s\S]*?)(?=\n\[|$)/', $keterangan, $matches)) {
            try {
                $json = json_decode(trim($matches[1]), true);
                if ($json && isset($json['status']) && $json['status'] === 'batal') {
                    return ['is_batal' => true, 'data' => $json];
                }
            } catch (\Exception $e) {
                return ['is_batal' => false, 'data' => null];
            }
        }

        return ['is_batal' => false, 'data' => null];
    }

    /**
     * Amend atau append tag [Data Pembatalan JSON] ke dalam kolom keterangan tanpa merusak teks lama
     *
     * @param string|null $keteranganLama
     * @param array $payloadBatal
     * @return string
     */
    public function amendPembatalanKeterangan($keteranganLama, array $payloadBatal)
    {
        $tag = '[Data Pembatalan JSON]';
        $jsonString = $tag . "\n" . json_encode($payloadBatal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if (empty($keteranganLama)) {
            return $jsonString;
        }

        // Jika tag sudah ada sebelumnya, ganti blok tersebut tanpa mengubah teks lainnya
        if (strpos($keteranganLama, $tag) !== false) {
            return preg_replace('/\[Data Pembatalan JSON\]\s*\n[\s\S]*?(?=\n\[|$)/', $jsonString, $keteranganLama);
        }

        // Jika belum ada, cukup append di baris baru
        return rtrim($keteranganLama) . "\n" . $jsonString;
    }

    /**
     * Cek apakah santri sudah memiliki nilai munaqosah final
     *
     * @param int|string $idSantri
     * @param int|string $idTahunAjaran
     * @return bool
     */
    /**
     * Cek apakah santri sudah memiliki nilai pra-munaqosah
     *
     * @param int|string $idSantri
     * @param int|string $idTahunAjaran
     * @return bool
     */
    public function hasNilaiPraMunaqosah($idSantri, $idTahunAjaran)
    {
        return $this->db->table('tbl_munaqosah_nilai')
            ->where('IdSantri', $idSantri)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', 'pra-munaqosah')
            ->countAllResults() > 0;
    }

    /**
     * Cek apakah santri sudah memiliki nilai munaqosah final
     *
     * @param int|string $idSantri
     * @param int|string $idTahunAjaran
     * @return bool
     */
    public function hasNilaiMunaqosahFinal($idSantri, $idTahunAjaran)
    {
        return $this->db->table('tbl_munaqosah_nilai')
            ->where('IdSantri', $idSantri)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', 'munaqosah')
            ->countAllResults() > 0;
    }
}
