<?php

namespace App\Models\Frontend\UjianMdta;

use CodeIgniter\Model;

class UjianMdtaJadwalModel extends Model
{
    protected $table      = 'tbl_ujian_mdta_jadwal';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'IdTpq',
        'TargetTpq',
        'IdPaket',
        'IdKelas',
        'IdTahunAjaran',
        'NamaUjian',
        'Semester',
        'TipeJadwal',
        'IdJadwalAsal',
        'AttemptKe',
        'TanggalMulai',
        'TanggalSelesai',
        'DurasiMenit',
        'JumlahSoal',
        'AcakSoal',
        'AcakJawaban',
        'JumlahPilihan',
        'ModeSoal',
        'NilaiMinimum',
        'BolehRemedial',
        'MaksRemedial',
        'TanggalMulaiRemedial',
        'TanggalSelesaiRemedial',
        'StatusRemedial',
        'TampilKunciJawaban',
        'TampilSoalJawaban',
        'Status',
        'IsArchived',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureSchemaUpdated();
    }

    private function ensureSchemaUpdated()
    {
        try {
            if (!$this->db->fieldExists('ModeSoal', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN ModeSoal VARCHAR(20) DEFAULT 'campuran' AFTER JumlahPilihan");
            }
            if (!$this->db->fieldExists('TargetTpq', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN TargetTpq TEXT DEFAULT NULL AFTER IdTpq");
            }
            if (!$this->db->fieldExists('IsArchived', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN IsArchived TINYINT(1) DEFAULT 0 AFTER Status");
            }
            $this->db->query("ALTER TABLE {$this->table} MODIFY COLUMN Semester VARCHAR(50) DEFAULT '1'");
        } catch (\Throwable $e) {
            // Ignore error if already updated
        }
    }

    /**
     * Ambil daftar jadwal dengan info Paket Soal dan Kelas.
     *
     * @param string      $idTpq
     * @param string|null $status Filter status
     * @param string|null $idTahunAjaran Filter Tahun Ajaran
     * @param int|null    $isArchived Filter Arsip (0=aktif, 1=arsip, null=semua)
     * @return array
     */
    public function getJadwalWithKelas(string $idTpq, ?string $status = null, ?string $idTahunAjaran = null, ?int $isArchived = 0): array
    {
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.*, p.NamaPaket, p.IdMateri, m.NamaMateri, k.NamaKelas');
        $builder->join('tbl_ujian_mdta_paket p', 'p.id = j.IdPaket', 'left');
        $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = p.IdMateri', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = j.IdKelas', 'left');

        // Filter kepemilikan pembuat jadwal (Strict per IdTpq: Admin hanya melihat jadwal IdTpq = '0', Lembaga hanya melihat jadwal IdTpq miliknya)
        $builder->where('j.IdTpq', (string)$idTpq);


        if ($status) {
            $builder->where('j.Status', $status);
        }

        if (!empty($idTahunAjaran) && $idTahunAjaran !== 'all') {
            $builder->where('j.IdTahunAjaran', $idTahunAjaran);
        }

        if ($isArchived !== null) {
            $builder->where('COALESCE(j.IsArchived, 0)', $isArchived);
        }

        $builder->orderBy('j.TanggalMulai', 'DESC');
        return $builder->get()->getResultArray();
    }


    /**
     * Ambil jadwal aktif yang bisa diikuti oleh santri berdasarkan kelasnya.
     * Termasuk jadwal utama dan jadwal remedial.
     *
     * @param string $idKelas    Kelas santri
     * @param string $idTpq     TPQ santri
     * @return array
     */
    public function getJadwalAktifBySantri(string $idKelas, string $idTpq = ''): array
    {
        $now     = date('Y-m-d H:i:s');
        $builder = $this->db->table($this->table . ' j');
        $builder->select('j.*, p.NamaPaket, p.IdMateri, m.NamaMateri, k.NamaKelas, p.SkalaNilai');
        $builder->join('tbl_ujian_mdta_paket p', 'p.id = j.IdPaket', 'left');
        $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = p.IdMateri', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = j.IdKelas', 'left');

        if (!empty($idKelas)) {
            $builder->where('j.IdKelas', $idKelas);
        }

        if (!empty($idTpq) && $idTpq !== '0') {
            $idTpqEsc = $this->db->escape($idTpq);
            $builder->groupStart();
                // Jadwal yang dibuat khusus oleh TPQ tempat santri terdaftar
                $builder->where('j.IdTpq', $idTpq);
                // ATAU Jadwal Ujian buatan Admin Pusat (IdTpq = '0')
                $builder->orGroupStart();
                    $builder->where('j.IdTpq', '0');
                    $builder->groupStart();
                        $builder->where('j.TargetTpq', null);
                        $builder->orWhere('j.TargetTpq', '');
                        $builder->orWhere('j.TargetTpq', 'all');
                        $builder->orWhere("FIND_IN_SET({$idTpqEsc}, REPLACE(j.TargetTpq, ' ', '')) > 0");
                    $builder->groupEnd();
                $builder->groupEnd();
            $builder->groupEnd();
        }

        $builder->whereIn('j.Status', ['aktif', 'pause']);
        $builder->where('j.TanggalSelesai >=', $now);
        $builder->orderBy('j.TipeJadwal', 'ASC');
        $builder->orderBy('j.TanggalMulai', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Ambil daftar santri yang gagal (NilaiAkhir < NilaiMinimum) pada jadwal tertentu.
     * Digunakan untuk form pembuatan jadwal remedial.
     *
     * @param int $idJadwal
     * @return array
     */
    public function getSantriGagal(int $idJadwal): array
    {
        $jadwal = $this->find($idJadwal);
        if (!$jadwal) {
            return [];
        }

        $builder = $this->db->table('tbl_ujian_mdta_sesi sesi');
        $builder->select('sesi.IdSantri, sesi.NilaiAkhir, sesi.AttemptKe, sb.NamaSantri');
        $builder->join('tbl_santri_baru sb', 'sb.IdSantri = sesi.IdSantri', 'left');
        $builder->where('sesi.IdJadwal', $idJadwal);
        $builder->where('sesi.StatusSesi', 'selesai');
        $builder->where('sesi.NilaiAkhir <', $jadwal['NilaiMinimum']);
        // Hanya ambil attempt terakhir per santri
        $builder->groupBy('sesi.IdSantri');
        $builder->having('sesi.AttemptKe = MAX(sesi.AttemptKe)');
        return $builder->get()->getResultArray();
    }

    /**
     * Buat jadwal remedial baru berdasarkan jadwal asal.
     * Menyalin semua konfigurasi kecuali tanggal dan attempt.
     *
     * @param int    $idJadwalAsal
     * @param string $tanggalMulai
     * @param string $tanggalSelesai
     * @return int|false ID jadwal remedial baru
     */
    public function duplikasiJadwalRemedial(int $idJadwalAsal, string $tanggalMulai, string $tanggalSelesai)
    {
        $jadwal = $this->find($idJadwalAsal);
        if (!$jadwal) {
            return false;
        }

        $attemptKe = $jadwal['AttemptKe'] + 1;

        $this->insert([
            'IdTpq'          => $jadwal['IdTpq'],
            'IdPaket'        => $jadwal['IdPaket'],
            'IdKelas'        => $jadwal['IdKelas'],
            'IdTahunAjaran'  => $jadwal['IdTahunAjaran'],
            'NamaUjian'      => $jadwal['NamaUjian'] . ' (Remedial ke-' . ($attemptKe - 1) . ')',
            'Semester'       => $jadwal['Semester'],
            'TipeJadwal'     => 'remedial',
            'IdJadwalAsal'   => $idJadwalAsal,
            'AttemptKe'      => $attemptKe,
            'TanggalMulai'   => $tanggalMulai,
            'TanggalSelesai' => $tanggalSelesai,
            'DurasiMenit'    => $jadwal['DurasiMenit'],
            'JumlahSoal'     => $jadwal['JumlahSoal'],
            'AcakSoal'       => $jadwal['AcakSoal'],
            'AcakJawaban'    => $jadwal['AcakJawaban'],
            'NilaiMinimum'   => $jadwal['NilaiMinimum'],
            'BolehRemedial'  => $jadwal['BolehRemedial'],
            'MaksRemedial'   => $jadwal['MaksRemedial'],
            'Status'         => 'draft',
        ]);

        return $this->getInsertID() ?: false;
    }

    /**
     * Cek apakah jumlah soal yang dikonfigurasi tidak melebihi soal aktif di paket.
     *
     * @param int $idPaket
     * @param int $jumlahSoal
     * @return bool true jika valid
     */
    public function validasiJumlahSoal(int $idPaket, int $jumlahSoal): bool
    {
        $soalModel = new UjianMdtaSoalModel();
        $total     = $soalModel->countByPaket($idPaket);
        return $jumlahSoal <= $total;
    }
}
