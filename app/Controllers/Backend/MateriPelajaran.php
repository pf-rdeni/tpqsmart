<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\MateriPelajaranModel;
use App\Models\MateriAlquranModel;
use App\Models\KategoriMateriModel;
use App\Models\TpqModel;


class MateriPelajaran extends BaseController
{
    protected $materiModel;
    protected $materiAlquranModel;
    protected $kategoriMateriModel;
    protected $tpqModel;

    public function __construct()
    {
        $this->materiModel = new MateriPelajaranModel();
        $this->materiAlquranModel = new MateriAlquranModel();
        $this->kategoriMateriModel = new KategoriMateriModel();
        $this->tpqModel = new TpqModel();
    }

    public function index()
    {
        $data['materi'] = $this->materiModel->findAll();
        return view('materipelajaran/index', $data);
    }

    public function create()
    {
        return view('materipelajaran/create');
    }

    public function store()
    {
        try {
            $result = $this->materiModel->save([
                'IdMateri'  => $this->request->getPost('IdMateri'),
                'NamaMateri' => strtoupper($this->request->getPost('NamaMateri')),
                'Kategori' => strtoupper($this->request->getPost('Kategori')),
                'IdTpq' => session()->get('IdTpq')
            ]);

            if (!$result) {
                throw new \Exception('Gagal menyimpan data materi pelajaran');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi pelajaran berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        $data['materi'] = $this->materiModel->find($id);
        return view('materipelajaran/edit', $data);
    }

    public function update($id)
    {
        try {
            $result = $this->materiModel->update($id, [
                'IdMateri'  => $this->request->getPost('IdMateri'),
                'NamaMateri' => strtoupper($this->request->getPost('NamaMateri')),
                'Kategori' => strtoupper($this->request->getPost('Kategori'))
            ]);

            if (!$result) {
                throw new \Exception('Gagal memperbarui data materi pelajaran');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi pelajaran berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $result = $this->materiModel->delete($id);
            if (!$result) {
                throw new \Exception('Gagal menghapus data materi pelajaran');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi pelajaran berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }

    public function showMateriPelajaran()
    {
        // ambil IdTpq dari session
        $IdTpq = session()->get('IdTpq');

        $dataMateriPelajaran = $this->materiModel
        ->select('tbl_materi_pelajaran.*, tpq.NamaTpq')
        ->join('tbl_tpq tpq', 'tpq.IdTpq = tbl_materi_pelajaran.IdTpq', 'left')
        ->where('tbl_materi_pelajaran.IdTpq', $IdTpq)
        ->orWhere('tbl_materi_pelajaran.IdTpq', null)
        ->findAll();

        $kategori = $this->materiModel
            ->select('Kategori')
            ->where('IdTpq', $IdTpq)
            ->orWhere('IdTpq', null)
            ->groupBy('Kategori')
            ->findAll();
        
        $data = [
            'page_title' => 'Data Materi Pelajaran',
            'materiPelajaran' => $dataMateriPelajaran,
            'kategoriPelajaran' => $kategori,
        ];
        return view('backend/materi/daftarMeteriPelajaran', $data);
    }

    public function getLastIdMateri()
    {
        $kategori = $this->request->getJSON()->kategori;

        // Ambil ID Materi terakhir berdasarkan kategori
        $lastId = $this->materiModel
            ->select('IdMateri')
            ->where('Kategori', $kategori)
            ->orderBy('IdMateri', 'DESC')
            ->get()
            ->getRowArray();

        if ($lastId) {
            // Ambil ID terakhir
            $currentId = $lastId['IdMateri'];

            // Pisahkan string dan angka
            preg_match('/([A-Za-z]+)(\d+)/', $currentId, $matches);

            if (count($matches) >= 3) {
                $prefix = $matches[1];  // Bagian huruf (contoh: "SH")
                $number = intval($matches[2]);  // Bagian angka (contoh: "01")

                // Tambah 1 ke angka dan format dengan leading zero
                $nextNumber = str_pad($number + 1, strlen($matches[2]), '0', STR_PAD_LEFT);
                $nextId = $prefix . $nextNumber;
            } else {
                // Jika format tidak sesuai, gunakan format default
                $nextId = $kategori . '01';
            }
        } else {
            // Jika belum ada data, mulai dari 01
            $nextId = $kategori . '01';
        }

        return $this->response->setJSON([
            'success' => true,
            'nextId' => $nextId
        ]);
    }

    /**
     * Get all kategori materi untuk dropdown
     */
    public function getKategoriMateri()
    {
        try {
            $kategori = $this->kategoriMateriModel
                ->where('Status', 'Aktif')
                ->orderBy('IdKategoriMateri', 'ASC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $kategori
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data kategori: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get surah alquran dari tabel tbl_alquran
     */
    public function getSurahAlquran()
    {
        try {
            $db = \Config\Database::connect();

            // Cek apakah tabel tbl_alquran ada
            $tables = $db->listTables();
            if (!in_array('tbl_alquran', $tables)) {
                log_message('error', 'Tabel tbl_alquran tidak ditemukan di database');
                return $this->response->setJSON([
                    'success' => false,
                    'data' => [],
                    'message' => 'Tabel tbl_alquran belum tersedia di database'
                ]);
            }

            // Ambil semua kolom yang mungkin ada di tbl_alquran
            $builder = $db->table('tbl_alquran');

            // Cek kolom yang ada di tabel
            $fields = $db->getFieldData('tbl_alquran');
            $fieldNames = array_column($fields, 'name');

            log_message('debug', 'Kolom di tbl_alquran: ' . json_encode($fieldNames));

            // Cari kolom yang sesuai untuk IdSurah dan NamaSurah
            $idField = null; // Primary key (id)
            $noSurahField = null; // NoSurah untuk display
            $namaSurahField = null;
            $idKategoriField = null;
            $juzField = null;
            $jumlahAyatField = null;

            // Cek berbagai kemungkinan nama kolom
            foreach ($fieldNames as $field) {
                $fieldLower = strtolower($field);
                // Prioritaskan kolom 'id' sebagai primary key
                if ($fieldLower === 'id') {
                    $idField = $field;
                }
                // NoSurah untuk display
                if (in_array($fieldLower, ['nosurah', 'no_surah'])) {
                    $noSurahField = $field;
                }
                if (in_array($fieldLower, ['namasurah', 'nama_surah', 'surah', 'nama', 'name'])) {
                    $namaSurahField = $field;
                }
                if (in_array($fieldLower, ['idkategori', 'id_kategori', 'kategori_id'])) {
                    $idKategoriField = $field;
                }
                if (in_array($fieldLower, ['juz', 'juz_id', 'idjuz', 'id_juz'])) {
                    $juzField = $field;
                }
                if (in_array($fieldLower, ['jumlahayat', 'jumlah_ayat', 'totalayat', 'total_ayat', 'ayat'])) {
                    $jumlahAyatField = $field;
                }
            }

            // Pastikan idField ada, jika tidak cari alternatif
            if (!$idField) {
                // Cari primary key dari field data
                foreach ($fields as $field) {
                    if (isset($field->primary_key) && $field->primary_key == 1) {
                        $idField = $field->name;
                        break;
                    }
                }
                // Jika masih tidak ada, gunakan 'id' sebagai default
                if (!$idField && in_array('id', $fieldNames)) {
                    $idField = 'id';
                }
            }

            // Build select query
            $selectFields = [];
            // IdSurah harus menggunakan primary key (id), bukan NoSurah
            if ($idField) {
                $selectFields[] = $idField . ' as IdSurah';
            } else {
                // Fallback jika tidak ada id
                $selectFields[] = 'id as IdSurah';
            }
            // NoSurah untuk display
            if ($noSurahField) {
                $selectFields[] = $noSurahField . ' as NoSurah';
            }
            if ($namaSurahField) {
                $selectFields[] = $namaSurahField . ' as NamaSurah';
            } else {
                // Jika tidak ada kolom nama, gunakan NoSurah sebagai fallback
                $selectFields[] = ($noSurahField ?: 'id') . ' as NamaSurah';
            }
            if ($idKategoriField) {
                $selectFields[] = $idKategoriField . ' as IdKategori';
            }
            if ($juzField) {
                $selectFields[] = $juzField . ' as Juz';
            }
            if ($jumlahAyatField) {
                $selectFields[] = $jumlahAyatField . ' as JumlahAyat';
            }

            if (empty($selectFields)) {
                // Jika tidak ada kolom yang dikenal, ambil semua
                $builder->select('*');
            } else {
                $builder->select(implode(', ', $selectFields));
            }

            // Order by NoSurah jika ada, jika tidak order by id
            if ($noSurahField) {
                $builder->orderBy($noSurahField, 'ASC');
            } elseif ($idField) {
                $builder->orderBy($idField, 'ASC');
            } elseif ($namaSurahField) {
                $builder->orderBy($namaSurahField, 'ASC');
            }

            $surah = $builder->get()->getResultArray();

            log_message('debug', 'Jumlah surah yang ditemukan: ' . count($surah));
            log_message('debug', 'Data surah sample: ' . json_encode(array_slice($surah, 0, 2)));

            return $this->response->setJSON([
                'success' => true,
                'data' => $surah,
                'count' => count($surah),
                'message' => count($surah) > 0 ? 'Data surah berhasil diambil' : 'Tidak ada data surah ditemukan'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getSurahAlquran: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'data' => [],
                'message' => 'Gagal mengambil data surah: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get list Juz dari tabel tbl_alquran
     */
    public function getListJuz()
    {
        try {
            $db = \Config\Database::connect();

            $tables = $db->listTables();
            if (!in_array('tbl_alquran', $tables)) {
                return $this->response->setJSON([
                    'success' => false,
                    'data' => [],
                    'message' => 'Tabel tbl_alquran belum tersedia di database'
                ]);
            }

            $builder = $db->table('tbl_alquran');
            $fields = $db->getFieldData('tbl_alquran');
            $fieldNames = array_column($fields, 'name');

            // Cari kolom Juz
            $juzField = null;
            foreach ($fieldNames as $field) {
                $fieldLower = strtolower($field);
                if (in_array($fieldLower, ['juz', 'juz_id', 'idjuz', 'id_juz'])) {
                    $juzField = $field;
                    break;
                }
            }

            if (!$juzField) {
                return $this->response->setJSON([
                    'success' => false,
                    'data' => [],
                    'message' => 'Kolom Juz tidak ditemukan di tabel tbl_alquran'
                ]);
            }

            $builder->select($juzField . ' as Juz')
                ->distinct()
                ->orderBy($juzField, 'ASC');

            $juzList = $builder->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $juzList
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getListJuz: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'data' => [],
                'message' => 'Gagal mengambil data juz: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get surah alquran berdasarkan Juz
     */
    public function getSurahAlquranByJuz()
    {
        try {
            $juz = $this->request->getGet('juz') ?? $this->request->getJSON()->juz ?? null;

            if (!$juz) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'data' => [],
                    'message' => 'Parameter Juz harus diisi'
                ]);
            }

            $db = \Config\Database::connect();
            $tables = $db->listTables();

            if (!in_array('tbl_alquran', $tables)) {
                return $this->response->setJSON([
                    'success' => false,
                    'data' => [],
                    'message' => 'Tabel tbl_alquran belum tersedia di database'
                ]);
            }

            $builder = $db->table('tbl_alquran');
            $fields = $db->getFieldData('tbl_alquran');
            $fieldNames = array_column($fields, 'name');

            $idSurahField = null;
            $namaSurahField = null;
            $idKategoriField = null;
            $juzField = null;
            $jumlahAyatField = null;

            foreach ($fieldNames as $field) {
                $fieldLower = strtolower($field);
                if (in_array($fieldLower, ['idsurah', 'id_surah', 'surah_id', 'id', 'nosurah', 'no_surah'])) {
                    $idSurahField = $field;
                }
                if (in_array($fieldLower, ['namasurah', 'nama_surah', 'surah', 'nama', 'name'])) {
                    $namaSurahField = $field;
                }
                if (in_array($fieldLower, ['idkategori', 'id_kategori', 'kategori_id'])) {
                    $idKategoriField = $field;
                }
                if (in_array($fieldLower, ['juz', 'juz_id', 'idjuz', 'id_juz'])) {
                    $juzField = $field;
                }
                if (in_array($fieldLower, ['jumlahayat', 'jumlah_ayat', 'totalayat', 'total_ayat', 'ayat'])) {
                    $jumlahAyatField = $field;
                }
            }

            if (!$juzField) {
                return $this->response->setJSON([
                    'success' => false,
                    'data' => [],
                    'message' => 'Kolom Juz tidak ditemukan di tabel tbl_alquran'
                ]);
            }

            $selectFields = [];
            if ($idSurahField) {
                $selectFields[] = $idSurahField . ' as IdSurah';
            }
            if ($namaSurahField) {
                $selectFields[] = $namaSurahField . ' as NamaSurah';
            } else {
                $selectFields[] = ($idSurahField ?: 'id') . ' as NamaSurah';
            }
            if ($idKategoriField) {
                $selectFields[] = $idKategoriField . ' as IdKategori';
            }
            if ($juzField) {
                $selectFields[] = $juzField . ' as Juz';
            }
            if ($jumlahAyatField) {
                $selectFields[] = $jumlahAyatField . ' as JumlahAyat';
            }

            $builder->select(implode(', ', $selectFields))
                ->where($juzField, $juz);

            // Order by nomor surah (IdSurah) untuk urutan yang benar
            if ($idSurahField) {
                $builder->orderBy($idSurahField, 'ASC');
            } elseif ($namaSurahField) {
                $builder->orderBy($namaSurahField, 'ASC');
            }

            $surah = $builder->get()->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => $surah,
                'count' => count($surah)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getSurahAlquranByJuz: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'data' => [],
                'message' => 'Gagal mengambil data surah: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get last IdMateri berdasarkan IdKategori
     */
    public function getLastIdMateriByKategori()
    {
        try {
            $data = $this->request->getJSON();
            $idKategori = $data->IdKategori ?? null;

            if (!$idKategori) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'IdKategori harus diisi'
                ]);
            }

            // Ambil kategori untuk mendapatkan prefix
            $kategori = $this->kategoriMateriModel
                ->where('IdKategoriMateri', $idKategori)
                ->first();

            if (!$kategori) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan'
                ]);
            }

            // Ambil ID Materi terakhir berdasarkan IdKategori
            $lastId = $this->materiModel
                ->select('IdMateri')
                ->where('IdKategori', $idKategori)
                ->orderBy('IdMateri', 'DESC')
                ->get()
                ->getRowArray();

            if ($lastId) {
                $currentId = $lastId['IdMateri'];
                // Pisahkan string dan angka
                preg_match('/([A-Za-z]+)(\d+)/', $currentId, $matches);

                if (count($matches) >= 3) {
                    $prefix = $matches[1];
                    $number = intval($matches[2]);
                    $nextNumber = str_pad($number + 1, strlen($matches[2]), '0', STR_PAD_LEFT);
                    $nextId = $prefix . $nextNumber;
                } else {
                    $nextId = $idKategori . '01';
                }
            } else {
                $nextId = $idKategori . '01';
            }

            return $this->response->setJSON([
                'success' => true,
                'nextId' => $nextId
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil ID Materi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get detail surah berdasarkan IdSurah
     */
    public function getDetailSurah()
    {
        try {
            $data = $this->request->getJSON();
            $idSurah = $data->IdSurah ?? null;

            if (!$idSurah) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'IdSurah harus diisi'
                ]);
            }

            $db = \Config\Database::connect();
            $tables = $db->listTables();

            if (!in_array('tbl_alquran', $tables)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Tabel tbl_alquran belum tersedia'
                ]);
            }

            $builder = $db->table('tbl_alquran');
            // Gunakan id (primary key) untuk mencari surah, bukan NoSurah
            $builder->select('id as IdSurah, NoSurah, NamaSurah, IdKategori')
                ->where('id', $idSurah);

            $surah = $builder->get()->getRowArray();

            if (!$surah) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Surah tidak ditemukan'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $surah
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil detail surah: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Store materi alquran (untuk kategori KM002 dan KM004)
     */
    public function storeMateriAlquran()
    {
        try {
            $data = $this->request->getJSON();

            // Validasi input manual
            $errors = [];
            if (empty($data->IdKategori)) {
                $errors[] = 'IdKategori harus diisi';
            }
            if (empty($data->IdSurah)) {
                $errors[] = 'IdSurah harus diisi';
            }
            if (empty($data->AyatAwal) || !is_numeric($data->AyatAwal)) {
                $errors[] = 'AyatAwal harus diisi dan berupa angka';
            }
            if (!empty($data->AyatAkhir) && !is_numeric($data->AyatAkhir)) {
                $errors[] = 'AyatAkhir harus berupa angka';
            }
            if (empty($data->IdTpq)) {
                $errors[] = 'IdTpq harus diisi';
            }

            if (!empty($errors)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $errors
                ]);
            }

            // Ambil detail surah
            $db = \Config\Database::connect();
            $tables = $db->listTables();

            if (!in_array('tbl_alquran', $tables)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Tabel tbl_alquran belum tersedia di database'
                ]);
            }

            $builder = $db->table('tbl_alquran');
            // Gunakan id (primary key) untuk mencari surah, bukan NoSurah
            $builder->select('id as IdSurah, NoSurah, Surah, JumlahAyat')
                ->where('id', $data->IdSurah);
            $surah = $builder->get()->getRowArray();

            if (!$surah) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Surah tidak ditemukan'
                ]);
            }

            // Generate IdMateri
            $lastId = $this->materiModel
                ->select('IdMateri')
                ->where('IdKategori', $data->IdKategori)
                ->orderBy('IdMateri', 'DESC')
                ->get()
                ->getRowArray();

            if ($lastId) {
                $currentId = $lastId['IdMateri'];
                preg_match('/([A-Za-z]+)(\d+)/', $currentId, $matches);
                if (count($matches) >= 3) {
                    $prefix = $matches[1];
                    $number = intval($matches[2]);
                    $nextNumber = str_pad($number + 1, strlen($matches[2]), '0', STR_PAD_LEFT);
                    $idMateri = $prefix . $nextNumber;
                } else {
                    $idMateri = $data->IdKategori . '01';
                }
            } else {
                $idMateri = $data->IdKategori . '01';
            }

            // Generate NamaMateri
            $namaMateri = strtoupper($surah['Surah']);
            // Jika ada Awal Ayat dan Akhir Ayat: "AL-BAQARAH 23-50"
            // Jika tidak ada akhir ayat: "AL-BAQARAH" (hanya nama surah)
            if (!empty($data->AyatAkhir) && $data->AyatAkhir > $data->AyatAwal) {
                $namaMateri .= ' ' . $data->AyatAwal . '-' . $data->AyatAkhir;
            }
            // Jika tidak ada akhir ayat atau sama dengan awal, hanya nama surah saja

            // Ambil Nama Kategori dari tbl_kategori_materi
            $kategori = $this->kategoriMateriModel
                ->where('IdKategoriMateri', $data->IdKategori)
                ->first();

            $namaKategori = '';
            if ($kategori && isset($kategori['NamaKategoriMateri'])) {
                $namaKategori = $kategori['NamaKategoriMateri'];
            }

            // Mulai transaksi
            $db->transStart();

            // Simpan ke tbl_materi_pelajaran
            $materiData = [
                'IdMateri' => $idMateri,
                'IdKategori' => $data->IdKategori,
                'IdTpq' => $data->IdTpq == '0' ? null : $data->IdTpq,
                'NamaMateri' => strtoupper($namaMateri),
                'Kategori' => $namaKategori
            ];

            $materiId = $this->materiModel->insert($materiData);
            if (!$materiId) {
                throw new \Exception('Gagal menyimpan data materi pelajaran');
            }

            // Cek apakah IdMateri sudah ada di tbl_materi_alquran (harus unik)
            $existingMateri = $this->materiAlquranModel
                ->where('IdMateri', $idMateri)
                ->first();

            if ($existingMateri) {
                $db->transRollback();
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'IdMateri sudah ada di tabel tbl_materi_alquran. IdMateri harus unik.',
                    'duplicate_id' => $idMateri
                ]);
            }

            // Simpan ke tbl_materi_alquran
            $alquranData = [
                'IdMateri' => $idMateri,
                'IdKategoriMateri' => $data->IdKategori,
                'IdTpq' => ($data->IdTpq == '0' || empty($data->IdTpq)) ? null : $data->IdTpq,
                'IdSurah' => $data->IdSurah,
                'AyatMulai' => $data->AyatAwal,
                'AyatAkhir' => !empty($data->AyatAkhir) ? $data->AyatAkhir : null,
                'NamaSurah' => strtoupper($surah['Surah'])
            ];

            $alquranId = $this->materiAlquranModel->insert($alquranData);
            if (!$alquranId) {
                $errors = $this->materiAlquranModel->errors();
                $errorMessage = 'Gagal menyimpan data materi alquran';
                if (!empty($errors)) {
                    $errorMessage .= ': ' . implode(', ', array_values($errors));
                } else {
                    $errorMessage .= '. Silakan cek log untuk detail error.';
                }
                log_message('error', 'Error insert materi alquran: ' . json_encode($alquranData));
                log_message('error', 'Validation errors: ' . json_encode($errors));
                throw new \Exception($errorMessage);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Data materi alquran berhasil disimpan',
                'data' => [
                    'IdMateri' => $idMateri,
                    'NamaMateri' => $namaMateri
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all TPQ untuk dropdown
     */
    public function getAllTpq()
    {
        try {
            $sessionIdTpq = session()->get('IdTpq');
            $isOperator = in_groups('Operator');
            $isAdmin = in_groups('Admin') || (empty($sessionIdTpq) || $sessionIdTpq == 0);

            $db = \Config\Database::connect();
            $builder = $db->table('tbl_tpq');
            $builder->select('IdTpq, NamaTpq, KelurahanDesa')
                ->orderBy('NamaTpq', 'ASC');

            // Jika Operator, hanya ambil TPQ mereka sendiri
            if ($isOperator && $sessionIdTpq && $sessionIdTpq != 0) {
                $builder->where('IdTpq', $sessionIdTpq);
            }

            $tpqList = $builder->get()->getResultArray();

            $data = [];

            // Jika Admin, tambahkan opsi Default
            if ($isAdmin) {
                $data[] = [
                    'IdTpq' => '0',
                    'NamaTpq' => 'Default (FKPQ)',
                    'KelurahanDesa' => ''
                ];
            }

            foreach ($tpqList as $tpq) {
                $data[] = [
                    'IdTpq' => $tpq['IdTpq'],
                    'NamaTpq' => $tpq['NamaTpq'],
                    'KelurahanDesa' => $tpq['KelurahanDesa'] ?? ''
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'isOperator' => $isOperator,
                'isAdmin' => $isAdmin,
                'sessionIdTpq' => $sessionIdTpq
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data TPQ: ' . $e->getMessage()
            ]);
        }
    }
}
