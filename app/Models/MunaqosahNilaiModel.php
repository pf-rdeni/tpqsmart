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
        $builder->join('tbl_kategori_materi km', 'km.IdKategoriMateri = nm.IdKategoriMateri', 'left');

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
            ->groupBy('NoPeserta')
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
     * Get statistik peserta yang sudah dinilai per GroupPeserta dan IdTpq
     * Menghitung peserta yang sudah selesai dinilai untuk semua grup materi
     * 
     * @param string $idTahunAjaran
     * @param string|null $typeUjian
     * @param int|null $idTpq
     * @return array
     */
    public function getStatistikPesertaDinilai($idTahunAjaran, $typeUjian = null, $idTpq = null)
    {
        // Ambil semua grup materi aktif
        $grupMateriModel = new \App\Models\MunaqosahGrupMateriUjiModel();
        $grupList = $grupMateriModel->getGrupMateriAktif();
        $totalGrupMateri = count($grupList);

        // Ambil data jadwal untuk mendapatkan GroupPeserta
        $jadwalModel = new \App\Models\MunaqosahJadwalUjianModel();
        $jadwalGroups = $jadwalModel->getStatistikGroupPeserta($idTahunAjaran, $typeUjian, $idTpq);

        $result = [];

        foreach ($jadwalGroups as $jadwalGroup) {
            $groupPeserta = $jadwalGroup['GroupPeserta'];
            $idTpqGroup = $jadwalGroup['IdTpq'];

            // Hitung total peserta yang sudah dinilai untuk semua grup materi
            // Query untuk mendapatkan peserta yang sudah dinilai di semua grup materi
            $sql = "SELECT mn.IdTpq, mn.NoPeserta, COUNT(DISTINCT mn.IdGrupMateriUjian) as grup_materi_count
                    FROM {$this->table} mn
                    WHERE mn.IdTahunAjaran = ? AND mn.IdTpq = ?";
            $params = [$idTahunAjaran, $idTpqGroup];

            if (!empty($typeUjian)) {
                $sql .= " AND mn.TypeUjian = ?";
                $params[] = $typeUjian;
            }

            $sql .= " GROUP BY mn.IdTpq, mn.NoPeserta
                      HAVING grup_materi_count = ?";
            $params[] = $totalGrupMateri;

            $pesertaSelesai = $this->db->query($sql, $params)->getResultArray();
            $totalSelesai = count($pesertaSelesai);

            // Hitung total peserta yang sudah dinilai untuk minimal 1 grup materi
            $builder2 = $this->db->table($this->table . ' mn');
            $builder2->select('COUNT(DISTINCT mn.NoPeserta) as total_dinilai');
            $builder2->where('mn.IdTahunAjaran', $idTahunAjaran);
            $builder2->where('mn.IdTpq', $idTpqGroup);

            if (!empty($typeUjian)) {
                $builder2->where('mn.TypeUjian', $typeUjian);
            }

            $totalDinilai = $builder2->get()->getRowArray();
            $totalDinilai = $totalDinilai ? (int)$totalDinilai['total_dinilai'] : 0;

            $result[] = [
                'GroupPeserta' => $groupPeserta,
                'IdTpq' => $idTpqGroup,
                'NamaTpq' => $jadwalGroup['NamaTpq'],
                'total_peserta' => (int)$jadwalGroup['total_peserta'],
                'total_dinilai' => $totalDinilai,
                'total_selesai' => $totalSelesai,
                'total_belum' => max(0, (int)$jadwalGroup['total_peserta'] - $totalDinilai),
            ];
        }

        return $result;
    }

    /**
     * Get statistik persentase input nilai per Group Materi
     * 
     * @param string $idTahunAjaran
     * @param string|null $typeUjian
     * @param int|null $idTpq
     * @return array
     */
    public function getStatistikPerGroupMateri($idTahunAjaran, $typeUjian = null, $idTpq = null)
    {
        // Ambil semua grup materi aktif
        $grupMateriModel = new \App\Models\MunaqosahGrupMateriUjiModel();
        $grupList = $grupMateriModel->getGrupMateriAktif();

        $result = [];

        foreach ($grupList as $grup) {
            $idGrupMateriUjian = $grup['IdGrupMateriUjian'];
            $namaGrupMateri = $grup['NamaMateriGrup'];

            // Hitung total peserta yang terdaftar untuk grup materi ini
            $builderRegistrasi = $this->db->table('tbl_munaqosah_registrasi_uji r');
            $builderRegistrasi->select('COUNT(DISTINCT r.NoPeserta) as total_peserta');
            $builderRegistrasi->where('r.IdTahunAjaran', $idTahunAjaran);
            $builderRegistrasi->where('r.IdGrupMateriUjian', $idGrupMateriUjian);

            if (!empty($typeUjian)) {
                $builderRegistrasi->where('r.TypeUjian', $typeUjian);
            }

            if (!empty($idTpq)) {
                $builderRegistrasi->where('r.IdTpq', $idTpq);
            }

            $totalPeserta = $builderRegistrasi->get()->getRowArray();
            $totalPeserta = $totalPeserta ? (int)$totalPeserta['total_peserta'] : 0;

            // Hitung total peserta yang sudah dinilai untuk grup materi ini
            // Gunakan subquery untuk mendapatkan peserta yang sudah dinilai di grup materi ini
            $sql = "SELECT COUNT(DISTINCT n.NoPeserta) as total_dinilai
                    FROM {$this->table} n
                    INNER JOIN tbl_munaqosah_registrasi_uji r ON r.NoPeserta = n.NoPeserta 
                        AND r.IdTahunAjaran = n.IdTahunAjaran 
                        AND r.TypeUjian = n.TypeUjian
                        AND r.IdGrupMateriUjian = ?
                    WHERE n.IdTahunAjaran = ?";

            $params = [$idGrupMateriUjian, $idTahunAjaran];

            if (!empty($typeUjian)) {
                $sql .= " AND n.TypeUjian = ?";
                $params[] = $typeUjian;
            }

            if (!empty($idTpq)) {
                $sql .= " AND n.IdTpq = ?";
                $params[] = $idTpq;
            }

            $totalDinilai = $this->db->query($sql, $params)->getRowArray();
            $totalDinilai = $totalDinilai ? (int)$totalDinilai['total_dinilai'] : 0;

            // Hitung persentase
            $persentase = $totalPeserta > 0 ? round(($totalDinilai / $totalPeserta) * 100) : 0;

            $result[] = [
                'IdGrupMateriUjian' => $idGrupMateriUjian,
                'NamaMateriGrup' => $namaGrupMateri,
                'total_peserta' => $totalPeserta,
                'total_dinilai' => $totalDinilai,
                'total_belum' => max(0, $totalPeserta - $totalDinilai),
                'persentase' => $persentase,
            ];
        }

        return $result;
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
     * Cek apakah peserta sudah memiliki nilai di grup materi tertentu
     * 
     * @param string $noPeserta
     * @param string $idGrupMateriUjian
     * @param string $idTahunAjaran
     * @param string $typeUjian
     * @param string|null $idTpq
     * @return bool True jika sudah ada nilai, False jika belum
     */
    public function hasNilaiByGrupMateri($noPeserta, $idGrupMateriUjian, $idTahunAjaran, $typeUjian, $idTpq = null)
    {
        $builder = $this->db->table($this->table);
        $builder->where('NoPeserta', $noPeserta);
        $builder->where('IdGrupMateriUjian', $idGrupMateriUjian);
        $builder->where('IdTahunAjaran', $idTahunAjaran);
        $builder->where('TypeUjian', $typeUjian);

        if (!empty($idTpq)) {
            $builder->where('IdTpq', $idTpq);
        }

        $count = $builder->countAllResults();

        return $count > 0;
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

    /**
     * Get statistik penilaian per Juri
     * Menghitung jumlah nilai yang sudah diinput per juri
     * 
     * @param string $idTahunAjaran
     * @param string|null $typeUjian
     * @param int|null $idTpq
     * @return array
     */
    public function getStatistikPenilaianPerJuri($idTahunAjaran, $typeUjian = null, $idTpq = null)
    {
        $builder = $this->db->table($this->table . ' n');
        $builder->select('j.UsernameJuri, j.IdGrupMateriUjian, g.NamaMateriGrup, COUNT(DISTINCT n.id) as total_input');
        $builder->join('tbl_munaqosah_juri j', 'j.IdJuri = n.IdJuri', 'left');
        $builder->join('tbl_munaqosah_grup_materi_uji g', 'g.IdGrupMateriUjian = j.IdGrupMateriUjian', 'left');

        $builder->where('n.IdTahunAjaran', $idTahunAjaran);

        if (!empty($typeUjian)) {
            $builder->where('n.TypeUjian', $typeUjian);
        }

        if (!empty($idTpq)) {
            $builder->where('n.IdTpq', $idTpq);
        }

        $builder->groupBy('j.UsernameJuri, j.IdGrupMateriUjian, g.NamaMateriGrup');
        $builder->orderBy('g.NamaMateriGrup', 'ASC');
        $builder->orderBy('j.UsernameJuri', 'ASC');

        $result = $builder->get()->getResultArray();

        return $result;
    }

    /**
     * Get statistik penilaian per Grup Materi berdasarkan Ruangan
     * Menghitung jumlah nilai yang sudah diinput per grup materi per ruangan
     * 
     * @param string $idTahunAjaran
     * @param string|null $typeUjian
     * @param int|null $idTpq
     * @return array
     */
    public function getStatistikPenilaianPerGrupMateriRuangan($idTahunAjaran, $typeUjian = null, $idTpq = null)
    {
        $builder = $this->db->table($this->table . ' n');
        $builder->select('g.NamaMateriGrup, n.RoomId, COUNT(DISTINCT n.id) as total_input');
        $builder->join('tbl_munaqosah_grup_materi_uji g', 'g.IdGrupMateriUjian = n.IdGrupMateriUjian', 'left');

        $builder->where('n.IdTahunAjaran', $idTahunAjaran);
        $builder->where('n.RoomId IS NOT NULL');
        $builder->where('n.RoomId !=', '');

        if (!empty($typeUjian)) {
            $builder->where('n.TypeUjian', $typeUjian);
        }

        if (!empty($idTpq)) {
            $builder->where('n.IdTpq', $idTpq);
        }

        $builder->groupBy('g.NamaMateriGrup, n.RoomId');
        $builder->orderBy('g.NamaMateriGrup', 'ASC');
        $builder->orderBy('n.RoomId', 'ASC');

        $result = $builder->get()->getResultArray();

        return $result;
    }

    /**
     * Get count nilai munaqosah by filter - group by NoPeserta
     * @param mixed $IdTpq
     * @param mixed $IdTahunAjaran
     * @param mixed $TypeUjian
     * @return array
     */
    public function getCountNilaiByFilter($IdTpq = null, $IdTahunAjaran = null, $TypeUjian = null)
    {
        $builder = $this->db->table('tbl_munaqosah_nilai n');
        $builder->select('
            n.IdTpq, 
            t.NamaTpq,
            t.KelurahanDesa,
            n.IdTahunAjaran, 
            n.TypeUjian,
            n.NoPeserta,
            COUNT(*) as TotalNilai
        ');
        $builder->join('tbl_tpq t', 't.IdTpq = n.IdTpq', 'left');

        // Apply filters
        if (!empty($IdTpq)) {
            if (is_array($IdTpq)) {
                $builder->whereIn('n.IdTpq', $IdTpq);
            } else {
                $builder->where('n.IdTpq', $IdTpq);
            }
        }

        if (!empty($IdTahunAjaran)) {
            if (is_array($IdTahunAjaran)) {
                $builder->whereIn('n.IdTahunAjaran', $IdTahunAjaran);
            } else {
                $builder->where('n.IdTahunAjaran', $IdTahunAjaran);
            }
        }

        if (!empty($TypeUjian)) {
            if (is_array($TypeUjian)) {
                $builder->whereIn('n.TypeUjian', $TypeUjian);
            } else {
                $builder->where('n.TypeUjian', $TypeUjian);
            }
        }

        $builder->groupBy(['n.IdTpq', 't.NamaTpq', 't.KelurahanDesa', 'n.IdTahunAjaran', 'n.TypeUjian', 'n.NoPeserta']);
        $builder->orderBy('n.IdTpq', 'ASC');
        $builder->orderBy('n.IdTahunAjaran', 'DESC');
        $builder->orderBy('n.TypeUjian', 'ASC');
        $builder->orderBy('n.NoPeserta', 'ASC');

        $results = $builder->get()->getResultArray();

        // Hitung total dan format data
        $totalCount = 0;
        foreach ($results as &$row) {
            $row['TotalNilai'] = (int)$row['TotalNilai'];
            $totalCount += $row['TotalNilai'];
        }
        unset($row);

        return [
            'detail' => $results,
            'total' => $totalCount
        ];
    }

    /**
     * Hapus nilai munaqosah by selected peserta berdasarkan NoPeserta
     * @param array $selectedPeserta Array of peserta data (NoPeserta)
     * @return array
     */
    public function deleteNilaiBySelectedPeserta($selectedPeserta)
    {
        $this->db->transStart();
        try {
            $totalAffected = 0;
            $details = [];
            
            foreach ($selectedPeserta as $pesertaData) {
                $NoPeserta = $pesertaData['NoPeserta'] ?? null;

                if (empty($NoPeserta)) {
                    continue;
                }

                // Count before delete
                $countBuilder = $this->db->table($this->table);
                $countBuilder->where('NoPeserta', $NoPeserta);
                $countBefore = $countBuilder->countAllResults();

                // Hapus data berdasarkan NoPeserta
                $deleteBuilder = $this->db->table($this->table);
                $deleteBuilder->where('NoPeserta', $NoPeserta);
                $deleteBuilder->delete();
                
                $affected = $this->db->affectedRows();
                $totalAffected += $affected;

                $details[] = [
                    'NoPeserta' => $NoPeserta,
                    'affected' => $affected,
                    'count_before' => $countBefore
                ];
            }

            $this->db->transComplete();

            // Clear cache jika ada
            if (function_exists('cache')) {
                cache()->clean();
            }

            return [
                'total_affected' => $totalAffected,
                'details' => $details
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
}
