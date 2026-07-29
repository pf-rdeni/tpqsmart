<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\Frontend\UjianMdta\UjianMdtaPaketModel;
use App\Models\Frontend\UjianMdta\UjianMdtaSoalModel;
use App\Models\Frontend\UjianMdta\UjianMdtaPilihanModel;
use App\Models\Frontend\UjianMdta\UjianMdtaJadwalModel;
use App\Models\Frontend\UjianMdta\UjianMdtaSesiModel;
use App\Models\Frontend\UjianMdta\UjianMdtaJawabanModel;

class UjianMdta extends BaseController
{
    protected $paketModel;
    protected $soalModel;
    protected $pilihanModel;
    protected $jadwalModel;
    protected $sesiModel;
    protected $jawabanModel;
    protected $db;

    protected $idTpq;

    public function __construct()
    {
        helper(['url', 'text']);
        $this->db           = \Config\Database::connect();
        $this->paketModel   = new UjianMdtaPaketModel();
        $this->soalModel    = new UjianMdtaSoalModel();
        $this->pilihanModel = new UjianMdtaPilihanModel();
        $this->jadwalModel  = new UjianMdtaJadwalModel();
        $this->sesiModel    = new UjianMdtaSesiModel();
        $this->jawabanModel = new UjianMdtaJawabanModel();
        $this->idTpq        = session()->get('IdTpq') ?? '0';
    }

    // ================================================================
    // DASHBOARD
    // ================================================================

    public function index()
    {
        $idTpq = $this->idTpq;

        // Statistik ringkas
        $totalPaket  = $this->paketModel->where('IdTpq', $idTpq)->where('Status', 'aktif')->countAllResults();
        $totalSoal   = $this->db->table('tbl_ujian_mdta_soal s')
            ->join('tbl_ujian_mdta_paket p', 'p.id = s.IdPaket')
            ->where('p.IdTpq', $idTpq)
            ->where('s.Status', 'aktif')
            ->countAllResults();
        $jadwalAktif = $this->jadwalModel->where('IdTpq', $idTpq)->where('Status', 'aktif')->countAllResults();

        $data = [
            'page_title'  => 'Ujian MDTA',
            'totalPaket'  => $totalPaket,
            'totalSoal'   => $totalSoal,
            'jadwalAktif' => $jadwalAktif,
        ];
        return view('backend/ujianMdta/index', $data);
    }

    // ================================================================
    // PAKET SOAL
    // ================================================================

    public function daftarPaket()
    {
        $idTpq    = $this->idTpq;
        $idKelas  = $this->request->getGet('kelas');
        $idMateri = $this->request->getGet('materi') ?: null;
        $keyword  = $this->request->getGet('keyword');

        $paketList = $this->paketModel->getPaketByTpq($idTpq, $idKelas, $idMateri, 'aktif');

        foreach ($paketList as &$p) {
            $p['isGlobalReadOnly'] = $this->isGlobalReadOnly($p);
        }

        // Filter keyword
        if ($keyword) {
            $paketList = array_filter($paketList, function ($p) use ($keyword) {
                return stripos($p['NamaPaket'], $keyword) !== false;
            });
        }

        // Daftar kelas dan materi khusus MDTA/MDA untuk filter dropdown
        $kelasList  = $this->db->table('tbl_kelas')->get()->getResultArray();
        $materiList = $this->getMateriMdaList();

        $data = [
            'page_title' => 'Paket Soal — Ujian MDTA',
            'paketList'  => $paketList,
            'kelasList'  => $kelasList,
            'materiList' => $materiList,
            'filter'     => ['kelas' => $idKelas, 'materi' => $idMateri, 'keyword' => $keyword],
        ];
        return view('backend/ujianMdta/paket/index', $data);
    }

    public function arsipPaket()
    {
        $idTpq    = $this->idTpq;
        $paketList = $this->paketModel->getPaketByTpq($idTpq, null, null, 'arsip');
        $data = [
            'page_title' => 'Arsip Paket Soal — Ujian MDTA',
            'paketList'  => $paketList,
        ];
        return view('backend/ujianMdta/paket/arsip', $data);
    }

    public function createPaket()
    {
        $idTpq      = $this->idTpq;
        $kelasList  = $this->db->table('tbl_kelas')->get()->getResultArray();
        $materiList = $this->getMateriMdaList();
        $taList     = session()->get('IdTahunAjaranList') ?? [];

        $data = [
            'page_title'  => 'Buat Paket Soal Baru',
            'kelasList'   => $kelasList,
            'materiList'  => $materiList,
            'taList'      => $taList,
            'isUserAdmin' => ((string)$this->idTpq === '0'),
            'validation'  => \Config\Services::validation(),
        ];
        return view('backend/ujianMdta/paket/form_paket', $data);
    }

    public function savePaket()
    {
        $rules = [
            'NamaPaket'     => 'required|max_length[150]',
            'IdKelas'       => 'required',
            'IdMateri'      => 'required',
            'IdTahunAjaran' => 'required',
            'ModeJawaban'   => 'required|in_list[ABCD,ABCDE]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $isGlobal = (int)($this->request->getPost('IsGlobal') ?? 0);

        $this->paketModel->insert([
            'IdTpq'               => $this->idTpq,
            'IsGlobal'            => $isGlobal,
            'IdKelas'             => $this->request->getPost('IdKelas'),
            'IdMateri'            => $this->request->getPost('IdMateri'),
            'IdTahunAjaran'       => $this->request->getPost('IdTahunAjaran'),
            'NamaPaket'           => $this->request->getPost('NamaPaket'),
            'ModeJawaban'         => $this->request->getPost('ModeJawaban'),
            'AcakSoalDefault'     => $this->request->getPost('AcakSoalDefault') ? 1 : 0,
            'AcakJawabanDefault'  => $this->request->getPost('AcakJawabanDefault') ? 1 : 0,
            'SkalaNilai'          => (int)($this->request->getPost('SkalaNilai') ?? 100),
            'SkorTidakMenjawab'   => (float)($this->request->getPost('SkorTidakMenjawab') ?? 0),
            'PetunjukPengerjaan'  => $this->request->getPost('PetunjukPengerjaan') ?? null,
            'Status'              => 'aktif',
        ]);

        $idPaket = $this->paketModel->getInsertID();
        return redirect()->to(base_url("backend/ujian-mdta/paket/{$idPaket}/soal"))
                         ->with('success', 'Paket soal berhasil dibuat. Silakan tambahkan soal.');
    }

    /**
     * Cek apakah sebuah paket bersifat Read-Only (Hanya Dibaca & Hanya Bisa Diduplikasi).
     * Suatu paket bersifat Read-Only bagi user yang login jika user tersebut BUKAN pembuat asli paket.
     */
    private function isGlobalReadOnly(?array $paket): bool
    {
        if (!$paket) {
            return false;
        }

        $idTpqPaket    = (string)($paket['IdTpq'] ?? '0');
        $loggedInIdTpq = (string)$this->idTpq;

        // Jika user yang login bukan pembuat paket ini, maka paket bersifat Read-Only (Hanya Bisa Diduplikasi)
        return ($idTpqPaket !== $loggedInIdTpq);
    }

    public function editPaket(int $id)
    {
        $paket = $this->paketModel->getPaketDetail($id);
        if (!$paket) {
            return redirect()->to(base_url('backend/ujian-mdta/paket'))->with('error', 'Paket tidak ditemukan.');
        }

        if ($this->isGlobalReadOnly($paket)) {
            return redirect()->to(base_url('backend/ujian-mdta/paket'))
                             ->with('error', 'Paket Soal Global / Pusat bersifat Read-Only (Hanya Dibaca). Silakan Duplikasi Paket ini ke TPQ Anda jika ingin menyesuaikan.');
        }

        $idTpq      = $this->idTpq;
        $kelasList  = $this->db->table('tbl_kelas')->get()->getResultArray();
        $materiList = $this->db->table('tbl_materi_pelajaran')->get()->getResultArray();
        $taList     = session()->get('IdTahunAjaranList') ?? [];

        $data = [
            'page_title'  => 'Edit Paket Soal',
            'paket'       => $paket,
            'kelasList'   => $kelasList,
            'materiList'  => $materiList,
            'taList'      => $taList,
            'isUserAdmin' => ((string)$this->idTpq === '0'),
            'validation'  => \Config\Services::validation(),
        ];
        return view('backend/ujianMdta/paket/form_paket', $data);
    }

    public function updatePaket(int $id)
    {
        $paket = $this->paketModel->find($id);
        if ($this->isGlobalReadOnly($paket)) {
            return redirect()->to(base_url('backend/ujian-mdta/paket'))
                             ->with('error', 'Paket Soal Global / Pusat bersifat Read-Only (Hanya Dibaca).');
        }

        $rules = [
            'NamaPaket'   => 'required|max_length[150]',
            'IdKelas'     => 'required',
            'IdMateri'    => 'required',
            'ModeJawaban' => 'required|in_list[ABCD,ABCDE]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'IdKelas'             => $this->request->getPost('IdKelas'),
            'IdMateri'            => $this->request->getPost('IdMateri'),
            'IdTahunAjaran'       => $this->request->getPost('IdTahunAjaran'),
            'NamaPaket'           => $this->request->getPost('NamaPaket'),
            'ModeJawaban'         => $this->request->getPost('ModeJawaban'),
            'AcakSoalDefault'     => $this->request->getPost('AcakSoalDefault') ? 1 : 0,
            'AcakJawabanDefault'  => $this->request->getPost('AcakJawabanDefault') ? 1 : 0,
            'SkalaNilai'          => (int)($this->request->getPost('SkalaNilai') ?? 100),
            'SkorTidakMenjawab'   => (float)($this->request->getPost('SkorTidakMenjawab') ?? 0),
            'PetunjukPengerjaan'  => $this->request->getPost('PetunjukPengerjaan') ?? null,
            'IsGlobal'            => (int)($this->request->getPost('IsGlobal') ?? 0),
        ];

        $this->paketModel->update($id, $updateData);

        return redirect()->to(base_url('backend/ujian-mdta/paket'))
                         ->with('success', 'Paket soal berhasil diperbarui.');
    }

    public function deletePaket(int $id)
    {
        $paket = $this->paketModel->find($id);
        if ($this->isGlobalReadOnly($paket)) {
            return redirect()->to(base_url('backend/ujian-mdta/paket'))
                             ->with('error', 'Paket Soal Global / Pusat tidak dapat dihapus oleh TPQ.');
        }

        // Cek apakah paket sedang digunakan di jadwal
        $digunakan = $this->jadwalModel->where('IdPaket', $id)->countAllResults();
        if ($digunakan > 0) {
            return redirect()->back()->with('error', 'Paket tidak bisa dihapus karena sedang digunakan di jadwal ujian.');
        }
        $this->paketModel->update($id, ['Status' => 'nonaktif']);
        return redirect()->to(base_url('backend/ujian-mdta/paket'))->with('success', 'Paket soal berhasil dihapus.');
    }

    public function duplikasiPaket(int $id)
    {
        $idPaketBaru = $this->paketModel->duplikasiPaket($id, $this->idTpq);
        if (!$idPaketBaru) {
            return redirect()->back()->with('error', 'Gagal menduplikasi paket soal.');
        }
        return redirect()->to(base_url("backend/ujian-mdta/paket/{$idPaketBaru}/soal"))
                         ->with('success', 'Paket Soal Global berhasil diduplikasi menjadi Paket Lokal TPQ Anda. Anda sekarang dapat menambah, mengedit, atau mengkustomisasi soal.');
    }

    public function arsipkanPaket(int $id)
    {
        $this->paketModel->arsipkan($id);
        return redirect()->back()->with('success', 'Paket soal dipindahkan ke arsip.');
    }

    public function restorePaket(int $id)
    {
        $this->paketModel->restoreArsip($id);
        return redirect()->back()->with('success', 'Paket soal berhasil direstore dari arsip.');
    }

    public function previewPaket(int $id)
    {
        $paket = $this->paketModel->getPaketDetail($id);
        if (!$paket) {
            return redirect()->to(base_url('backend/ujian-mdta/paket'))->with('error', 'Paket tidak ditemukan.');
        }
        $soalList = $this->soalModel->getSoalByPaket($id);
        foreach ($soalList as &$soal) {
            $soal['pilihan'] = $this->pilihanModel->getPilihanBySoal((int)$soal['id']);
        }
        $data = [
            'page_title' => 'Preview: ' . $paket['NamaPaket'],
            'paket'      => $paket,
            'soalList'   => $soalList,
        ];
        return view('backend/ujianMdta/paket/preview', $data);
    }

    public function exportPaketPdf(int $id)
    {
        return redirect()->to(base_url('backend/ujian-mdta/paket'))->with('error', 'Fitur ekspor PDF dinonaktifkan. Silakan gunakan fitur ekspor MS Word.');
    }

    /**
     * Konversi gambar URL/relatif menjadi Base64 Data URI & hitung dimensi piksel presisi berdasarkan setelan editor untuk MS Word & Dompdf.
     */
    private function convertImagesForPdf(string $html): string
    {
        // 1. Pindahkan atribut style width dari parent wrapper (<figure style="width: 25%"> / <p style="width: 25%">) ke tag <img>
        $html = preg_replace_callback('/<(?:figure|p|div|span)[^>]*style=["\'][^"\']*width\s*:\s*(\d+(?:\.\d+)?)\s*%[^"\']*["\'][^>]*>\s*<img([^>]+)>/i', function($wrapperMatch) {
            $pct = (float)$wrapperMatch[1];
            $imgTag = '<img' . $wrapperMatch[2] . '>';
            if (strpos($imgTag, 'style=') !== false) {
                $imgTag = preg_replace('/style=["\']([^"\']*)["\']/i', 'style="$1; width: ' . $pct . '%;"', $imgTag);
            } else {
                $imgTag = str_replace('<img ', '<img style="width: ' . $pct . '%;" ', $imgTag);
            }
            return $imgTag;
        }, $html);

        // 2. Olah seluruh tag <img> dengan penanganan kontekstual presisi
        return preg_replace_callback('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', function ($matches) {
            $fullTag = $matches[0];
            $src     = $matches[1];
            
            // Dapatkan path relatif file dari URL
            $parsedUrl    = parse_url($src, PHP_URL_PATH);
            $relativePath = ltrim($parsedUrl, '/');

            // Coba berbagai kemungkinan path lokal di server
            $possiblePaths = [
                FCPATH . $relativePath,
                FCPATH . preg_replace('/^\/?tpqsmart\//', '', $relativePath),
                FCPATH . 'uploads/' . basename($relativePath),
                FCPATH . 'uploads/ujian_mdta/soal/' . basename($relativePath),
            ];

            $localPath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path) && is_file($path)) {
                    $localPath = $path;
                    break;
                }
            }

            if ($localPath) {
                $type = pathinfo($localPath, PATHINFO_EXTENSION);
                if (in_array(strtolower($type), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $data   = file_get_contents($localPath);
                    $base64 = 'data:image/' . ($type === 'jpg' ? 'jpeg' : $type) . ';base64,' . base64_encode($data);

                    // Ambil dimensi fisik asli gambar
                    $imgSize = @getimagesize($localPath);
                    $origW   = ($imgSize && $imgSize[0] > 0) ? $imgSize[0] : 400;
                    $origH   = ($imgSize && $imgSize[1] > 0) ? $imgSize[1] : 300;

                    // Tentukan skala target
                    $targetW = null;

                    // Ekstrak persentase jika ada (25%, 50%, 75%, 100%)
                    if (preg_match('/width\s*:\s*(\d+(?:\.\d+)?)\s*%/i', $fullTag, $pMatch)) {
                        $pct = (float)$pMatch[1];
                        if ($pct <= 30) {
                            $targetW = 75;  // 25% scale -> 75px
                        } else if ($pct <= 60) {
                            $targetW = 120; // 50% scale -> 120px
                        } else if ($pct <= 80) {
                            $targetW = 160; // 75% scale -> 160px
                        } else {
                            $targetW = 180; // 100% scale -> 180px max
                        }
                    } else if (preg_match('/width\s*:\s*(\d+(?:\.\d+)?)\s*px/i', $fullTag, $pxMatch)) {
                        $targetW = min(round((float)$pxMatch[1]), 180);
                    }

                    // Jika tidak ada persentase khusus (default), batasi maksimal 90px agar gambar ringkas & mungil
                    if (!$targetW) {
                        $targetW = min($origW, 90);
                    }

                    // Batas mutlak maksimum lebar gambar = 180px
                    if ($targetW > 180) {
                        $targetW = 180;
                    }

                    // Hitung tinggi piksel proporsional
                    $targetH = round($origH * ($targetW / $origW));
                    if ($targetH < 1) $targetH = 50;

                    // Ganti URL src dengan Base64
                    $tag = str_replace($src, $base64, $fullTag);

                    // Hapus atribut width/height/style lama
                    $tag = preg_replace('/\s+(width|height)=["\'][^"\']*["\']/i', '', $tag);
                    $tag = preg_replace('/\s+style=["\'][^"\']*["\']/i', '', $tag);

                    // Injeksi atribut HTML & inline style piksel eksplisit untuk MS Word & mPDF
                    $tag = str_replace('<img ', '<img width="' . $targetW . '" height="' . $targetH . '" style="width:' . $targetW . 'px !important; height:' . $targetH . 'px !important; max-width:100%;" ', $tag);

                    return $tag;
                }
            }
            return $fullTag;
        }, $html);
    }

    public function exportPaketWord(int $id)
    {
        $paket = $this->paketModel->getPaketDetail($id);
        if (!$paket) {
            return redirect()->to(base_url('backend/ujian-mdta/paket'))->with('error', 'Paket soal tidak ditemukan.');
        }

        $soalList = $this->soalModel->getSoalByPaket($id);
        foreach ($soalList as &$soal) {
            $soal['pilihan'] = $this->pilihanModel->getPilihanBySoal((int)$soal['id']);
        }

        $mode = $this->request->getGet('mode') ?? 'soal';
        $filename = 'Paket_Soal_' . url_title($paket['NamaPaket'], '-', true) . '_' . $mode . '.doc';

        $html = view('backend/ujianMdta/paket/export_word', [
            'paket'    => $paket,
            'soalList' => $soalList,
            'mode'     => $mode,
        ]);

        // Konversi gambar ke Base64 Data URI agar tampil di MS Word
        $html = $this->convertImagesForPdf($html);

        return $this->response
            ->setHeader('Content-Type', 'application/msword; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->setHeader('Pragma', 'public')
            ->setBody($html);
    }

    // ================================================================
    // SOAL DALAM PAKET
    // ================================================================

    public function daftarSoal(int $idPaket)
    {
        $paket = $this->paketModel->getPaketDetail($idPaket);
        if (!$paket) {
            return redirect()->to(base_url('backend/ujian-mdta/paket'))->with('error', 'Paket tidak ditemukan.');
        }

        $soalList = $this->soalModel->getSoalByPaket($idPaket);
        foreach ($soalList as &$soal) {
            $soal['pilihan'] = $this->pilihanModel->getPilihanBySoal((int)$soal['id']);
        }

        $data = [
            'page_title'       => 'Daftar Soal — ' . $paket['NamaPaket'],
            'paket'            => $paket,
            'soalList'         => $soalList,
            'isGlobalReadOnly' => $this->isGlobalReadOnly($paket),
        ];
        return view('backend/ujianMdta/soal/index', $data);
    }

    public function createSoal(int $idPaket)
    {
        $paket = $this->paketModel->getPaketDetail($idPaket);
        if (!$paket) {
            return redirect()->to(base_url('backend/ujian-mdta/paket'))->with('error', 'Paket tidak ditemukan.');
        }

        if ($this->isGlobalReadOnly($paket)) {
            return redirect()->to(base_url("backend/ujian-mdta/paket/{$idPaket}/soal"))
                             ->with('error', 'Paket Soal Global / Pusat bersifat Read-Only (Hanya Dibaca). Silakan Duplikasi Paket ini ke TPQ Anda jika ingin menambah soal.');
        }

        $nomorBerikut = $this->soalModel->getNextNomor($idPaket);
        $data = [
            'page_title'   => 'Tambah Soal — ' . $paket['NamaPaket'],
            'paket'        => $paket,
            'soal'         => null,
            'pilihanList'  => [],
            'nomorSoal'    => $nomorBerikut,
            'validation'   => \Config\Services::validation(),
        ];
        return view('backend/ujianMdta/soal/form_soal', $data);
    }

    public function saveSoal(int $idPaket)
    {
        $rules = [
            'UraianSoal' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $jenisSoal = $this->request->getPost('JenisSoal') ?? 'pilihan_ganda';

        // Simpan soal
        $this->soalModel->insert([
            'IdPaket'          => $idPaket,
            'NomorSoal'        => (int)($this->request->getPost('NomorSoal') ?? $this->soalModel->getNextNomor($idPaket)),
            'JenisSoal'        => $jenisSoal,
            'UraianSoal'       => $this->request->getPost('UraianSoal'),
            'TingkatKesulitan' => $this->request->getPost('TingkatKesulitan') ?? 'sedang',
            'AcakSoal'         => $this->request->getPost('AcakSoal') ? 1 : 0,
            'TimeLimitSoal'    => $this->request->getPost('TimeLimitSoal') ?: null,
            'Pembahasan'       => $this->request->getPost('Pembahasan') ?: null,
            'IndikatorCapaian' => $this->request->getPost('IndikatorCapaian') ?: null,
            'Status'           => 'aktif',
        ]);
        $idSoal = $this->soalModel->getInsertID();

        // Simpan pilihan jawaban jika pilihan ganda
        if ($jenisSoal === 'pilihan_ganda') {
            $pilihanData  = $this->request->getPost('pilihan');
            $jawabanBenar = $this->request->getPost('jawaban_benar');
            if (!empty($pilihanData)) {
                $pilihanArray = [];
                foreach ($pilihanData as $huruf => $teks) {
                    $pilihanArray[] = [
                        'HurufPilihan' => $huruf,
                        'TeksPilihan'  => $teks,
                        'IsBenar'      => ($huruf === $jawabanBenar) ? 1 : 0,
                    ];
                }
                $this->pilihanModel->saveAllPilihan((int)$idSoal, $pilihanArray);
            }
        }

        return redirect()->to(base_url("backend/ujian-mdta/paket/{$idPaket}/soal"))
                         ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function editSoal(int $id)
    {
        $soal = $this->soalModel->getSoalWithPilihan($id);
        if (!$soal) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan.');
        }
        $paket = $this->paketModel->getPaketDetail((int)$soal['IdPaket']);
        $data = [
            'page_title'  => 'Edit Soal',
            'paket'       => $paket,
            'soal'        => $soal,
            'pilihanList' => $soal['pilihan'],
            'nomorSoal'   => $soal['NomorSoal'],
            'validation'  => \Config\Services::validation(),
        ];
        return view('backend/ujianMdta/soal/form_soal', $data);
    }

    public function updateSoal(int $id)
    {
        $soal = $this->soalModel->find($id);
        if (!$soal) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan.');
        }

        $jenisSoal = $this->request->getPost('JenisSoal') ?? 'pilihan_ganda';
        $this->soalModel->update($id, [
            'NomorSoal'        => (int)($this->request->getPost('NomorSoal') ?? $soal['NomorSoal']),
            'JenisSoal'        => $jenisSoal,
            'UraianSoal'       => $this->request->getPost('UraianSoal'),
            'TingkatKesulitan' => $this->request->getPost('TingkatKesulitan') ?? 'sedang',
            'AcakSoal'         => $this->request->getPost('AcakSoal') ? 1 : 0,
            'TimeLimitSoal'    => $this->request->getPost('TimeLimitSoal') ?: null,
            'Pembahasan'       => $this->request->getPost('Pembahasan') ?: null,
            'IndikatorCapaian' => $this->request->getPost('IndikatorCapaian') ?: null,
        ]);

        // Update pilihan jawaban jika pilihan ganda
        if ($jenisSoal === 'pilihan_ganda') {
            $pilihanData  = $this->request->getPost('pilihan');
            $jawabanBenar = $this->request->getPost('jawaban_benar');
            if (!empty($pilihanData)) {
                $pilihanArray = [];
                foreach ($pilihanData as $huruf => $teks) {
                    $pilihanArray[] = [
                        'HurufPilihan' => $huruf,
                        'TeksPilihan'  => $teks,
                        'IsBenar'      => ($huruf === $jawabanBenar) ? 1 : 0,
                    ];
                }
                $this->pilihanModel->saveAllPilihan($id, $pilihanArray);
            }
        }

        return redirect()->to(base_url("backend/ujian-mdta/paket/{$soal['IdPaket']}/soal"))
                         ->with('success', 'Soal berhasil diperbarui.');
    }

    public function deleteSoal(int $id)
    {
        $soal = $this->soalModel->find($id);
        if (!$soal) {
            return $this->response->setJSON(['success' => false, 'message' => 'Soal tidak ditemukan.']);
        }
        $idPaket = $soal['IdPaket'];
        $this->soalModel->update($id, ['Status' => 'nonaktif']);
        $this->soalModel->reorderNomor((int)$idPaket);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true]);
        }
        return redirect()->to(base_url("backend/ujian-mdta/paket/{$idPaket}/soal"))
                         ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Duplikasi / penggandaan butir soal pada paket yang sama.
     */
    public function duplikasiSoal(int $id)
    {
        $soal = $this->soalModel->find($id);
        if (!$soal) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Soal tidak ditemukan.']);
            }
            return redirect()->back()->with('error', 'Soal tidak ditemukan.');
        }

        $idPaket   = (int)$soal['IdPaket'];
        $nextNomor = $this->soalModel->getNextNomor($idPaket);

        // 1. Copy data soal
        $newSoalData = [
            'IdPaket'          => $idPaket,
            'NomorSoal'        => $nextNomor,
            'JenisSoal'        => $soal['JenisSoal'] ?? 'pilihan_ganda',
            'UraianSoal'       => $soal['UraianSoal'] ?? '',
            'TingkatKesulitan' => $soal['TingkatKesulitan'] ?? 'sedang',
            'AcakSoal'         => $soal['AcakSoal'] ?? 1,
            'TimeLimitSoal'    => $soal['TimeLimitSoal'] ?? null,
            'Pembahasan'       => $soal['Pembahasan'] ?? null,
            'IndikatorCapaian' => $soal['IndikatorCapaian'] ?? null,
            'Status'           => 'aktif',
        ];

        $this->soalModel->insert($newSoalData);
        $newIdSoal = $this->soalModel->getInsertID();

        // 2. Copy pilihan jawaban jika pilihan ganda
        if (($soal['JenisSoal'] ?? 'pilihan_ganda') === 'pilihan_ganda') {
            $pilihanList = $this->pilihanModel->getPilihanBySoal($id);
            if (!empty($pilihanList)) {
                $pilihanArray = [];
                foreach ($pilihanList as $p) {
                    $pilihanArray[] = [
                        'HurufPilihan' => $p['HurufPilihan'],
                        'TeksPilihan'  => $p['TeksPilihan'],
                        'IsBenar'      => $p['IsBenar'],
                    ];
                }
                $this->pilihanModel->saveAllPilihan((int)$newIdSoal, $pilihanArray);
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'message'   => "Soal No. {$soal['NomorSoal']} berhasil diduplikasi menjadi Soal No. {$nextNomor}.",
                'nextNomor' => $nextNomor,
                'newId'     => $newIdSoal
            ]);
        }

        return redirect()->to(base_url("backend/ujian-mdta/paket/{$idPaket}/soal"))
                         ->with('success', "Soal No. {$soal['NomorSoal']} berhasil diduplikasi menjadi Soal No. {$nextNomor}.");
    }

    /**
     * Kosongkan / nonaktifkan seluruh soal dalam paket ini.
     */
    public function kosongkanSoal(int $idPaket)
    {
        $paket = $this->paketModel->find($idPaket);
        if (!$paket) {
            return redirect()->back()->with('error', 'Paket soal tidak ditemukan.');
        }

        $this->soalModel->where('IdPaket', $idPaket)->set(['Status' => 'nonaktif'])->update();

        return redirect()->to(base_url("backend/ujian-mdta/paket/{$idPaket}/soal"))
                         ->with('success', 'Seluruh soal dalam paket ini berhasil dikosongkan.');
    }

    /**
     * Upload gambar dari CKEditor — return URL gambar sesuai format CKEditor callback.
     */
    public function uploadGambarSoal()
    {
        $file = $this->request->getFile('upload');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['error' => ['message' => 'File tidak valid.']]);
        }

        $ext       = $file->getClientExtension();
        $allowed   = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($ext), $allowed)) {
            return $this->response->setJSON(['error' => ['message' => 'Format file tidak didukung.']]);
        }

        $newName  = $file->getRandomName();
        $uploadDir = FCPATH . 'uploads/ujian_mdta/soal/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $file->move($uploadDir, $newName);

        return $this->response->setJSON([
            'url' => base_url("uploads/ujian_mdta/soal/{$newName}"),
        ]);
    }

    /**
     * Upload file audio soal.
     */
    public function uploadAudioSoal()
    {
        $file = $this->request->getFile('audio');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File audio tidak valid.']);
        }

        $ext     = $file->getClientExtension();
        $allowed = ['mp3', 'ogg', 'wav'];
        if (!in_array(strtolower($ext), $allowed)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Format audio tidak didukung (mp3/ogg/wav).']);
        }

        $newName   = $file->getRandomName();
        $uploadDir = FCPATH . 'uploads/ujian_mdta/audio/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $file->move($uploadDir, $newName);

        return $this->response->setJSON([
            'success'  => true,
            'filename' => $newName,
            'url'      => base_url("uploads/ujian_mdta/audio/{$newName}"),
        ]);
    }

    // ================================================================
    // AJAX HELPERS
    // ================================================================

    public function getKelasOptions()
    {
        $idTpq  = $this->request->getGet('idTpq') ?? $this->idTpq;
        $kelas  = $this->db->table('tbl_kelas')->get()->getResultArray();
        return $this->response->setJSON($kelas);
    }

    public function getPaketOptions()
    {
        $idTpq    = $this->request->getGet('idTpq') ?? $this->idTpq;
        $idKelas  = $this->request->getGet('idKelas');
        $idMateri = $this->request->getGet('idMateri') ?: null;

        $paketList = $this->paketModel->getPaketByTpq($idTpq, $idKelas, $idMateri, 'aktif');
        return $this->response->setJSON($paketList);
    }

    // ================================================================
    // JADWAL UJIAN (Phase 3)
    // ================================================================

    public function daftarJadwal()
    {
        $idTpq         = (string)($this->idTpq ?? '0');
        $isAdmin       = ($idTpq === '0');
        $sessionTa     = session()->get('IdTahunAjaran') ?? '2025-2026';
        
        $filterTa      = $this->request->getGet('ta');
        $filterArsip   = $this->request->getGet('arsip') ?? '0';

        // Penentuan Tahun Ajaran Efektif
        if ($isAdmin) {
            // Admin: Tampilkan semua tahun ajaran jika tidak ada filter spesifik dari URL
            $effectiveTa = ($filterTa !== null) ? $filterTa : 'all';
        } else {
            // Operator: default mengacu ke Tahun Ajaran aktif di Session / Sidebar
            $effectiveTa = ($filterTa !== null) ? $filterTa : $sessionTa;
        }

        $isArchivedVal = ($filterArsip === '1') ? 1 : (($filterArsip === 'all') ? null : 0);

        $jadwalList = $this->jadwalModel->getJadwalWithKelas($idTpq, null, $effectiveTa, $isArchivedVal);

        // Hitung info status & hak remedial per jadwal
        foreach ($jadwalList as &$j) {
            $idJadwal = (int)$j['id'];
            $nilaiMin = (float)($j['NilaiMinimum'] ?? 0);

            // Ambil sesi terakhir per santri di jadwal ini
            $builder = $this->db->table('tbl_ujian_mdta_sesi s1');
            $builder->select('s1.id, s1.IdSantri, s1.AttemptKe, s1.NilaiAkhir, s1.StatusSesi, s1.IsRemedialAllowed');
            $builder->where('s1.IdJadwal', $idJadwal);
            $builder->where("s1.id = (SELECT MAX(s2.id) FROM tbl_ujian_mdta_sesi s2 WHERE s2.IdSantri = s1.IdSantri AND s2.IdJadwal = {$idJadwal})");
            $latestSesi = $builder->get()->getResultArray();

            $maxAttempt       = 1;
            $jmlPerluRemedial = 0;
            $jmlRemedialAktif = 0;

            foreach ($latestSesi as $s) {
                $att = (int)($s['AttemptKe'] ?? 1);
                if ($att > $maxAttempt) {
                    $maxAttempt = $att;
                }

                $nilai = $s['NilaiAkhir'] !== null ? (float)$s['NilaiAkhir'] : null;
                $st    = strtolower(trim($s['StatusSesi'] ?? ''));

                if (in_array($st, ['selesai', 'timeout']) && $nilai !== null && $nilai < $nilaiMin) {
                    $jmlPerluRemedial++;
                    if ((int)($s['IsRemedialAllowed'] ?? 0) === 1) {
                        $jmlRemedialAktif++;
                    }
                }
            }

            $j['max_attempt']           = $maxAttempt;
            $j['target_remedial_ke']    = $maxAttempt; // Misal Remedial ke-1 jika attempt=1
            $j['jml_perlu_remedial']    = $jmlPerluRemedial;
            $j['jml_remedial_aktif']    = $jmlRemedialAktif;
            $j['is_remedial_ready']     = ($j['BolehRemedial'] == 1 && $jmlPerluRemedial > 0);
            $j['is_remedial_all_active'] = ($jmlPerluRemedial > 0 && $jmlRemedialAktif >= $jmlPerluRemedial);
            $j['total_sesi']            = $this->sesiModel->where('IdJadwal', $idJadwal)->countAllResults();

            // Construct remedial subrows
            $remedialSubrows = [];
            $statusRemedial  = strtolower(trim($j['StatusRemedial'] ?? 'nonaktif'));

            $maxAttemptSesi = $this->sesiModel->where('IdJadwal', $idJadwal)->selectMax('AttemptKe')->first();
            $maxAttemptVal  = (int)($maxAttemptSesi['AttemptKe'] ?? 1);
            $totalRemedialCount = max(0, $maxAttemptVal - 1);
            if ($totalRemedialCount < 1 && ($statusRemedial === 'aktif' || !empty($j['TanggalMulaiRemedial']) || $jmlRemedialAktif > 0)) {
                $totalRemedialCount = 1;
            }
            if ($jmlRemedialAktif > 0 && $totalRemedialCount < $maxAttempt) {
                $totalRemedialCount = $maxAttempt;
            }

            if ($statusRemedial === 'aktif' || $statusRemedial === 'selesai' || !empty($j['TanggalMulaiRemedial']) || $maxAttemptVal > 1 || $jmlRemedialAktif > 0) {
                for ($remKe = 1; $remKe <= $totalRemedialCount; $remKe++) {
                    $attemptSesiRemed = $remKe + 1; // Sesi AttemptKe untuk Remedial ke-remKe

                    // Hitung santri yang sesi TERAKHIRNYA berada di attempt remedial ini dan nilainya < KKM
                    $gagalInRemed = 0;
                    foreach ($latestSesi as $ls) {
                        $lsAtt   = (int)($ls['AttemptKe'] ?? 1);
                        $lsNilai = $ls['NilaiAkhir'] !== null ? (float)$ls['NilaiAkhir'] : null;
                        $lsSt    = strtolower(trim($ls['StatusSesi'] ?? ''));

                        if ($lsAtt === $attemptSesiRemed && in_array($lsSt, ['selesai', 'timeout']) && $lsNilai !== null && $lsNilai < $nilaiMin) {
                            $gagalInRemed++;
                        }
                    }

                    $totalSantriRemed = $this->db->table('tbl_ujian_mdta_sesi')
                        ->where('IdJadwal', $idJadwal)
                        ->where('AttemptKe', $attemptSesiRemed)
                        ->countAllResults();

                    $remedialSubrows[] = [
                        'id_jadwal'           => $idJadwal,
                        'attempt_target'      => $remKe,
                        'next_attempt_target' => $remKe + 1,
                        'nama_remedial'       => "Ujian Remedial Ke-{$remKe}",
                        'tgl_mulai'           => $j['TanggalMulaiRemedial'],
                        'tgl_selesai'         => $j['TanggalSelesaiRemedial'],
                        'status_remedial'     => $statusRemedial !== 'nonaktif' ? $statusRemedial : 'aktif',
                        'jml_santri'          => $totalSantriRemed > 0 ? $totalSantriRemed : $jmlPerluRemedial,
                        'jml_gagal'           => $gagalInRemed,
                        'is_remedial_ready'   => ($j['BolehRemedial'] == 1 && $gagalInRemed > 0),
                    ];
                }
            }

            $j['remedial_subrows'] = $remedialSubrows;
        }
        unset($j);

        // Ambil daftar Tahun Ajaran
        $taList = session()->get('IdTahunAjaranList') ?? [];
        if (empty($taList)) {
            $taRows = $this->db->table('tbl_ujian_mdta_jadwal')->select('IdTahunAjaran')->groupBy('IdTahunAjaran')->get()->getResultArray();
            $taList = array_filter(array_column($taRows, 'IdTahunAjaran'));
        }

        $data = [
            'page_title'    => 'Jadwal Ujian MDTA',
            'menu_open'     => 'ujian-mdta',
            'menu_active'   => 'ujian-mdta-jadwal',
            'jadwalList'    => $jadwalList,
            'isAdmin'       => $isAdmin,
            'sessionTa'     => $sessionTa,
            'taList'        => $taList,
            'filter'        => [
                'ta'        => $effectiveTa,
                'arsip'     => $filterArsip,
            ],
        ];

        return view('backend/ujianMdta/jadwal/index', $data);
    }

    /**
     * Toggle status arsip jadwal (Arsipkan / Buka Arsip)
     */
    public function archiveJadwal(int $id)
    {
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jadwal tidak ditemukan.']);
            }
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        // Cek hak akses jika bukan admin
        if ((string)$this->idTpq !== '0' && (string)$jadwal['IdTpq'] !== (string)$this->idTpq) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.']);
            }
            return redirect()->back()->with('error', 'Anda tidak berhak mengarsip jadwal ini.');
        }

        $newArchived = ((int)($jadwal['IsArchived'] ?? 0) === 1) ? 0 : 1;
        $this->jadwalModel->update($id, ['IsArchived' => $newArchived]);

        $statusMsg = $newArchived === 1 ? 'Jadwal berhasil diarsip.' : 'Jadwal berhasil dikeluarkan dari arsip.';

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'    => true,
                'message'    => $statusMsg,
                'isArchived' => $newArchived,
            ]);
        }

        return redirect()->back()->with('success', $statusMsg);
    }


    /**
     * Ambil daftar IdTpq yang memiliki setting MDA_S1_ApakahMemilikiLembagaMDATA = true / 1
     */
    private function getMdaTpqIds(): array
    {
        $toolsRows = $this->db->table('tbl_tools')
            ->where('SettingKey', 'MDA_S1_ApakahMemilikiLembagaMDATA')
            ->whereIn('LOWER(SettingValue)', ['1', 'true', 'yes', 'on', 'enabled', 'active'])
            ->whereNotIn('IdTpq', ['default', '0'])
            ->get()->getResultArray();

        $ids = array_filter(array_column($toolsRows, 'IdTpq'), function ($val) {
            return !empty($val) && $val !== 'default' && $val !== '0';
        });

        return array_values(array_unique($ids));
    }


    private function getTpqListForAdmin(): array
    {
        if ((string)$this->idTpq !== '0') {
            return [];
        }

        $mdaTpqIds = $this->getMdaTpqIds();

        $tableName  = $this->db->tableExists('tbl_tpq') ? 'tbl_tpq' : 'tbl_lembaga';
        $primaryKey = $tableName === 'tbl_tpq' ? 'IdTpq' : 'id';
        $nameField  = $tableName === 'tbl_tpq' ? 'NamaTpq' : 'NamaLembaga';

        $builder = $this->db->table($tableName);
        if (!empty($mdaTpqIds)) {
            $builder->whereIn($primaryKey, $mdaTpqIds);
        } else {
            return [];
        }

        return $builder->orderBy($nameField, 'ASC')->get()->getResultArray();
    }


    private function getKelasListForUser(): array
    {
        $idTpq         = (string)($this->idTpq ?? '0');
        $idGuru        = session()->get('IdGuru');
        $activeRole    = session()->get('active_role');
        $hasAdminGroup = in_groups('Admin');
        $hasOpGroup    = in_groups('Operator');

        $isFullAccess = ($hasAdminGroup || $hasOpGroup) && !in_array($activeRole, ['guru', 'wali_kelas']);

        if (!$isFullAccess && !empty($idGuru)) {
            $idTahunAjaran = session()->get('IdTahunAjaran');
            $builder = $this->db->table('tbl_guru_kelas gk');
            $builder->select('k.IdKelas, k.NamaKelas');
            $builder->join('tbl_kelas k', 'k.IdKelas = gk.IdKelas');
            $builder->where('gk.IdGuru', $idGuru);
            if ($idTpq !== '0') {
                $builder->where('gk.IdTpq', $idTpq);
            }
            if (!empty($idTahunAjaran)) {
                $builder->where('gk.IdTahunAjaran', $idTahunAjaran);
            }
            $builder->groupBy('k.IdKelas');
            $builder->orderBy('k.NamaKelas', 'ASC');
            $kelasList = $builder->get()->getResultArray();

            if (!empty($kelasList)) {
                return $kelasList;
            }

            // Fallback: Check session IdKelas
            $sessionKelasId = session()->get('IdKelas');
            if ($sessionKelasId) {
                return $this->db->table('tbl_kelas')
                    ->where('IdKelas', $sessionKelasId)
                    ->get()->getResultArray();
            }
        }

        return $this->db->table('tbl_kelas')->orderBy('NamaKelas', 'ASC')->get()->getResultArray();
    }

    public function createJadwal()
    {
        $idTpq         = $this->idTpq;
        $kelasList     = $this->getKelasListForUser();
        $firstKelas    = !empty($kelasList) ? ($kelasList[0]['IdKelas'] ?? null) : null;
        $paketList     = $this->paketModel->getPaketByTpq($idTpq, $firstKelas, null, 'aktif');
        $taList        = session()->get('IdTahunAjaranList') ?? [];
        $activeRole    = session()->get('active_role');
        $isFullAccess  = (in_groups('Admin') || in_groups('Operator')) && !in_array($activeRole, ['guru', 'wali_kelas']);

        $data = [
            'page_title'   => 'Buat Jadwal Ujian',
            'kelasList'    => $kelasList,
            'paketList'    => $paketList,
            'taList'       => $taList,
            'tpqList'      => $this->getTpqListForAdmin(),
            'isUserAdmin'  => ((string)$this->idTpq === '0'),
            'isFullAccess' => $isFullAccess,
            'jadwal'       => null,
            'validation'   => \Config\Services::validation(),
        ];
        return view('backend/ujianMdta/jadwal/form_jadwal', $data);
    }

    public function saveJadwal()
    {
        $idPaket    = (int)$this->request->getPost('IdPaket');
        $jumlahSoal = (int)$this->request->getPost('JumlahSoal');

        $rules = [
            'NamaUjian'      => 'required|max_length[150]',
            'IdPaket'        => 'required|is_natural_no_zero',
            'IdKelas'        => 'required',
            'IdTahunAjaran'  => 'required',
            'Semester'       => 'required|in_list[1,2,mingguan,uts,uas]',
            'TanggalMulai'   => 'required',
            'TanggalSelesai' => 'required',
            'DurasiMenit'    => 'required|is_natural_no_zero',
            'JumlahSoal'     => 'required|is_natural_no_zero',
            'NilaiMinimum'   => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi: JumlahSoal tidak boleh melebihi soal aktif di paket
        if (!$this->jadwalModel->validasiJumlahSoal($idPaket, $jumlahSoal)) {
            $totalAktif = $this->soalModel->countByPaket($idPaket);
            return redirect()->back()->withInput()
                ->with('error', "Jumlah soal ({$jumlahSoal}) melebihi total soal aktif di paket ({$totalAktif} soal).");
        }

        $targetTpq = 'all';
        if ((string)$this->idTpq === '0') {
            $targetType = $this->request->getPost('TargetType');
            if ($targetType === 'selected') {
                $selectedTpqArr = $this->request->getPost('TargetTpqList');
                if (is_array($selectedTpqArr) && !empty($selectedTpqArr)) {
                    $targetTpq = implode(',', $selectedTpqArr);
                }
            }
        }

        $this->jadwalModel->insert([
            'IdTpq'              => $this->idTpq,
            'TargetTpq'          => $targetTpq,
            'IdPaket'            => $idPaket,
            'IdKelas'            => $this->request->getPost('IdKelas'),
            'IdTahunAjaran'      => $this->request->getPost('IdTahunAjaran'),
            'NamaUjian'          => $this->request->getPost('NamaUjian'),
            'Semester'           => $this->request->getPost('Semester'),
            'TipeJadwal'         => 'utama',
            'AttemptKe'          => 1,
            'TanggalMulai'       => $this->request->getPost('TanggalMulai'),
            'TanggalSelesai'     => $this->request->getPost('TanggalSelesai'),
            'DurasiMenit'        => $this->request->getPost('DurasiMenit'),
            'JumlahSoal'         => $jumlahSoal,
            'AcakSoal'           => $this->request->getPost('AcakSoal') ? 1 : 0,
            'AcakJawaban'        => $this->request->getPost('AcakJawaban') ? 1 : 0,
            'JumlahPilihan'      => (int)($this->request->getPost('JumlahPilihan') ?? 4),
            'ModeSoal'           => $this->request->getPost('ModeSoal') ?? 'campuran',
            'NilaiMinimum'       => $this->request->getPost('NilaiMinimum'),
            'BolehRemedial'      => $this->request->getPost('BolehRemedial') ? 1 : 0,
            'MaksRemedial'       => (int)($this->request->getPost('MaksRemedial') ?? 1),
            'TampilKunciJawaban' => $this->request->getPost('TampilKunciJawaban') ? 1 : 0,
            'TampilSoalJawaban'  => $this->request->getPost('TampilSoalJawaban') ? 1 : 0,
            'Status'             => $this->request->getPost('Status') ?: 'aktif',
        ]);

        return redirect()->to(base_url('backend/ujian-mdta/jadwal'))
                         ->with('success', 'Jadwal ujian berhasil disimpan.');
    }

    public function editJadwal(int $id)
    {
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            return redirect()->to(base_url('backend/ujian-mdta/jadwal'))
                ->with('error', 'Jadwal tidak ditemukan.');
        }

        $idTpq        = $this->idTpq;
        $kelasList    = $this->getKelasListForUser();
        $paketList    = $this->paketModel->getPaketByTpq($idTpq, $jadwal['IdKelas'], null, 'aktif', (int)$jadwal['IdPaket']);
        $taList       = session()->get('IdTahunAjaranList') ?? [];
        $activeRole   = session()->get('active_role');
        $isFullAccess = (in_groups('Admin') || in_groups('Operator')) && !in_array($activeRole, ['guru', 'wali_kelas']);

        $totalSesi = $this->sesiModel->where('IdJadwal', $id)->countAllResults();

        $data = [
            'page_title'   => 'Edit Jadwal Ujian — ' . $jadwal['NamaUjian'],
            'jadwal'       => $jadwal,
            'totalSesi'    => $totalSesi,
            'kelasList'    => $kelasList,
            'paketList'    => $paketList,
            'taList'       => $taList,
            'tpqList'      => $this->getTpqListForAdmin(),
            'isUserAdmin'  => ((string)$this->idTpq === '0'),
            'isFullAccess' => $isFullAccess,
            'validation'   => \Config\Services::validation(),
        ];
        return view('backend/ujianMdta/jadwal/form_jadwal', $data);
    }

    /**
     * POST — Reset seluruh data santri (sesi/jawaban) pada jadwal lalu buka form edit.
     */
    public function resetAndEditJadwal(int $id)
    {
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            return redirect()->to(base_url('backend/ujian-mdta/jadwal'))
                ->with('error', 'Jadwal tidak ditemukan.');
        }

        // Hapus seluruh data sesi, jawaban, dan soal_sesi untuk jadwal ini
        $sesiList = $this->sesiModel->where('IdJadwal', $id)->findAll();
        foreach ($sesiList as $s) {
            $idSesi = (int)$s['id'];
            $this->db->table('tbl_ujian_mdta_jawaban')->where('IdSesi', $idSesi)->delete();
            $this->db->table('tbl_ujian_mdta_soal_sesi')->where('IdSesi', $idSesi)->delete();
        }
        $this->db->table('tbl_ujian_mdta_sesi')->where('IdJadwal', $id)->delete();

        // Reset status jadwal ke 'draft'
        $this->jadwalModel->update($id, ['Status' => 'draft']);

        return redirect()->to(base_url("backend/ujian-mdta/jadwal/edit/{$id}"))
                         ->with('success', 'Seluruh data sesi & jawaban santri telah dibersihkan secara permanen. Silakan lakukan perubahan jadwal.');
    }

    public function updateJadwal(int $id)
    {
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            return redirect()->to(base_url('backend/ujian-mdta/jadwal'))
                ->with('error', 'Jadwal tidak ditemukan.');
        }

        $idPaket         = (int)$this->request->getPost('IdPaket');
        $jumlahSoal      = (int)$this->request->getPost('JumlahSoal');
        $originalIdPaket = (int)($this->request->getPost('OriginalIdPaket') ?? $jadwal['IdPaket']);
        $originalJmlSoal = (int)($this->request->getPost('OriginalJumlahSoal') ?? $jadwal['JumlahSoal']);
        $confirmReset    = (int)($this->request->getPost('ConfirmResetData') ?? 0);

        if (!$this->jadwalModel->validasiJumlahSoal($idPaket, $jumlahSoal)) {
            $totalAktif = $this->soalModel->countByPaket($idPaket);
            return redirect()->back()->withInput()
                ->with('error', "Jumlah soal ({$jumlahSoal}) melebihi total soal aktif di paket ({$totalAktif} soal).");
        }

        $totalSesi     = $this->sesiModel->where('IdJadwal', $id)->countAllResults();
        $isSoalChanged = ($idPaket !== $originalIdPaket || $jumlahSoal !== $originalJmlSoal);

        $resetNote = " Seluruh data nilai & sesi santri tetap AMAN dan UTUH.";

        if ($totalSesi > 0 && $isSoalChanged) {
            if ($confirmReset === 1) {
                // Hapus data sesi & jawaban santri karena Paket Soal / Jumlah Soal mendasar diubah
                $sesiList = $this->sesiModel->where('IdJadwal', $id)->findAll();
                foreach ($sesiList as $s) {
                    $idSesi = (int)$s['id'];
                    $this->db->table('tbl_ujian_mdta_jawaban')->where('IdSesi', $idSesi)->delete();
                    $this->db->table('tbl_ujian_mdta_soal_sesi')->where('IdSesi', $idSesi)->delete();
                }
                $this->db->table('tbl_ujian_mdta_sesi')->where('IdJadwal', $id)->delete();
                $resetNote = " Data sesi & jawaban santri telah di-reset karena Paket Soal / Jumlah Soal diubah.";
            } else {
                return redirect()->back()->withInput()->with('error', 'Mengubah Paket Soal atau Jumlah Soal pada jadwal yang sudah berjalan memerlukan konfirmasi reset data.');
            }
        }

        $updateJadwalData = [
            'IdPaket'            => $idPaket,
            'IdKelas'            => $this->request->getPost('IdKelas'),
            'IdTahunAjaran'      => $this->request->getPost('IdTahunAjaran'),
            'NamaUjian'          => $this->request->getPost('NamaUjian'),
            'Semester'           => $this->request->getPost('Semester'),
            'TanggalMulai'       => $this->request->getPost('TanggalMulai'),
            'TanggalSelesai'     => $this->request->getPost('TanggalSelesai'),
            'DurasiMenit'        => $this->request->getPost('DurasiMenit'),
            'JumlahSoal'         => $jumlahSoal,
            'AcakSoal'           => $this->request->getPost('AcakSoal') ? 1 : 0,
            'AcakJawaban'        => $this->request->getPost('AcakJawaban') ? 1 : 0,
            'JumlahPilihan'      => (int)($this->request->getPost('JumlahPilihan') ?? 4),
            'ModeSoal'           => $this->request->getPost('ModeSoal') ?? 'campuran',
            'NilaiMinimum'       => $this->request->getPost('NilaiMinimum'),
            'BolehRemedial'      => $this->request->getPost('BolehRemedial') ? 1 : 0,
            'MaksRemedial'       => (int)($this->request->getPost('MaksRemedial') ?? 1),
            'TampilKunciJawaban' => $this->request->getPost('TampilKunciJawaban') ? 1 : 0,
            'TampilSoalJawaban'  => $this->request->getPost('TampilSoalJawaban') ? 1 : 0,
            'Status'             => $this->request->getPost('Status') ?: ($jadwal['Status'] ?? 'aktif'),
        ];

        if ((string)$this->idTpq === '0') {
            $targetType = $this->request->getPost('TargetType');
            if ($targetType === 'selected') {
                $selectedTpqArr = $this->request->getPost('TargetTpqList');
                if (is_array($selectedTpqArr) && !empty($selectedTpqArr)) {
                    $updateJadwalData['TargetTpq'] = implode(',', $selectedTpqArr);
                } else {
                    $updateJadwalData['TargetTpq'] = 'all';
                }
            } else {
                $updateJadwalData['TargetTpq'] = 'all';
            }
        }

        $this->jadwalModel->update($id, $updateJadwalData);

        return redirect()->to(base_url('backend/ujian-mdta/jadwal'))
                         ->with('success', 'Jadwal ujian berhasil diperbarui.' . $resetNote);
    }

    public function deleteJadwal(int $id)
    {
        if (!in_groups(['Admin', 'KepalaTpq', 'Operator'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal || (int)$jadwal['IdTpq'] !== (int)$this->idTpq) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan atau tidak valid.');
        }

        $totalSesi = $this->sesiModel->where('IdJadwal', $id)->countAllResults();

        if ($totalSesi > 0) {
            $confirmInput = trim($this->request->getPost('confirm_delete') ?? '');
            if ($confirmInput !== 'HAPUS') {
                return redirect()->back()->with('error', "Penghapusan dibatalkan. Jadwal memiliki {$totalSesi} sesi data santri. Wajib mengonfirmasi dengan mengetik kata 'HAPUS'.");
            }

            // Hapus cascade data sesi, soal sesi, dan jawaban santri
            $sesiIds = $this->db->table('tbl_ujian_mdta_sesi')
                ->select('id')
                ->where('IdJadwal', $id)
                ->get()
                ->getResultArray();

            if (!empty($sesiIds)) {
                $ids = array_column($sesiIds, 'id');
                $this->db->table('tbl_ujian_mdta_jawaban')->whereIn('IdSesi', $ids)->delete();
                $this->db->table('tbl_ujian_mdta_soal_sesi')->whereIn('IdSesi', $ids)->delete();
                $this->db->table('tbl_ujian_mdta_sesi')->whereIn('id', $ids)->delete();
            }
        }

        // Hapus jadwal permanen
        $this->jadwalModel->delete($id);

        return redirect()->to(base_url('backend/ujian-mdta/jadwal'))
            ->with('success', 'Jadwal ujian beserta seluruh data riwayat peserta berhasil dihapus permanen.');
    }

    public function deleteRemedialSubrow(int $idJadwal, int $attemptTarget)
    {
        if (!in_groups(['Admin', 'KepalaTpq', 'Operator'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal || (int)$jadwal['IdTpq'] !== (int)$this->idTpq) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $attemptSesi = $attemptTarget + 1; // Remedial Ke-1 adalah AttemptKe = 2
        $totalSesiRemed = $this->db->table('tbl_ujian_mdta_sesi')
            ->where('IdJadwal', $idJadwal)
            ->where('AttemptKe', $attemptSesi)
            ->countAllResults();

        if ($totalSesiRemed > 0) {
            $confirmInput = trim($this->request->getPost('confirm_delete') ?? '');
            if ($confirmInput !== 'HAPUS') {
                return redirect()->back()->with('error', "Penghapusan remedial dibatalkan. Sesi ini memiliki {$totalSesiRemed} riwayat pengerjaan santri. Wajib mengonfirmasi dengan mengetik kata 'HAPUS'.");
            }

            // Hapus cascade data sesi remedial attemptTarget
            $sesiIds = $this->db->table('tbl_ujian_mdta_sesi')
                ->select('id')
                ->where('IdJadwal', $idJadwal)
                ->where('AttemptKe', $attemptSesi)
                ->get()
                ->getResultArray();

            if (!empty($sesiIds)) {
                $ids = array_column($sesiIds, 'id');
                $this->db->table('tbl_ujian_mdta_jawaban')->whereIn('IdSesi', $ids)->delete();
                $this->db->table('tbl_ujian_mdta_soal_sesi')->whereIn('IdSesi', $ids)->delete();
                $this->db->table('tbl_ujian_mdta_sesi')->whereIn('id', $ids)->delete();
            }
        }

        // Jika tidak ada lagi sesi remedial tersisa, matikan status remedial di jadwal
        $sisaSesiRemed = $this->db->table('tbl_ujian_mdta_sesi')
            ->where('IdJadwal', $idJadwal)
            ->where('AttemptKe >', 1)
            ->countAllResults();

        if ($sisaSesiRemed === 0 && $attemptTarget <= 1) {
            $this->jadwalModel->update($idJadwal, [
                'StatusRemedial'         => 'nonaktif',
                'TanggalMulaiRemedial'   => null,
                'TanggalSelesaiRemedial' => null,
            ]);
        }

        return redirect()->to(base_url('backend/ujian-mdta/jadwal'))
            ->with('success', "🎉 Sesi Ujian Remedial Ke-{$attemptTarget} berhasil dihapus.");
    }

    public function aktivasiJadwal(int $id)
    {
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        // Validasi soal cukup
        if (!$this->jadwalModel->validasiJumlahSoal((int)$jadwal['IdPaket'], (int)$jadwal['JumlahSoal'])) {
            return redirect()->back()->with('error', 'Tidak bisa mengaktifkan jadwal: jumlah soal aktif di paket tidak mencukupi.');
        }

        $this->jadwalModel->update($id, ['Status' => 'aktif']);
        return redirect()->back()->with('success', 'Jadwal ujian berhasil diaktifkan. Santri kini bisa mengerjakan ujian.');
    }

    public function createJadwalRemedial(int $idJadwalAsal)
    {
        $jadwal = $this->jadwalModel->find($idJadwalAsal);
        if (!$jadwal) {
            return redirect()->to(base_url('backend/ujian-mdta/jadwal'))->with('error', 'Jadwal tidak ditemukan.');
        }

        $santriGagal = $this->jadwalModel->getSantriGagal($idJadwalAsal);
        $data = [
            'page_title'    => 'Buat Jadwal Remedial',
            'jadwalAsal'    => $jadwal,
            'santriGagal'   => $santriGagal,
            'validation'    => \Config\Services::validation(),
        ];
        return view('backend/ujianMdta/jadwal/remedial', $data);
    }

    public function saveJadwalRemedial(int $idJadwalAsal)
    {
        $rules = [
            'TanggalMulai'   => 'required',
            'TanggalSelesai' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idJadwalBaru = $this->jadwalModel->duplikasiJadwalRemedial(
            $idJadwalAsal,
            $this->request->getPost('TanggalMulai'),
            $this->request->getPost('TanggalSelesai')
        );

        if (!$idJadwalBaru) {
            return redirect()->back()->with('error', 'Gagal membuat jadwal remedial.');
        }

        return redirect()->to(base_url('backend/ujian-mdta/jadwal'))
                         ->with('success', 'Jadwal remedial berhasil dibuat dengan status Draft.');
    }

    // ================================================================
    // LAPORAN & MONITOR (Phase 5)
    // ================================================================

    public function monitorUjian(int $idJadwal, ?int $attemptKe = null)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return redirect()->to(base_url('backend/ujian-mdta/jadwal'))->with('error', 'Jadwal tidak ditemukan.');
        }

        $attemptKeFilter = ($attemptKe !== null && (int)$attemptKe > 0) ? (int)$attemptKe : 1;
        $sesiList        = $this->sesiModel->getSesiByJadwal($idJadwal, $attemptKeFilter);
        $namaFilter      = $attemptKeFilter === 1 ? 'Ujian Utama' : 'Ujian Remedial Ke-' . ($attemptKeFilter - 1);

        $data = [
            'page_title'        => 'Live Monitor Ujian',
            'jadwal'            => $jadwal,
            'sesiList'          => $sesiList,
            'attemptKeFilter'   => $attemptKeFilter,
            'namaAttemptFilter' => $namaFilter,
            'isUserAdmin'       => ((string)$this->idTpq === '0'),
        ];
        return view('backend/ujianMdta/jadwal/monitor', $data);
    }

    public function getMonitorDataAjax(int $idJadwal, ?int $attemptKe = null)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jadwal tidak ditemukan.']);
        }

        $attemptKeFilter = ($attemptKe !== null && (int)$attemptKe > 0) ? (int)$attemptKe : 1;
        $sesiList        = $this->sesiModel->getSesiByJadwal($idJadwal, $attemptKeFilter);
        $durasiDetik     = (int)$jadwal['DurasiMenit'] * 60;
        $nowTime         = time();

        foreach ($sesiList as &$s) {
            $s['StatusSesi'] = $s['StatusSesi'] ?? $s['status_sesi'] ?? 'belum';
            $s['idSesi']     = $s['idSesi'] ?? $s['id'] ?? null;
            if (in_array(strtolower($s['StatusSesi']), ['sedang', 'pause'])) {
                $waktuMulai     = !empty($s['WaktuMulai']) ? strtotime($s['WaktuMulai']) : time();
                $totalDurasi    = $durasiDetik + (int)($s['TambahanWaktuDetik'] ?? 0);
                $sisaDetik      = max(0, ($waktuMulai + $totalDurasi) - $nowTime);
                $s['SisaDetik'] = $sisaDetik;
            } else {
                $s['SisaDetik'] = 0;
            }
        }
        unset($s);

        return $this->response->setJSON([
            'success'  => true,
            'sesiList' => $sesiList,
        ]);
    }

    public function tambahWaktuSesi(int $idSesi)
    {
        $sesi = $this->sesiModel->find($idSesi);
        if (!$sesi) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi tidak ditemukan.']);
        }
        $menit  = (int)($this->request->getPost('menit') ?? 5);
        $tambah = $menit * 60;
        $curr   = (int)($sesi['TambahanWaktuDetik'] ?? 0);

        $this->sesiModel->update($idSesi, [
            'TambahanWaktuDetik' => $curr + $tambah
        ]);

        return $this->response->setJSON(['success' => true, 'message' => "Berhasil menambah {$menit} menit untuk santri."]);
    }

    public function pauseSesi(int $idJadwal)
    {
        $idSesi   = $this->request->getPost('idSesi');
        $idSantri = $this->request->getPost('idSantri');

        if (!empty($idSesi) && is_numeric($idSesi) && (int)$idSesi > 0) {
            $this->sesiModel->update((int)$idSesi, [
                'StatusSesi' => 'pause',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else if (!empty($idSantri)) {
            $sesi = $this->sesiModel->where('IdSantri', $idSantri)->where('IdJadwal', $idJadwal)->orderBy('id', 'DESC')->first();
            if ($sesi) {
                $this->sesiModel->update($sesi['id'], [
                    'StatusSesi' => 'pause',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $token = bin2hex(random_bytes(16));
                $this->sesiModel->insert([
                    'IdJadwal'           => $idJadwal,
                    'IdSantri'           => $idSantri,
                    'AttemptKe'          => 1,
                    'TokenSesi'          => $token,
                    'StatusSesi'         => 'pause',
                    'TambahanWaktuDetik' => 0,
                ]);
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Sesi ujian santri berhasil di-pause (jeda).']);
    }

    public function resumeSesi(int $idJadwal)
    {
        $idSesi   = $this->request->getPost('idSesi');
        $idSantri = $this->request->getPost('idSantri');

        if (!empty($idSesi) && is_numeric($idSesi) && (int)$idSesi > 0) {
            $sesi = $this->sesiModel->find((int)$idSesi);
            if ($sesi) {
                $statusTarget = !empty($sesi['WaktuMulai']) ? 'sedang' : 'belum';
                $this->sesiModel->update($sesi['id'], ['StatusSesi' => $statusTarget]);
            }
        } else if (!empty($idSantri)) {
            $sesi = $this->sesiModel->where('IdSantri', $idSantri)->where('IdJadwal', $idJadwal)->orderBy('id', 'DESC')->first();
            if ($sesi) {
                $statusTarget = !empty($sesi['WaktuMulai']) ? 'sedang' : 'belum';
                $this->sesiModel->update($sesi['id'], ['StatusSesi' => $statusTarget]);
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Sesi ujian santri berhasil dilanjutkan.']);
    }

    public function stopSesi(int $idSesi)
    {
        $sesi = $this->sesiModel->find($idSesi);
        if (!$sesi) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi tidak ditemukan.']);
        }

        $this->jawabanModel->evaluasiJawaban($idSesi);
        $this->sesiModel->hitungNilai($idSesi);

        return $this->response->setJSON(['success' => true, 'message' => 'Sesi ujian dihentikan & jawaban berhasil disimpan.']);
    }

    public function resetSesiIndividual(int $idSesi)
    {
        $sesi = $this->sesiModel->find($idSesi);
        if (!$sesi) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi tidak ditemukan.']);
        }

        $this->db->table('tbl_ujian_mdta_jawaban')->where('IdSesi', $idSesi)->delete();
        $this->db->table('tbl_ujian_mdta_soal_sesi')->where('IdSesi', $idSesi)->delete();
        $this->sesiModel->delete($idSesi);

        return $this->response->setJSON(['success' => true, 'message' => 'Sesi ujian santri berhasil di-reset ke awal.']);
    }

    // ================================================================
    // KONTROL MASAL / BULK ACTIONS UNTUK SELURUH SANTRI (MONITOR)
    // ================================================================

    public function tambahWaktuSemua(int $idJadwal)
    {
        $menit  = (int)($this->request->getPost('menit') ?? 5);
        $tambah = $menit * 60;

        $builder = $this->db->table('tbl_ujian_mdta_sesi');
        $builder->where('IdJadwal', $idJadwal);
        $builder->whereIn('StatusSesi', ['sedang', 'pause']);
        $builder->set('TambahanWaktuDetik', "TambahanWaktuDetik + {$tambah}", false);
        $builder->update();

        return $this->response->setJSON(['success' => true, 'message' => "Berhasil menambah {$menit} menit untuk seluruh santri yang aktif."]);
    }

    public function pauseSemua(int $idJadwal)
    {
        $this->db->table('tbl_ujian_mdta_sesi')
                 ->where('IdJadwal', $idJadwal)
                 ->where('StatusSesi', 'sedang')
                 ->update([
                     'StatusSesi' => 'pause',
                     'updated_at' => date('Y-m-d H:i:s')
                 ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Seluruh sesi santri yang sedang mengerjakan berhasil di-pause.']);
    }

    public function resumeSemua(int $idJadwal)
    {
        $sesiList = $this->sesiModel->where('IdJadwal', $idJadwal)
                                   ->where('StatusSesi', 'pause')
                                   ->findAll();

        $now = time();
        foreach ($sesiList as $s) {
            $statusTarget = !empty($s['WaktuMulai']) ? 'sedang' : 'belum';
            $pauseDuration = (!empty($s['updated_at']) && $statusTarget === 'sedang') ? max(0, $now - strtotime($s['updated_at'])) : 0;
            $currTambah = (int)($s['TambahanWaktuDetik'] ?? 0);

            $this->sesiModel->update($s['id'], [
                'TambahanWaktuDetik' => $currTambah + $pauseDuration,
                'StatusSesi'         => $statusTarget,
                'updated_at'         => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Seluruh sesi santri yang di-pause berhasil dilanjutkan.']);
    }

    public function stopSemua(int $idJadwal)
    {
        $sesiList = $this->sesiModel->where('IdJadwal', $idJadwal)
                                   ->whereIn('StatusSesi', ['sedang', 'pause'])
                                   ->findAll();

        foreach ($sesiList as $s) {
            $idSesi = (int)$s['id'];
            $this->jawabanModel->evaluasiJawaban($idSesi);
            $this->sesiModel->hitungNilai($idSesi);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Seluruh sesi santri berhasil dihentikan dan dinilai.']);
    }

    public function resetSemuaMonitor(int $idJadwal)
    {
        $sesiList = $this->sesiModel->where('IdJadwal', $idJadwal)->findAll();
        foreach ($sesiList as $s) {
            $idSesi = (int)$s['id'];
            $this->db->table('tbl_ujian_mdta_jawaban')->where('IdSesi', $idSesi)->delete();
            $this->db->table('tbl_ujian_mdta_soal_sesi')->where('IdSesi', $idSesi)->delete();
        }
        $this->db->table('tbl_ujian_mdta_sesi')->where('IdJadwal', $idJadwal)->delete();

        return $this->response->setJSON(['success' => true, 'message' => 'Seluruh sesi ujian santri berhasil di-reset ke awal.']);
    }

    /**
     * AJAX — Ambil rincian jawaban esai santri untuk dikoreksi/dinilai pengawas.
     */
    public function getDetailJawabanEsai(int $idSesi)
    {
        $sesi = $this->sesiModel->find($idSesi);
        if (!$sesi) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi tidak ditemukan.']);
        }

        $santri = $this->db->table('tbl_santri_baru')->where('IdSantri', $sesi['IdSantri'])->get()->getRowArray();
        $namaSantri = $santri['NamaSantri'] ?? "Santri #{$sesi['IdSantri']}";

        $builder = $this->db->table('tbl_ujian_mdta_jawaban j');
        $builder->select('j.id as idJawaban, j.IdSoal, j.JawabanEsai, j.NilaiEsai, j.IsBenar,
                          soal.NomorSoal, soal.UraianSoal, soal.Pembahasan, soal.JenisSoal');
        $builder->join('tbl_ujian_mdta_soal soal', 'soal.id = j.IdSoal', 'left');
        $builder->where('j.IdSesi', $idSesi);
        $builder->where('soal.JenisSoal', 'esai');
        $builder->orderBy('soal.NomorSoal', 'ASC');

        $listEsai = $builder->get()->getResultArray();

        return $this->response->setJSON([
            'success'    => true,
            'namaSantri' => $namaSantri,
            'idSesi'     => $idSesi,
            'nilaiAkhir' => number_format((float)($sesi['NilaiAkhir'] ?? 0), 2),
            'listEsai'   => $listEsai,
        ]);
    }

    /**
     * AJAX — Simpan penilaian jawaban esai oleh pengawas dan hitung ulang NilaiAkhir.
     */
    public function simpanPenilaianEsai(int $idSesi)
    {
        $sesi = $this->sesiModel->find($idSesi);
        if (!$sesi) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi tidak ditemukan.']);
        }

        $scores = $this->request->getPost('scores');
        if (!empty($scores) && is_array($scores)) {
            foreach ($scores as $idJawaban => $nilai) {
                $numNilai = max(0, min(100, (float)$nilai));
                $isBenar  = $numNilai > 0 ? 1 : 0;
                $this->jawabanModel->update((int)$idJawaban, [
                    'NilaiEsai' => $numNilai,
                    'IsBenar'   => $isBenar,
                ]);
            }
        }

        // Hitung ulang nilai akhir sesi
        $this->sesiModel->hitungNilai($idSesi);
        $sesiUpdated = $this->sesiModel->find($idSesi);

        return $this->response->setJSON([
            'success'    => true,
            'message'    => 'Penilaian jawaban esai berhasil disimpan dan nilai akhir telah dihitung ulang!',
            'nilaiAkhir' => number_format((float)($sesiUpdated['NilaiAkhir'] ?? 0), 2),
        ]);
    }

    /**
     * AJAX — Aktifkan/Izinkan remedial untuk sesi santri tertentu oleh Pengawas.
     */
    public function aktifkanRemedialSesi(int $idSesi)
    {
        $sesi = $this->sesiModel->find($idSesi);
        if (!$sesi) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sesi tidak ditemukan.']);
        }

        $ok = $this->sesiModel->update($idSesi, ['IsRemedialAllowed' => 1]);

        return $this->response->setJSON([
            'success' => $ok,
            'message' => 'Sesi remedial untuk santri berhasil diaktifkan dan diizinkan pengawas!',
        ]);
    }

    /**
     * AJAX — Aktifkan remedial secara masal untuk seluruh santri yang belum lulus di jadwal ini.
     */
    public function aktifkanRemedialSemua(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jadwal tidak ditemukan.']);
        }

        $nilaiMin = (float)($jadwal['NilaiMinimum'] ?? 0);

        $sesiList = $this->sesiModel->where('IdJadwal', $idJadwal)
                                   ->whereIn('StatusSesi', ['selesai', 'timeout'])
                                   ->where('NilaiAkhir <', $nilaiMin)
                                   ->findAll();

        $count = 0;
        foreach ($sesiList as $s) {
            $this->sesiModel->update((int)$s['id'], ['IsRemedialAllowed' => 1]);
            $count++;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Sesi remedial berhasil diaktifkan serentak untuk {$count} santri yang belum mencapai nilai minimum!",
        ]);
    }

    /**
     * POST — Aktifkan & Set Waktu Pelaksanaan Ujian Remedial Tingkat Jadwal.
     */
    public function aktifkanRemedialJadwal(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return redirect()->to(base_url('backend/ujian-mdta/jadwal'))->with('error', 'Jadwal tidak ditemukan.');
        }

        $tglMulai   = $this->request->getPost('TanggalMulaiRemedial');
        $tglSelesai = $this->request->getPost('TanggalSelesaiRemedial');

        $dataUpdate = [
            'StatusRemedial' => 'aktif',
        ];
        if (!empty($tglMulai)) {
            $dataUpdate['TanggalMulaiRemedial'] = date('Y-m-d H:i:s', strtotime($tglMulai));
        }
        if (!empty($tglSelesai)) {
            $dataUpdate['TanggalSelesaiRemedial'] = date('Y-m-d H:i:s', strtotime($tglSelesai));
        }

        $this->jadwalModel->update($idJadwal, $dataUpdate);

        // Izinkan sesi remedial untuk seluruh santri yang nilainya di bawah KKM
        $nilaiMin = (float)($jadwal['NilaiMinimum'] ?? 0);
        $sesiList = $this->sesiModel->where('IdJadwal', $idJadwal)
                                   ->whereIn('StatusSesi', ['selesai', 'timeout'])
                                   ->where('NilaiAkhir <', $nilaiMin)
                                   ->findAll();

        $count = 0;
        foreach ($sesiList as $s) {
            $this->sesiModel->update((int)$s['id'], ['IsRemedialAllowed' => 1]);
            $count++;
        }

        return redirect()->to(base_url('backend/ujian-mdta/jadwal'))
                         ->with('success', "🎉 Sesi Ujian Remedial berhasil diaktifkan & dijadwalkan untuk {$count} santri yang belum lulus!");
    }

    public function laporan(?int $idJadwal = null)
    {
        if ($idJadwal !== null && (int)$idJadwal > 0) {
            return $this->laporanNilai((int)$idJadwal);
        }

        $jadwalList = $this->jadwalModel->getJadwalWithKelas($this->idTpq);

        // Jika hanya ada 1 jadwal, langsung arahkan ke laporan jadwal tersebut
        if (count($jadwalList) === 1) {
            return $this->laporanNilai((int)$jadwalList[0]['id']);
        }

        $data = [
            'page_title'  => 'Daftar Laporan Hasil Ujian MDTA',
            'jadwalList'  => $jadwalList,
            'isUserAdmin' => ((string)$this->idTpq === '0'),
        ];
        return view('backend/ujianMdta/laporan/index', $data);
    }

    public function laporanNilai(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return redirect()->to(base_url('backend/ujian-mdta/jadwal'))->with('error', 'Jadwal tidak ditemukan.');
        }

        $sesiList    = $this->sesiModel->getSesiByJadwal($idJadwal);
        $santriGagal = $this->sesiModel->getSantriPerluRemedial($idJadwal);

        // Hitung total santri aktif di kelas yang dituju jadwal ini
        $totalSantriQuery = $this->db
            ->table('tbl_kelas_santri')
            ->where('IdKelas', $jadwal['IdKelas']);

        if ((string)$jadwal['IdTpq'] === '0') {
            $targetTpqVal = $jadwal['TargetTpq'] ?? 'all';
            if ($targetTpqVal !== 'all' && !empty($targetTpqVal)) {
                $targetTpqArr = array_map('trim', explode(',', $targetTpqVal));
                $totalSantriQuery->whereIn('IdTpq', $targetTpqArr);
            }
        } else {
            $totalSantriQuery->where('IdTpq', $jadwal['IdTpq']);
        }

        $totalSantriKelas = (int) $totalSantriQuery
            ->where('IdTahunAjaran', $jadwal['IdTahunAjaran'])
            ->where('Status', 1)
            ->countAllResults();

        $data = [
            'page_title'        => 'Laporan Nilai — ' . $jadwal['NamaUjian'],
            'jadwal'            => $jadwal,
            'sesiList'          => $sesiList,
            'santriGagal'       => $santriGagal,
            'totalSantriKelas'  => $totalSantriKelas,
            'isUserAdmin'       => ((string)$this->idTpq === '0'),
        ];
        return view('backend/ujianMdta/laporan/nilai', $data);
    }

    public function exportLaporan(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $sesiList = $this->sesiModel->getSesiByJadwal($idJadwal);

        // Generate PDF menggunakan Dompdf
        $html = view('backend/ujianMdta/laporan/export_pdf', [
            'jadwal'   => $jadwal,
            'sesiList' => $sesiList,
        ]);

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'laporan-ujian-' . url_title($jadwal['NamaUjian'], '-', true) . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Reset sesi ujian satu santri pada jadwal tertentu.
     * Menghapus semua jawaban dan soal sesi, lalu menghapus sesi itu sendiri.
     */
    public function resetSesiSantri(int $idJadwal, string $idSantri)
    {
        if (!in_groups(['Admin', 'KepalaTpq', 'Operator'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $db = \Config\Database::connect();

        // Ambil semua id sesi untuk santri + jadwal ini
        $sesiIds = $db->table('tbl_ujian_mdta_sesi')
            ->select('id')
            ->where('IdJadwal', $idJadwal)
            ->where('IdSantri', $idSantri)
            ->get()
            ->getResultArray();

        if (!empty($sesiIds)) {
            $ids = array_column($sesiIds, 'id');
            // Hapus jawaban
            $db->table('tbl_ujian_mdta_jawaban')->whereIn('IdSesi', $ids)->delete();
            // Hapus soal sesi
            $db->table('tbl_ujian_mdta_soal_sesi')->whereIn('IdSesi', $ids)->delete();
            // Hapus sesi
            $db->table('tbl_ujian_mdta_sesi')->whereIn('id', $ids)->delete();
        }

        return redirect()->to(base_url("backend/ujian-mdta/laporan/{$idJadwal}"))
            ->with('success', 'Sesi ujian santri berhasil direset. Santri dapat mengulang ujian.');
    }

    /**
     * Reset seluruh sesi ujian pada jadwal tertentu (semua santri).
     */
    public function resetSemuaSesi(int $idJadwal)
    {
        if (!in_groups(['Admin', 'KepalaTpq', 'Operator'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $db = \Config\Database::connect();

        $sesiIds = $db->table('tbl_ujian_mdta_sesi')
            ->select('id')
            ->where('IdJadwal', $idJadwal)
            ->get()
            ->getResultArray();

        if (!empty($sesiIds)) {
            $ids = array_column($sesiIds, 'id');
            $db->table('tbl_ujian_mdta_jawaban')->whereIn('IdSesi', $ids)->delete();
            $db->table('tbl_ujian_mdta_soal_sesi')->whereIn('IdSesi', $ids)->delete();
            $db->table('tbl_ujian_mdta_sesi')->whereIn('id', $ids)->delete();
        }

        return redirect()->to(base_url("backend/ujian-mdta/laporan/{$idJadwal}"))
            ->with('success', 'Semua sesi ujian berhasil direset. Seluruh santri dapat mengulang ujian.');
    }

    /**
     * Ambil daftar mata pelajaran khusus MDTA / MDA.
     */
    private function getMateriMdaList(): array
    {
        return $this->db->table('tbl_materi_pelajaran')
            ->groupStart()
                ->like('Kategori', 'MDTA')
                ->orLike('Kategori', 'MDA')
                ->orWhereIn('IdKategori', ['KM009', 'KM010'])
            ->groupEnd()
            ->orderBy('NamaMateri', 'ASC')
            ->get()
            ->getResultArray();
    }

    // ================================================================
    // LAPORAN HASIL UJIAN (REFERENSI GAMBAR 1, 2, 3)
    // ================================================================

    /**
     * Halaman Utama Laporan Hasil Ujian (Gambar 2 & Gambar 1)
     */
    public function laporanHasil()
    {
        $sessionTpq       = (string)($this->idTpq ?? '0');
        $idTpqFilter      = $this->request->getGet('IdTpq') ?? '';
        $idKelasFilter    = $this->request->getGet('IdKelas') ?? '';
        $idMateriFilter   = $this->request->getGet('IdMateri') ?? '';
        $ruangFilter      = $this->request->getGet('Ruang') ?? '';
        $startDateFilter  = $this->request->getGet('startDate') ?? '';
        $endDateFilter    = $this->request->getGet('endDate') ?? '';
        $keywordFilter    = $this->request->getGet('keyword') ?? '';

        // Tentukan effectiveTpq (jika login sebagai Lembaga, kunci ke IdTpq Lembaga sendiri)
        $isLembagaUser = ($sessionTpq !== '0');
        if ($isLembagaUser) {
            $effectiveTpq = $sessionTpq;
        } else {
            $effectiveTpq = (!empty($idTpqFilter) && $idTpqFilter !== 'all') ? $idTpqFilter : '0';
        }

        // Query Jadwal Ujian beserta statistik ringkasnya
        $builder = $this->db->table('tbl_ujian_mdta_jadwal j');
        $builder->select('
            j.*,
            p.NamaPaket,
            p.IdMateri,
            m.NamaMateri,
            k.NamaKelas,
            t.NamaTpq
        ');
        $builder->join('tbl_ujian_mdta_paket p', 'p.id = j.IdPaket', 'left');
        $builder->join('tbl_materi_pelajaran m', 'm.IdMateri = p.IdMateri', 'left');
        $builder->join('tbl_kelas k', 'k.IdKelas = j.IdKelas', 'left');
        $builder->join('tbl_tpq t', 't.IdTpq = j.IdTpq', 'left');

        // Filter jadwal berdasarkan pembuat jadwal secara ketat:
        // Admin hanya melihat jadwal buatan Admin (j.IdTpq = '0')
        // Operator Lembaga hanya melihat jadwal buatan Lembaga-nya sendiri (j.IdTpq = $sessionTpq)
        if ($isLembagaUser) {
            $builder->where('j.IdTpq', $sessionTpq);
        } else {
            $builder->where('j.IdTpq', '0');
        }


        if (!empty($idKelasFilter) && $idKelasFilter !== 'all') {
            $builder->where('j.IdKelas', $idKelasFilter);
        }

        if (!empty($idMateriFilter) && $idMateriFilter !== 'all') {
            $builder->where('p.IdMateri', $idMateriFilter);
        }

        if (!empty($startDateFilter)) {
            $builder->where('j.TanggalMulai >=', $startDateFilter . ' 00:00:00');
        }
        if (!empty($endDateFilter)) {
            $builder->where('j.TanggalMulai <=', $endDateFilter . ' 23:59:59');
        }

        if (!empty($keywordFilter)) {
            $builder->groupStart()
                ->like('j.NamaUjian', $keywordFilter)
                ->orLike('p.NamaPaket', $keywordFilter)
                ->orLike('m.NamaMateri', $keywordFilter)
                ->orLike('k.NamaKelas', $keywordFilter)
            ->groupEnd();
        }

        $builder->orderBy('j.TanggalMulai', 'DESC');
        $jadwalList = $builder->get()->getResultArray();

        // Hitung statistik peserta KHUSUS dari Lembaga yang login
        foreach ($jadwalList as &$j) {
            $idJadwal = (int)$j['id'];

            // Query statistik sesi santri dari Lembaga ini
            $sesiBuilder = $this->db->table('tbl_ujian_mdta_sesi sesi');
            $sesiBuilder->select('
                COUNT(sesi.id) as total_sesi,
                MAX(sesi.NilaiAkhir) as nilai_tertinggi,
                AVG(sesi.NilaiAkhir) as nilai_rerata,
                SUM(CASE WHEN sesi.NilaiAkhir >= ' . (float)$j['NilaiMinimum'] . ' THEN 1 ELSE 0 END) as atas_kkm,
                SUM(CASE WHEN sesi.NilaiAkhir < ' . (float)$j['NilaiMinimum'] . ' THEN 1 ELSE 0 END) as bawah_kkm,
                SUM(CASE WHEN sesi.StatusSesi IN ("selesai", "timeout") THEN 1 ELSE 0 END) as mengerjakan
            ');
            $sesiBuilder->where('sesi.IdJadwal', $idJadwal);

            if ($effectiveTpq !== '0') {
                $sesiBuilder->where('sesi.IdTpq', $effectiveTpq);
            }

            $sesiStats = $sesiBuilder->get()->getRowArray();

            // Total santri aktif di kelas untuk Lembaga ini saja
            $totalSantriQuery = $this->db->table('tbl_kelas_santri')
                ->where('IdKelas', $j['IdKelas'])
                ->where('Status', 1);

            if ($effectiveTpq !== '0') {
                $totalSantriQuery->where('IdTpq', $effectiveTpq);
            }

            $totalSantri = $totalSantriQuery->countAllResults();

            $j['stats'] = [
                'Tertinggi'   => $sesiStats['nilai_tertinggi'] !== null ? round((float)$sesiStats['nilai_tertinggi'], 2) : 0,
                'Rerata'      => $sesiStats['nilai_rerata'] !== null ? round((float)$sesiStats['nilai_rerata'], 2) : 0,
                'Total'       => $totalSantri > 0 ? $totalSantri : (int)($sesiStats['total_sesi'] ?? 0),
                'Mengerjakan' => (int)($sesiStats['mengerjakan'] ?? 0),
                'AtasKKM'     => (int)($sesiStats['atas_kkm'] ?? 0),
                'BawahKKM'    => (int)($sesiStats['bawah_kkm'] ?? 0),
            ];
        }
        unset($j);

        // Ambil daftar Lembaga untuk dropdown (jika user Lembaga, hanya tampilkan Lembaganya sendiri; jika Admin, filter yang Memiliki Lembaga MDTA)
        $tpqBuilder = $this->db->table('tbl_tpq');
        if ($isLembagaUser) {
            $tpqBuilder->where('IdTpq', $sessionTpq);
        } else {
            $mdaTpqIds = $this->getMdaTpqIds();
            if (!empty($mdaTpqIds)) {
                $tpqBuilder->whereIn('IdTpq', $mdaTpqIds);
            }
        }
        $tpqList   = $tpqBuilder->orderBy('NamaTpq', 'ASC')->get()->getResultArray();
        $kelasList = $this->db->table('tbl_kelas')->orderBy('NamaKelas', 'ASC')->get()->getResultArray();

        $materiList = $this->db->table('tbl_materi_pelajaran m')
            ->select('m.IdMateri, m.NamaMateri')
            ->groupStart()
                ->like('m.Kategori', 'MDTA')
                ->orLike('m.Kategori', 'MDA')
                ->orWhereIn('m.IdKategori', ['KM009', 'KM010'])
            ->groupEnd()
            ->orderBy('m.NamaMateri', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($materiList)) {
            $materiList = $this->db->table('tbl_ujian_mdta_paket p')
                ->select('m.IdMateri, m.NamaMateri')
                ->join('tbl_materi_pelajaran m', 'm.IdMateri = p.IdMateri')
                ->groupBy('m.IdMateri')
                ->orderBy('m.NamaMateri', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'page_title'    => 'Laporan Hasil Ujian MDTA',
            'menu_open'     => 'ujian-mdta',
            'menu_active'   => 'ujian-mdta-laporan',
            'jadwalList'    => $jadwalList,
            'tpqList'       => $tpqList,
            'kelasList'     => $kelasList,
            'materiList'    => $materiList,
            'isLembagaUser' => $isLembagaUser,
            'effectiveTpq'  => $effectiveTpq,
            'filter'        => [
                'IdTpq'     => $effectiveTpq,
                'IdKelas'   => $idKelasFilter,
                'IdMateri'  => $idMateriFilter,
                'Ruang'     => $ruangFilter,
                'startDate' => $startDateFilter,
                'endDate'   => $endDateFilter,
                'keyword'   => $keywordFilter,
            ]
        ];

        return view('backend/ujianMdta/laporan/hasil', $data);

    }

    /**
     * Export Excel "Rekap Per Materi" (Referensi Gambar 3)
     */
    public function rekapMateriExcel()
    {
        $sessionTpq       = (string)($this->idTpq ?? '0');
        $idKelas          = $this->request->getGet('IdKelas') ?? '';
        $idTpqFilter      = $this->request->getGet('IdTpq') ?? '';
        $startDateFilter  = $this->request->getGet('startDate') ?? '';
        $endDateFilter    = $this->request->getGet('endDate') ?? '';
        $keywordFilter    = $this->request->getGet('keyword') ?? '';
        $jadwalIdsStr     = $this->request->getGet('jadwal_ids') ?? '';
        $jadwalIds        = !empty($jadwalIdsStr) ? array_filter(array_map('intval', explode(',', $jadwalIdsStr))) : [];
        $idTahunAjaran    = session()->get('IdTahunAjaran') ?? '2025-2026';

        $isLembagaUser = ($sessionTpq !== '0');
        $effectiveTpq  = $isLembagaUser ? $sessionTpq : ((!empty($idTpqFilter) && $idTpqFilter !== 'all') ? $idTpqFilter : '0');

        // 1. Ambil daftar materi MDTA untuk header kolom Excel (khusus jadwal yang di-checklist jika ada)
        if (!empty($jadwalIds)) {
            $materiList = $this->db->table('tbl_ujian_mdta_jadwal j')
                ->select('m.IdMateri, m.NamaMateri')
                ->join('tbl_ujian_mdta_paket p', 'p.id = j.IdPaket')
                ->join('tbl_materi_pelajaran m', 'm.IdMateri = p.IdMateri')
                ->whereIn('j.id', $jadwalIds)
                ->groupBy('m.IdMateri')
                ->orderBy('m.NamaMateri', 'ASC')
                ->get()
                ->getResultArray();
        } else {
            $materiList = $this->db->table('tbl_materi_pelajaran m')
                ->select('m.IdMateri, m.NamaMateri')
                ->groupStart()
                    ->like('m.Kategori', 'MDTA')
                    ->orLike('m.Kategori', 'MDA')
                    ->orWhereIn('m.IdKategori', ['KM009', 'KM010'])
                ->groupEnd()
                ->orderBy('m.NamaMateri', 'ASC')
                ->get()
                ->getResultArray();

            if (empty($materiList)) {
                $materiList = $this->db->table('tbl_ujian_mdta_paket p')
                    ->select('m.IdMateri, m.NamaMateri')
                    ->join('tbl_materi_pelajaran m', 'm.IdMateri = p.IdMateri')
                    ->groupBy('m.IdMateri')
                    ->orderBy('m.NamaMateri', 'ASC')
                    ->get()
                    ->getResultArray();
            }
        }

        // 2. Ambil santri aktif khusus dari Lembaga terpilih/login
        $santriBuilder = $this->db->table('tbl_santri_baru s')
            ->select('s.IdSantri, s.IdTpq, s.NamaSantri, k.NamaKelas, t.NamaTpq, ks.IdKelas')
            ->join('tbl_kelas_santri ks', 'ks.IdSantri = s.IdSantri AND ks.IdTahunAjaran = "' . $idTahunAjaran . '"', 'left')
            ->join('tbl_kelas k', 'k.IdKelas = COALESCE(ks.IdKelas, s.IdKelas)', 'left')
            ->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'left')
            ->where('s.Active', 1);

        if ($effectiveTpq !== '0') {
            $santriBuilder->where('s.IdTpq', $effectiveTpq);
        } else {
            $mdaTpqIds = $this->getMdaTpqIds();
            if (!empty($mdaTpqIds)) {
                $santriBuilder->whereIn('s.IdTpq', $mdaTpqIds);
            }
        }

        // Filter kelas berdasarkan jadwal yang dicentang atau filter IdKelas
        if (!empty($jadwalIds)) {
            $jadwalKelasRows = $this->db->table('tbl_ujian_mdta_jadwal')
                ->select('IdKelas')
                ->whereIn('id', $jadwalIds)
                ->get()
                ->getResultArray();
            $kelasFromJadwal = array_filter(array_column($jadwalKelasRows, 'IdKelas'));
            if (!empty($kelasFromJadwal)) {
                $santriBuilder->whereIn('COALESCE(ks.IdKelas, s.IdKelas)', $kelasFromJadwal);
            }
        } else if (!empty($idKelas) && $idKelas !== 'all') {
            $santriBuilder->where('COALESCE(ks.IdKelas, s.IdKelas) =', $idKelas);
        }

        if (!empty($keywordFilter)) {
            $santriBuilder->like('s.NamaSantri', $keywordFilter);
        }

        $santriList = $santriBuilder->orderBy('t.NamaTpq', 'ASC')->orderBy('s.NamaSantri', 'ASC')->get()->getResultArray();



        // 3. Score map per santri & materi
        $scoresMap = [];
        $tipeUjianMap = [];

        if (!empty($santriList)) {
            $santriIds = array_column($santriList, 'IdSantri');

            $scoresBuilder = $this->db->table('tbl_ujian_mdta_sesi sesi')
                ->select('sesi.IdSantri, p.IdMateri, MAX(sesi.NilaiAkhir) as NilaiTerbaik, MAX(sesi.AttemptKe) as MaxAttempt')
                ->join('tbl_ujian_mdta_jadwal j', 'j.id = sesi.IdJadwal')
                ->join('tbl_ujian_mdta_paket p', 'p.id = j.IdPaket')
                ->whereIn('sesi.IdSantri', $santriIds)
                ->where('sesi.StatusSesi IN ("selesai", "timeout")');

            if (!empty($jadwalIds)) {
                $scoresBuilder->whereIn('j.id', $jadwalIds);
            }

            if (!empty($startDateFilter)) {
                $scoresBuilder->where('j.TanggalMulai >=', $startDateFilter . ' 00:00:00');
            }
            if (!empty($endDateFilter)) {
                $scoresBuilder->where('j.TanggalMulai <=', $endDateFilter . ' 23:59:59');
            }

            $scores = $scoresBuilder->groupBy('sesi.IdSantri, p.IdMateri')->get()->getResultArray();

            foreach ($scores as $sc) {
                $scoresMap[$sc['IdSantri']][$sc['IdMateri']] = (float)$sc['NilaiTerbaik'];

                $attempt = (int)$sc['MaxAttempt'];
                if ($attempt <= 1) {
                    $tipeUjianMap[$sc['IdSantri']] = 'Utama';
                } elseif ($attempt == 2) {
                    $tipeUjianMap[$sc['IdSantri']] = 'Remedial 1';
                } else {
                    $tipeUjianMap[$sc['IdSantri']] = 'Remedial ' . ($attempt - 1);
                }
            }
        }

        $filename = 'Rekap_Nilai_Ujian_Per_Materi_' . date('Ymd_His') . '.xls';
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        echo view('backend/ujianMdta/laporan/excel_rekap_materi', [
            'materiList'   => $materiList,
            'santriList'   => $santriList,
            'scoresMap'    => $scoresMap,
            'tipeUjianMap' => $tipeUjianMap,
        ]);
        exit;
    }

    /**
     * Export Excel "Rekap Per Sesi"
     */
    public function rekapSesiExcel()
    {
        $sessionTpq      = (string)($this->idTpq ?? '0');
        $idJadwal        = $this->request->getGet('idJadwal') ?? 0;
        $idKelas         = $this->request->getGet('IdKelas') ?? '';
        $idTpq           = $this->request->getGet('IdTpq') ?? '';
        $startDateFilter = $this->request->getGet('startDate') ?? '';
        $endDateFilter   = $this->request->getGet('endDate') ?? '';
        $keywordFilter   = $this->request->getGet('keyword') ?? '';
        $jadwalIdsStr    = $this->request->getGet('jadwal_ids') ?? '';
        $jadwalIds       = !empty($jadwalIdsStr) ? array_filter(array_map('intval', explode(',', $jadwalIdsStr))) : [];

        $builder = $this->db->table('tbl_ujian_mdta_sesi sesi')
            ->select('sesi.*, s.NamaSantri, s.NISN, s.IdSantri, k.NamaKelas, t.NamaTpq, j.NamaUjian, j.NilaiMinimum')
            ->join('tbl_santri_baru s', 's.IdSantri = sesi.IdSantri', 'left')
            ->join('tbl_ujian_mdta_jadwal j', 'j.id = sesi.IdJadwal', 'left')
            ->join('tbl_kelas k', 'k.IdKelas = j.IdKelas', 'left')
            ->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'left');

        if (!empty($jadwalIds)) {
            $builder->whereIn('sesi.IdJadwal', $jadwalIds);
        } else if (!empty($idJadwal)) {
            $builder->where('sesi.IdJadwal', $idJadwal);
        }
        
        if ($sessionTpq !== '0') {
            $builder->where('s.IdTpq', $sessionTpq);
        } else if (!empty($idTpq) && $idTpq !== 'all') {
            $builder->where('s.IdTpq', $idTpq);
        } else {
            $mdaTpqIds = $this->getMdaTpqIds();
            if (!empty($mdaTpqIds)) {
                $builder->whereIn('s.IdTpq', $mdaTpqIds);
            }
        }


        if (!empty($idKelas) && $idKelas !== 'all') {
            $builder->where('j.IdKelas', $idKelas);
        }

        if (!empty($startDateFilter)) {
            $builder->where('j.TanggalMulai >=', $startDateFilter . ' 00:00:00');
        }
        if (!empty($endDateFilter)) {
            $builder->where('j.TanggalMulai <=', $endDateFilter . ' 23:59:59');
        }

        if (!empty($keywordFilter)) {
            $builder->groupStart()
                ->like('j.NamaUjian', $keywordFilter)
                ->orLike('s.NamaSantri', $keywordFilter)
            ->groupEnd();
        }

        $sesiList = $builder->orderBy('s.NamaSantri', 'ASC')->get()->getResultArray();


        $filename = 'Rekap_Sesi_Ujian_' . date('Ymd_His') . '.xls';
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        echo view('backend/ujianMdta/laporan/excel_rekap_sesi', [
            'sesiList' => $sesiList,
        ]);
        exit;
    }

    /**
     * Cetak Presensi Ujian (PDF)
     */
    public function cetakPresensi(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $sessionTpq = (string)($this->idTpq ?? '0');
        $sesiList   = $this->sesiModel->getSesiByJadwal($idJadwal, null, ($sessionTpq !== '0' ? $sessionTpq : null));

        $html = view('backend/ujianMdta/laporan/cetak_presensi', [
            'jadwal'   => $jadwal,
            'sesiList' => $sesiList,
        ]);

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'presensi-ujian-' . url_title($jadwal['NamaUjian'], '-', true) . '.pdf';
        $dompdf->stream($filename, ['Attachment' => false]);
    }

    /**
     * Cetak Presensi Ujian (Excel)
     */
    public function cetakPresensiExcel(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $sessionTpq = (string)($this->idTpq ?? '0');
        $sesiList   = $this->sesiModel->getSesiByJadwal($idJadwal, null, ($sessionTpq !== '0' ? $sessionTpq : null));

        $filename = 'Presensi_Ujian_' . url_title($jadwal['NamaUjian'], '_', true) . '.xls';
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        echo view('backend/ujianMdta/laporan/excel_presensi', [
            'jadwal'   => $jadwal,
            'sesiList' => $sesiList,
        ]);
        exit;
    }

    /**
     * Cetak Berita Acara Ujian
     */
    public function cetakBeritaAcara(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $sessionTpq = (string)($this->idTpq ?? '0');
        $sesiList   = $this->sesiModel->getSesiByJadwal($idJadwal, null, ($sessionTpq !== '0' ? $sessionTpq : null));

        $html = view('backend/ujianMdta/laporan/cetak_berita_acara', [
            'jadwal'   => $jadwal,
            'sesiList' => $sesiList,
        ]);

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'berita-acara-ujian-' . url_title($jadwal['NamaUjian'], '-', true) . '.pdf';
        $dompdf->stream($filename, ['Attachment' => false]);
    }

    /**
     * Analisis Butir Soal
     */
    public function analisisButirSoal(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $sessionTpq  = (string)($this->idTpq ?? '0');
        $soalBuilder = $this->db->table('tbl_ujian_mdta_soal s')
            ->select('s.*, COUNT(j.id) as total_dijawab, SUM(CASE WHEN j.IsBenar = 1 THEN 1 ELSE 0 END) as total_benar');

        if ($sessionTpq !== '0') {
            $soalBuilder->join('tbl_ujian_mdta_jawaban j', 'j.IdSoal = s.id', 'left')
                        ->join('tbl_ujian_mdta_sesi sesi', 'sesi.id = j.IdSesi AND sesi.IdJadwal = ' . (int)$idJadwal . ' AND sesi.IdTpq = "' . $sessionTpq . '"', 'left');
        } else {
            $soalBuilder->join('tbl_ujian_mdta_jawaban j', 'j.IdSoal = s.id', 'left')
                        ->join('tbl_ujian_mdta_sesi sesi', 'sesi.id = j.IdSesi AND sesi.IdJadwal = ' . (int)$idJadwal, 'left');
        }

        $soalList = $soalBuilder->where('s.IdPaket', $jadwal['IdPaket'])
            ->groupBy('s.id')
            ->get()
            ->getResultArray();

        $data = [
            'page_title' => 'Analisis Butir Soal — ' . $jadwal['NamaUjian'],
            'jadwal'     => $jadwal,
            'soalList'   => $soalList,
        ];

        return view('backend/ujianMdta/laporan/analisis_soal', $data);
    }

    /**
     * Rekap Jawaban Santri
     */
    public function rekapJawaban(int $idJadwal)
    {
        $jadwal = $this->jadwalModel->find($idJadwal);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $sessionTpq = (string)($this->idTpq ?? '0');
        $sesiList   = $this->sesiModel->getSesiByJadwal($idJadwal, null, ($sessionTpq !== '0' ? $sessionTpq : null));
        $soalList   = $this->db->table('tbl_ujian_mdta_soal')
            ->where('IdPaket', $jadwal['IdPaket'])
            ->orderBy('NomorSoal', 'ASC')
            ->get()
            ->getResultArray();

        $jawabanMap = [];
        $jawabanBuilder = $this->db->table('tbl_ujian_mdta_jawaban j')
            ->select('j.IdSesi, j.IdSoal, j.JawabanEsai, j.IsBenar, pil.HurufPilihan')
            ->join('tbl_ujian_mdta_sesi sesi', 'sesi.id = j.IdSesi')
            ->join('tbl_ujian_mdta_pilihan pil', 'pil.id = j.IdPilihan', 'left')
            ->where('sesi.IdJadwal', $idJadwal);

        if ($sessionTpq !== '0') {
            $jawabanBuilder->where('sesi.IdTpq', $sessionTpq);
        }

        $jawabanRows = $jawabanBuilder->get()->getResultArray();

        foreach ($jawabanRows as $jr) {
            $jawabanMap[$jr['IdSesi']][$jr['IdSoal']] = [
                'jawaban' => !empty($jr['HurufPilihan']) ? $jr['HurufPilihan'] : $jr['JawabanEsai'],
                'isBenar' => (int)$jr['IsBenar'],
            ];
        }

        $data = [
            'page_title'  => 'Rekap Jawaban Santri — ' . $jadwal['NamaUjian'],
            'jadwal'      => $jadwal,
            'sesiList'    => $sesiList,
            'soalList'    => $soalList,
            'jawabanMap'  => $jawabanMap,
            'isUserAdmin' => ($sessionTpq === '0'),
        ];


        return view('backend/ujianMdta/laporan/rekap_jawaban', $data);
    }

}

