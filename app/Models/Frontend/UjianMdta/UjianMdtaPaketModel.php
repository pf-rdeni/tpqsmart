<?php

namespace App\Models\Frontend\UjianMdta;

use CodeIgniter\Model;

class UjianMdtaPaketModel extends Model
{
    protected $table      = 'tbl_ujian_mdta_paket';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'IdTpq',
        'IdMda',
        'IsGlobal',
        'IdKelas',
        'IdMateri',
        'IdTahunAjaran',
        'NamaPaket',
        'ModeJawaban',
        'AcakSoalDefault',
        'AcakJawabanDefault',
        'SkalaNilai',
        'SkorTidakMenjawab',
        'PetunjukPengerjaan',
        'Status',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->ensureIsGlobalColumn();
    }

    private function ensureIsGlobalColumn()
    {
        try {
            if (!$this->db->fieldExists('IsGlobal', $this->table)) {
                $this->db->query("ALTER TABLE {$this->table} ADD COLUMN IsGlobal TINYINT(1) DEFAULT 0 AFTER IdTpq");
            }
        } catch (\Throwable $e) {
            // Ignore error
        }
    }

    /**
     * Ambil semua paket soal yang bisa diakses oleh sebuah TPQ.
     * Termasuk paket global milik Admin (IdTpq = '0') dan paket milik TPQ sendiri.
     *
     * @param string $idTpq
     * @param string|null $idKelas   Filter by kelas (opsional)
     * @param string|null $idMateri  Filter by materi (opsional)
     * @param string      $status    Filter by status
     * @return array
     */
    public function getPaketByTpq(string $idTpq, ?string $idKelas = null, ?string $idMateri = null, string $status = 'aktif', ?int $currentPaketId = null): array
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, m.NamaMateri, k.NamaKelas, 
            (SELECT COUNT(*) FROM tbl_ujian_mdta_soal s WHERE s.IdPaket = p.id AND s.Status = "aktif") as JumlahSoal,
            (SELECT COUNT(*) FROM tbl_ujian_mdta_soal s WHERE s.IdPaket = p.id AND s.Status = "aktif" AND (s.JenisSoal = "pilihan_ganda" OR s.JenisSoal IS NULL OR s.JenisSoal = "")) as JumlahPG,
            (SELECT COUNT(*) FROM tbl_ujian_mdta_soal s WHERE s.IdPaket = p.id AND s.Status = "aktif" AND s.JenisSoal = "esai") as JumlahEsai');
        $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = p.IdMateri', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = p.IdKelas', 'left');

        // Setiap user (Admin maupun TPQ) hanya melihat paket miliknya sendiri (p.IdTpq = $idTpq) ATAU paket yang dipublikasikan secara Global (p.IsGlobal = 1)
        $builder->groupStart();
        $builder->where('p.IdTpq', $idTpq);
        $builder->orWhere('p.IsGlobal', 1);
        $builder->groupEnd();

        // Hanya tampilkan paket soal yang AKTIF (paket yang diarsipkan / Status != 'aktif' tidak akan muncul)
        if ($status) {
            if ($currentPaketId) {
                $builder->groupStart();
                $builder->where('p.Status', $status);
                $builder->orWhere('p.id', $currentPaketId);
                $builder->groupEnd();
            } else {
                $builder->where('p.Status', $status);
            }
        }
        if ($idKelas) {
            $builder->groupStart();
            $builder->where('p.IdKelas', $idKelas);
            $builder->orWhere('p.IdKelas IS NULL');
            $builder->orWhere('p.IdKelas', '');
            $builder->orWhere('p.IdKelas', '0');
            $builder->groupEnd();
        }
        if ($idMateri) {
            $builder->where('p.IdMateri', $idMateri);
        }

        $builder->orderBy('p.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }


    /**
     * Ambil satu paket beserta info jumlah soal aktif di dalamnya.
     *
     * @param int $id
     * @return array|null
     */
    public function getPaketDetail(int $id): ?array
    {
        $builder = $this->db->table($this->table . ' p');
        $builder->select('p.*, m.NamaMateri, k.NamaKelas,
            (SELECT COUNT(*) FROM tbl_ujian_mdta_soal s WHERE s.IdPaket = p.id AND s.Status = "aktif") as JumlahSoal,
            (SELECT COUNT(*) FROM tbl_ujian_mdta_soal s WHERE s.IdPaket = p.id AND s.Status = "aktif" AND (s.JenisSoal = "pilihan_ganda" OR s.JenisSoal IS NULL OR s.JenisSoal = "")) as JumlahPG,
            (SELECT COUNT(*) FROM tbl_ujian_mdta_soal s WHERE s.IdPaket = p.id AND s.Status = "aktif" AND s.JenisSoal = "esai") as JumlahEsai');
        $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = p.IdMateri', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = p.IdKelas', 'left');
        $builder->where('p.id', $id);
        return $builder->get()->getRowArray();
    }

    /**
     * Duplikasi paket soal beserta semua soal dan pilihannya ke TPQ tertentu.
     * Mengembalikan ID paket baru, atau false jika gagal.
     *
     * @param int $idPaketAsal
     * @param string|null $targetIdTpq
     * @return int|false
     */
    public function duplikasiPaket(int $idPaketAsal, ?string $targetIdTpq = null)
    {
        $paket = $this->find($idPaketAsal);
        if (!$paket) {
            return false;
        }

        // Salin data paket, atur nama dan kepemilikan lokal TPQ
        unset($paket['id'], $paket['created_at'], $paket['updated_at']);
        
        $cleanName = preg_replace('/^(\[Salinan TPQ\]|\[Salinan\]|SALINAN - )\s*/i', '', $paket['NamaPaket']);
        $paket['NamaPaket'] = '[Salinan TPQ] ' . $cleanName;
        $paket['IsGlobal']  = 0;
        if ($targetIdTpq !== null) {
            $paket['IdTpq'] = $targetIdTpq;
        }
        $paket['Status']    = 'aktif';

        $this->insert($paket);
        $idPaketBaru = $this->getInsertID();

        if (!$idPaketBaru) {
            return false;
        }

        // Duplikasi soal-soal aktif dari paket asal
        $soalModel    = new UjianMdtaSoalModel();
        $pilihanModel = new UjianMdtaPilihanModel();

        $daftarSoal = $soalModel->where('IdPaket', $idPaketAsal)->where('Status', 'aktif')->findAll();

        foreach ($daftarSoal as $soal) {
            $idSoalAsal = $soal['id'];
            unset($soal['id'], $soal['created_at'], $soal['updated_at']);
            $soal['IdPaket'] = $idPaketBaru;

            $soalModel->insert($soal);
            $idSoalBaru = $soalModel->getInsertID();

            // Duplikasi pilihan jawaban tiap soal
            $daftarPilihan = $pilihanModel->where('IdSoal', $idSoalAsal)->findAll();
            foreach ($daftarPilihan as $pilihan) {
                unset($pilihan['id'], $pilihan['created_at'], $pilihan['updated_at']);
                $pilihan['IdSoal'] = $idSoalBaru;
                $pilihanModel->insert($pilihan);
            }
        }

        return $idPaketBaru;
    }

    /**
     * Arsipkan paket soal (ubah status menjadi 'arsip').
     *
     * @param int $id
     * @return bool
     */
    public function arsipkan(int $id): bool
    {
        return $this->update($id, ['Status' => 'arsip']);
    }

    /**
     * Restore paket dari arsip ke aktif.
     *
     * @param int $id
     * @return bool
     */
    public function restoreArsip(int $id): bool
    {
        return $this->update($id, ['Status' => 'aktif']);
    }
}
