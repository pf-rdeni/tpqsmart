<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahNilaiModel extends Model
{
    protected $table = 'tbl_munaqosah_nilai';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'NoPeserta',
        'IdSantri',
        'IdTpq',
        'IdTahunAjaran',
        'IdJuri',
        'IdMateri',
        'IdGrupMateriUjian',
        'RoomId',
        'IdKategoriMateri',
        'TypeUjian',
        'Nilai',
        'Catatan',
        'IsModified',
        'ModifiedBy',
        'ModifiedAt',
        'ModificationReason'
    ];

    protected $validationRules = [
        'NoPeserta' => 'required|max_length[50]',
        'IdSantri' => 'required|max_length[50]',
        'IdTpq' => 'required|max_length[50]',
        'IdTahunAjaran' => 'required|max_length[50]',
        'IdJuri' => 'required|max_length[50]',
        'IdMateri' => 'required|max_length[50]',
        'IdGrupMateriUjian' => 'required|max_length[50]',
        'IdKategoriMateri' => 'required|max_length[50]',
        'TypeUjian' => 'required|in_list[munaqosah,pra-munaqosah]',
        'Nilai' => 'required|decimal',
        'Catatan' => 'permit_empty'
    ];

    protected $validationMessages = [
        'NoPeserta' => [
            'required' => 'Nomor peserta harus diisi',
            'max_length' => 'Nomor peserta maksimal 50 karakter'
        ],
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
        ],
        'IdJuri' => [
            'required' => 'ID Juri harus diisi',
            'max_length' => 'ID Juri maksimal 50 karakter'
        ],
        'IdMateri' => [
            'required' => 'ID Materi harus diisi',
            'max_length' => 'ID Materi maksimal 50 karakter'
        ],
        'IdGrupMateriUjian' => [
            'required' => 'ID Grup Materi Ujian harus diisi',
            'max_length' => 'ID Grup Materi Ujian maksimal 50 karakter'
        ],
        'IdKategoriMateri' => [
            'required' => 'Kategori materi ujian harus diisi',
            'max_length' => 'ID kategori materi maksimal 50 karakter'
        ],
        'TypeUjian' => [
            'required' => 'Tipe ujian harus diisi',
            'in_list' => 'Tipe ujian harus munaqosah atau pra-munaqosah'
        ],
        'Nilai' => [
            'required' => 'Nilai harus diisi',
            'decimal' => 'Nilai harus berupa angka desimal'
        ]
    ];

    public function getNilaiWithRelations($id = null)
    {
        $builder = $this->db->table($this->table . ' nm');
        $builder->select('nm.*, s.NamaSantri, t.NamaTpq, mp.NamaMateri, km.NamaKategoriMateri');
        $builder->join('tbl_santri_baru s', 's.IdSantri = nm.IdSantri', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = nm.IdTpq', 'left');
        $builder->join('tbl_materi_pelajaran mp', 'mp.IdMateri = nm.IdMateri', 'left');
        $builder->join('tbl_munaqosah_kategori_materi km', 'km.IdKategoriMateri = nm.IdKategoriMateri', 'left');

        if ($id) {
            $builder->where('nm.id', $id);
            return $builder->get()->getRow();
        }

        return $builder->get()->getResult();
    }

    public function getNilaiByPeserta($noPeserta)
    {
        return $this->where('NoPeserta', $noPeserta)->findAll();
    }

    public function getNilaiByTahunAjaran($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)->findAll();
    }

    /**
     * Ambil 3 No Peserta terakhir yang sudah dinilai oleh juri tertentu dengan durasi
     */
    public function getPesertaTerakhirByJuri($idJuri, $idTahunAjaran, $typeUjian)
    {
        // Query sederhana untuk mendapatkan data peserta
        $sql = "
            SELECT DISTINCT 
                mn.NoPeserta, 
                MAX(mn.updated_at) as updated_at,
                s.NamaSantri, 
                j.UsernameJuri
            FROM tbl_munaqosah_nilai mn
            LEFT JOIN tbl_munaqosah_juri j ON j.IdJuri = mn.IdJuri
            LEFT JOIN tbl_santri_baru s ON s.IdSantri = mn.IdSantri
            WHERE mn.IdJuri = ? 
                AND mn.IdTahunAjaran = ? 
                AND mn.TypeUjian = ?
            GROUP BY mn.NoPeserta, s.NamaSantri, j.UsernameJuri
            ORDER BY updated_at DESC
        ";

        $result = $this->db->query($sql, [$idJuri, $idTahunAjaran, $typeUjian])->getResultArray();

        // Hitung durasi secara manual untuk akurasi
        $totalRows = count($result);
        foreach ($result as $index => &$row) {
            $durationSeconds = null;

            if ($index < $totalRows - 1) {
                // Ada data berikutnya, hitung durasi dari data berikutnya
                $nextRow = $result[$index + 1];
                $currentTime = strtotime($row['updated_at']);
                $nextTime = strtotime($nextRow['updated_at']);
                $durationSeconds = $currentTime - $nextTime;
            }

            if ($durationSeconds !== null && $durationSeconds > 0) {
                $minutes = floor($durationSeconds / 60);
                $seconds = $durationSeconds % 60;

                if ($minutes > 0) {
                    $row['duration'] = $minutes . 'm ' . $seconds . 's';
                } else {
                    $row['duration'] = $seconds . 's';
                }

                // Tentukan class berdasarkan durasi
                if ($durationSeconds <= 60) { // <= 1 menit
                    $row['duration_class'] = 'duration-fast';
                } elseif ($durationSeconds <= 300) { // <= 5 menit
                    $row['duration_class'] = 'duration-medium';
                } else { // > 5 menit
                    $row['duration_class'] = 'duration-slow';
                }
            } else {
                $row['duration'] = '-';
                $row['duration_class'] = 'duration-none';
            }
        }

        return $result;
    }

    public function getTotalPesertaByJuri($IdTpq, $idJuri, $idTahunAjaran, $typeUjian)
    {
        return $this->where('IdTpq', $IdTpq)
            ->where('IdJuri', $idJuri)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', $typeUjian)
            ->distinct()
            ->countAllResults('NoPeserta');
    }

    /**
     * Get total unique participants already evaluated per tahun ajaran, type ujian, and TPQ
     */
    public function getTotalEvaluatedParticipants($idTahunAjaran, $typeUjian, $idTpq = 0)
    {
        $builder = $this->db->table($this->table);
        $builder->select('COUNT(DISTINCT NoPeserta) as count');
        $builder->where('IdTahunAjaran', $idTahunAjaran);
        $builder->where('TypeUjian', $typeUjian);
        $builder->where('IdTpq', $idTpq);
        return $builder->get()->getRow()->count;
    }

    /**
     * Cek apakah peserta sudah di-test di room tertentu untuk grup materi tertentu
     * 
     * @param string $noPeserta
     * @param string $idGrupMateriUjian
     * @param string $idTahunAjaran
     * @param string $typeUjian
     * @return array|null
     */
    public function getPesertaRoomByGrupMateri($noPeserta, $idGrupMateriUjian, $idTahunAjaran, $typeUjian)
    {
        return $this->select('RoomId')
            ->where('NoPeserta', $noPeserta)
            ->where('IdGrupMateriUjian', $idGrupMateriUjian)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', $typeUjian)
            ->where('RoomId IS NOT NULL')
            ->where('RoomId !=', '')
            ->first();
    }

    /**
     * Cek berapa juri yang sudah menilai peserta di grup materi tertentu
     * 
     * @param string $noPeserta
     * @param string $idGrupMateriUjian
     * @param string $idTahunAjaran
     * @param string $typeUjian
     * @return int
     */
    public function countJuriByPesertaGrupMateri($noPeserta, $idGrupMateriUjian, $idTahunAjaran, $typeUjian)
    {
        $result = $this->select('COUNT(DISTINCT IdJuri) as total')
            ->where('NoPeserta', $noPeserta)
            ->where('IdGrupMateriUjian', $idGrupMateriUjian)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', $typeUjian)
            ->first();

        return $result['total'] ?? 0;
    }

    /**
     * Get list of juri who already scored a participant for specific grup materi
     * 
     * @param string $noPeserta
     * @param string $idGrupMateriUjian
     * @param string $idTahunAjaran
     * @param string $typeUjian
     * @return array
     */
    public function getJuriByPesertaGrupMateri($noPeserta, $idGrupMateriUjian, $idTahunAjaran, $typeUjian)
    {
        $builder = $this->db->table($this->table . ' mn');
        $builder->select('mn.IdJuri, j.UsernameJuri, mn.RoomId, mn.Nilai, mn.updated_at');
        $builder->join('tbl_munaqosah_juri j', 'j.IdJuri = mn.IdJuri', 'left');
        $builder->where('mn.NoPeserta', $noPeserta);
        $builder->where('mn.IdGrupMateriUjian', $idGrupMateriUjian);
        $builder->where('mn.IdTahunAjaran', $idTahunAjaran);
        $builder->where('mn.TypeUjian', $typeUjian);
        $builder->orderBy('mn.created_at', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Check if a jury already scored this participant for specific grup materi
     * 
     * @param string $noPeserta
     * @param string $idJuri
     * @param string $idGrupMateriUjian
     * @param string $idTahunAjaran
     * @param string $typeUjian
     * @return array|null
     */
    public function checkJuriAlreadyScored($noPeserta, $idJuri, $idGrupMateriUjian, $idTahunAjaran, $typeUjian)
    {
        return $this->where('NoPeserta', $noPeserta)
            ->where('IdJuri', $idJuri)
            ->where('IdGrupMateriUjian', $idGrupMateriUjian)
            ->where('IdTahunAjaran', $idTahunAjaran)
            ->where('TypeUjian', $typeUjian)
            ->first();
    }
}
