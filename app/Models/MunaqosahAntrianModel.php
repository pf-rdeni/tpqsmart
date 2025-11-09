<?php

namespace App\Models;

use CodeIgniter\Model;

class MunaqosahAntrianModel extends Model
{
    protected $table = 'tbl_munaqosah_antrian';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'NoPeserta',
        'IdTahunAjaran',
        'IdGrupMateriUjian',
        'TypeUjian',
        'IdKategoriMateri',
        'Status',
        'RoomId',
        'Keterangan',
        'IdTpq',
        'IdSantri',
        'GroupPeserta'
    ];

    protected $validationRules = [
        'NoPeserta' => 'required|max_length[50]',
        'IdTahunAjaran' => 'required|max_length[50]',
        'IdGrupMateriUjian' => 'permit_empty|max_length[50]',
        'TypeUjian' => 'permit_empty|in_list[pra-munaqosah,munaqosah]',
        'IdKategoriMateri' => 'permit_empty|max_length[50]',
        'Status' => 'permit_empty|in_list[0,1,2]',
        'RoomId' => 'permit_empty|max_length[20]',
        'Keterangan' => 'permit_empty',
        'IdTpq' => 'permit_empty|max_length[50]',
        'IdSantri' => 'permit_empty|max_length[50]',
        'GroupPeserta' => 'permit_empty|max_length[50]'
    ];

    protected $validationMessages = [
        'NoPeserta' => [
            'required' => 'Nomor peserta harus diisi',
            'max_length' => 'Nomor peserta maksimal 50 karakter'
        ],
        'IdTahunAjaran' => [
            'required' => 'ID Tahun Ajaran harus diisi',
            'max_length' => 'ID Tahun Ajaran maksimal 50 karakter'
        ],
        'IdGrupMateriUjian' => [
            'max_length' => 'ID Grup Materi Ujian maksimal 50 karakter'
        ],
        'TypeUjian' => [
            'in_list' => 'Type ujian harus pra-munaqosah atau munaqosah'
        ],
        'IdKategoriMateri' => [
            'max_length' => 'ID kategori materi maksimal 50 karakter'
        ],
        'Status' => [
            'in_list' => 'Status harus 0 (menunggu), 1 (proses), atau 2 (selesai)'
        ],
        'RoomId' => [
            'max_length' => 'Room ID maksimal 20 karakter'
        ]
    ];

    public function getAntrianByTahunAjaran($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function getAntrianBelumSelesai($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
            ->where('Status', 0)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function getAntrianSelesai($idTahunAjaran)
    {
        return $this->where('IdTahunAjaran', $idTahunAjaran)
            ->where('Status', 2)
                   ->orderBy('created_at', 'ASC')
                   ->findAll();
    }

    public function updateStatus($id, $status, $roomId = null)
    {
        return $this->update($id, [
            'Status' => $status,
            'RoomId' => $roomId,
        ]);
    }

    public function getQueueWithDetails(array $filters = [])
    {
        $builder = $this->db->table($this->table . ' q');
        $builder->select('q.*, COALESCE(q.IdGrupMateriUjian, r.IdGrupMateriUjian) as IdGrupMateriResolved, COALESCE(q.TypeUjian, r.TypeUjian) as TypeUjianResolved, COALESCE(q.IdKategoriMateri, r.IdKategoriMateri) as IdKategoriMateriResolved, COALESCE(q.IdTpq, r.IdTpq) as IdTpqResolved, COALESCE(q.IdSantri, r.IdSantri) as IdSantriResolved, r.IdSantri, r.IdTpq, r.IdGrupMateriUjian, r.IdKategoriMateri, r.TypeUjian, s.NamaSantri, t.NamaTpq, km.NamaKategoriMateri');

        // JOIN dengan tabel registrasi
        // Jika filter TypeUjian diberikan, tambahkan kondisi TypeUjian dalam JOIN
        // untuk memastikan JOIN hanya mengambil data registrasi yang sesuai TypeUjian
        // Ini penting jika ada NoPeserta yang sama dengan TypeUjian berbeda di tabel registrasi
        $joinCondition = 'r.NoPeserta = q.NoPeserta AND r.IdTahunAjaran = q.IdTahunAjaran';
        if (!empty($filters['TypeUjian'])) {
            $allowedTypes = ['pra-munaqosah', 'munaqosah'];
            if (in_array($filters['TypeUjian'], $allowedTypes, true)) {
                // Tambahkan kondisi TypeUjian dalam JOIN untuk memastikan data registrasi sesuai
                // Jika q.TypeUjian sudah ada, JOIN hanya dengan data registrasi yang TypeUjian-nya sama dengan q.TypeUjian
                // Jika q.TypeUjian NULL, JOIN hanya dengan data registrasi yang TypeUjian-nya sesuai filter
                $joinCondition .= ' AND (';
                $joinCondition .= '(q.TypeUjian IS NOT NULL AND q.TypeUjian != \'\' AND r.TypeUjian = q.TypeUjian)';
                $joinCondition .= ' OR ';
                $joinCondition .= '(q.TypeUjian IS NULL OR q.TypeUjian = \'\') AND r.TypeUjian = ' . $this->db->escape($filters['TypeUjian']);
                $joinCondition .= ')';
            }
        }
        $builder->join('tbl_munaqosah_registrasi_uji r', $joinCondition, 'left');

        $builder->join('tbl_santri_baru s', 's.IdSantri = COALESCE(q.IdSantri, r.IdSantri)', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = COALESCE(q.IdTpq, r.IdTpq)', 'left');
        $builder->join('tbl_kategori_materi km', 'km.IdKategoriMateri = COALESCE(q.IdKategoriMateri, r.IdKategoriMateri)', 'left', false);

        if (!empty($filters['IdTahunAjaran'])) {
            $builder->where('q.IdTahunAjaran', $filters['IdTahunAjaran']);
        }

        if (!empty($filters['IdGrupMateriUjian'])) {
            $builder->groupStart()
                ->where('q.IdGrupMateriUjian', $filters['IdGrupMateriUjian'])
                ->orGroupStart()
                ->where('q.IdGrupMateriUjian IS NULL')
                ->where('r.IdGrupMateriUjian', $filters['IdGrupMateriUjian'])
                ->groupEnd()
                ->groupEnd();
        }

        if (!empty($filters['TypeUjian'])) {
            // Validasi: pastikan TypeUjian hanya menerima nilai 'pra-munaqosah' atau 'munaqosah'
            $allowedTypes = ['pra-munaqosah', 'munaqosah'];
            if (in_array($filters['TypeUjian'], $allowedTypes, true)) {
                // Filter TypeUjian: HANYA menampilkan data dengan TypeUjian yang sesuai filter
                // Prioritas: q.TypeUjian (tabel antrian) adalah sumber utama
                // Jika q.TypeUjian ada, harus sesuai filter
                // Jika q.TypeUjian NULL/kosong, gunakan r.TypeUjian yang sesuai filter
                $builder->groupStart()
                    // Case 1: q.TypeUjian ada dan HARUS sesuai filter
                    ->where('q.TypeUjian', $filters['TypeUjian'])
                    ->orGroupStart()
                    // Case 2: q.TypeUjian NULL atau kosong, DAN r.TypeUjian harus sesuai filter
                    ->where('(q.TypeUjian IS NULL OR q.TypeUjian = \'\')', null, false)
                    ->where('r.TypeUjian', $filters['TypeUjian'])
                    ->groupEnd()
                    ->groupEnd();

                // Note: Filter tambahan pasca-query dilakukan di bawah untuk memastikan TypeUjianResolved sesuai
            }
        }

        if (!empty($filters['IdTpq'])) {
            $builder->groupStart()
                ->where('q.IdTpq', $filters['IdTpq'])
                ->orGroupStart()
                ->where('q.IdTpq IS NULL')
                ->where('r.IdTpq', $filters['IdTpq'])
                ->groupEnd()
                ->groupEnd();
        }

        if (isset($filters['Status']) && $filters['Status'] !== '') {
            $builder->where('q.Status', $filters['Status']);
        }

        $builder->orderBy('q.Status', 'ASC');
        // Sorting berdasarkan GroupPeserta (ASC), jika NULL maka default ke 'Group 1'
        //Jika diperlukan kondisi memprioritaskan GroupPeserta, gunakan kondisi ini
        //$builder->orderBy('COALESCE(q.GroupPeserta, \'Group 1\')', 'ASC', false);
        $builder->orderBy('q.created_at', 'ASC');
        $builder->groupBy('q.id');

        $result = $builder->get()->getResultArray();

        // Filter pasca-query: pastikan TypeUjianResolved (yang ditampilkan di view) sesuai filter
        // Ini memastikan tidak ada data yang lolos dari filter karena JOIN atau kondisi lainnya
        if (!empty($filters['TypeUjian'])) {
            $allowedTypes = ['pra-munaqosah', 'munaqosah'];
            if (in_array($filters['TypeUjian'], $allowedTypes, true)) {
                $result = array_filter($result, function ($row) use ($filters) {
                    // Gunakan TypeUjian dari tabel antrian (q) sebagai prioritas utama
                    // Field 'TypeUjian' di hasil query adalah dari q (karena q.* di SELECT)
                    $typeUjianFromQ = $row['TypeUjian'] ?? null;

                    // Jika q.TypeUjian ada, gunakan itu (harus sesuai filter karena sudah difilter di WHERE)
                    // Jika q.TypeUjian NULL/kosong, gunakan TypeUjianResolved
                    if (!empty($typeUjianFromQ)) {
                        return $typeUjianFromQ === $filters['TypeUjian'];
                    } else {
                        // Jika q.TypeUjian NULL, gunakan TypeUjianResolved (yang sudah sesuai filter karena JOIN)
                        $typeUjianResolved = $row['TypeUjianResolved'] ?? null;
                        return $typeUjianResolved === $filters['TypeUjian'];
                    }
                });
                // Re-index array setelah filter
                $result = array_values($result);
            }
        }

        return $result;
    }

    public function getStatusCounts(array $filters = [])
    {
        $builder = $this->db->table($this->table . ' q');
        $builder->select('q.Status, COUNT(DISTINCT q.id) as total');
        $builder->join('tbl_munaqosah_registrasi_uji r', 'r.NoPeserta = q.NoPeserta AND r.IdTahunAjaran = q.IdTahunAjaran', 'left');

        if (!empty($filters['IdTahunAjaran'])) {
            $builder->where('q.IdTahunAjaran', $filters['IdTahunAjaran']);
        }

        if (!empty($filters['IdGrupMateriUjian'])) {
            $builder->groupStart()
                ->where('q.IdGrupMateriUjian', $filters['IdGrupMateriUjian'])
                ->orGroupStart()
                ->where('q.IdGrupMateriUjian IS NULL')
                ->where('r.IdGrupMateriUjian', $filters['IdGrupMateriUjian'])
                ->groupEnd()
                ->groupEnd();
        }

        if (!empty($filters['TypeUjian'])) {
            // Validasi: pastikan TypeUjian hanya menerima nilai 'pra-munaqosah' atau 'munaqosah'
            $allowedTypes = ['pra-munaqosah', 'munaqosah'];
            if (in_array($filters['TypeUjian'], $allowedTypes, true)) {
                $builder->groupStart()
                    ->where('q.TypeUjian', $filters['TypeUjian'])
                    ->orGroupStart()
                    ->where('(q.TypeUjian IS NULL OR q.TypeUjian = \'\')', null, false)
                    ->where('r.TypeUjian', $filters['TypeUjian'])
                    ->groupEnd()
                    ->groupEnd();
            }
        }

        if (!empty($filters['IdTpq'])) {
            $builder->groupStart()
                ->where('q.IdTpq', $filters['IdTpq'])
                ->orGroupStart()
                ->where('q.IdTpq IS NULL')
                ->where('r.IdTpq', $filters['IdTpq'])
                ->groupEnd()
                ->groupEnd();
        }

        $builder->groupBy('q.Status');

        $results = $builder->get()->getResultArray();

        $counts = [
            0 => 0,
            1 => 0,
            2 => 0,
        ];

        foreach ($results as $row) {
            $status = (int) $row['Status'];
            $counts[$status] = (int) $row['total'];
        }

        return $counts;
    }

    /**
     * Cek apakah peserta sudah ada di antrian grup materi lain (bukan status 2)
     * Mempertimbangkan filter tahun, type, dan tpq
     * 
     * @param string $noPeserta
     * @param string $idTahunAjaran
     * @param string $idGrupMateriUjian (grup saat ini, akan diexclude)
     * @param string $typeUjian
     * @param string|null $idTpq
     * @return array|null Data antrian di grup lain atau null jika tidak ada
     */
    public function getAntrianDiGrupLain($noPeserta, $idTahunAjaran, $idGrupMateriUjian, $typeUjian, $idTpq = null)
    {
        $builder = $this->db->table($this->table . ' q');
        $builder->select('q.*, r.IdGrupMateriUjian as IdGrupMateriResolved, r.TypeUjian as TypeUjianResolved, 
                         r.IdTpq as IdTpqResolved, s.NamaSantri, gm.NamaMateriGrup');

        // JOIN dengan registrasi untuk mendapatkan data lengkap
        $joinCondition = 'r.NoPeserta = q.NoPeserta AND r.IdTahunAjaran = q.IdTahunAjaran';
        $builder->join('tbl_munaqosah_registrasi_uji r', $joinCondition, 'left');

        // JOIN dengan santri untuk nama
        $builder->join('tbl_santri_baru s', 's.IdSantri = COALESCE(q.IdSantri, r.IdSantri)', 'left');

        // JOIN dengan grup materi untuk nama grup
        $builder->join(
            'tbl_munaqosah_grup_materi_uji gm',
            'gm.IdGrupMateriUjian = COALESCE(q.IdGrupMateriUjian, r.IdGrupMateriUjian)',
            'left'
        );

        // Filter berdasarkan no peserta dan tahun ajaran
        $builder->where('q.NoPeserta', $noPeserta);
        $builder->where('q.IdTahunAjaran', $idTahunAjaran);

        // Exclude grup materi saat ini
        $builder->groupStart()
            ->where('q.IdGrupMateriUjian !=', $idGrupMateriUjian)
            ->where('q.IdGrupMateriUjian IS NOT NULL')
            ->orGroupStart()
            ->where('q.IdGrupMateriUjian IS NULL')
            ->where('r.IdGrupMateriUjian !=', $idGrupMateriUjian)
            ->where('r.IdGrupMateriUjian IS NOT NULL')
            ->groupEnd()
            ->groupEnd();

        // Filter TypeUjian
        $builder->groupStart()
            ->where('q.TypeUjian', $typeUjian)
            ->orGroupStart()
            ->where('(q.TypeUjian IS NULL OR q.TypeUjian = \'\')', null, false)
            ->where('r.TypeUjian', $typeUjian)
            ->groupEnd()
            ->groupEnd();

        // Filter IdTpq jika tersedia
        if (!empty($idTpq)) {
            $builder->groupStart()
                ->where('q.IdTpq', $idTpq)
                ->orGroupStart()
                ->where('q.IdTpq IS NULL')
                ->where('r.IdTpq', $idTpq)
                ->groupEnd()
                ->groupEnd();
        }

        // Hanya ambil yang status bukan 2 (selesai)
        $builder->whereIn('q.Status', [0, 1]);

        // Urutkan berdasarkan created_at terbaru
        $builder->orderBy('q.created_at', 'DESC');

        $result = $builder->get()->getResultArray();

        // Filter pasca-query untuk memastikan TypeUjian sesuai
        if (!empty($result)) {
            $result = array_filter($result, function ($row) use ($typeUjian) {
                $typeUjianFromQ = $row['TypeUjian'] ?? null;
                if (!empty($typeUjianFromQ)) {
                    return $typeUjianFromQ === $typeUjian;
                } else {
                    $typeUjianResolved = $row['TypeUjianResolved'] ?? null;
                    return $typeUjianResolved === $typeUjian;
                }
            });
            $result = array_values($result);
        }

        return !empty($result) ? $result[0] : null;
    }
}
