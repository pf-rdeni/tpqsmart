<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KegiatanAbsensiModel;
use App\Models\AbsensiGuruModel;
use App\Models\GuruModel;
use App\Models\TpqModel;

use App\Models\HelpFunctionModel;

class KegiatanAbsensi extends BaseController
{
    protected $kegiatanModel;
    protected $absensiGuruModel;
    protected $guruModel;
    protected $tpqModel;
    protected $helpFunctionModel;

    public function __construct()
    {
        $this->kegiatanModel = new KegiatanAbsensiModel();
        $this->absensiGuruModel = new AbsensiGuruModel();
        $this->guruModel = new GuruModel();
        $this->tpqModel = new TpqModel();
        $this->helpFunctionModel = new HelpFunctionModel();
    }

    public function index()
    {
        $activeRole = session()->get('active_role');
        $idTpqSession = session()->get('IdTpq');
        
        $query = $this->kegiatanModel->select('tbl_kegiatan_absensi.*, tbl_tpq.NamaTpq')
                                     ->join('tbl_tpq', 'tbl_tpq.IdTpq = tbl_kegiatan_absensi.IdTpq', 'left');

        // Filter based on role
        if ($activeRole == 'operator' || (!empty($idTpqSession) && !in_groups('Admin'))) {
             // Operator ONLY sees their own 'TPQ' events
             $query->groupStart()
                   ->where('tbl_kegiatan_absensi.Lingkup', 'TPQ')
                   ->where('tbl_kegiatan_absensi.IdTpq', $idTpqSession)
                   ->groupEnd();
        }

        $kegiatan = $query->orderBy('Tanggal', 'DESC')->findAll();

        // Auto-generate token for legacy events
        $updated = false;
        foreach ($kegiatan as &$k) {
            if (empty($k['Token'])) {
                $token = bin2hex(random_bytes(32));
                $this->kegiatanModel->update($k['Id'], ['Token' => $token]);
                $k['Token'] = $token;
                $updated = true;
            }
        }
        
        // Get Guru List for WA Share
        // Filter by TPQ if strictly scoped? Or just all?
        // Let's get all active teachers for the search.
        $guruList = $this->guruModel->select('IdGuru, Nama, NoHp')->where('Status', '1')->findAll();

        $data = [
            'page_title' => 'Data Kegiatan Absensi Guru',
            'kegiatan'   => $kegiatan,
            'guruList'   => $guruList
        ];

        return view('backend/absensiGuru/index', $data);
    }

    public function new()
    {
        $data = [
            'page_title' => 'Tambah Kegiatan Absensi',
            'tpq_list'   => $this->helpFunctionModel->getDataTpq(),
            'isGuru'     => in_groups('Guru') && !in_groups('Admin'),
        ];
        return view('backend/absensiGuru/form', $data);
    }

    public function create()
    {
        $rules = [
            'NamaKegiatan' => 'required',
            'Tanggal'      => 'required|valid_date',
            'JamMulai'     => 'required',
            'JamSelesai'   => 'required',
            'LingkupSelect'=> 'required',
            'Tempat'       => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lingkupSelect = $this->request->getPost('LingkupSelect');
        $lingkup = 'Umum';
        $idTpq = null;

        if ($lingkupSelect === 'Umum') {
            $lingkup = 'Umum';
            $idTpq = null;
        } else {
            // Assume it's an IdTpq
            $lingkup = 'TPQ';
            // Validation: Ensure valid TPQ ID?
            $idTpq = $lingkupSelect;
        }

        // Override for Operator and Guru - force TPQ scope
        $sessionIdTpq = session()->get('IdTpq');
        if ((session()->get('active_role') == 'operator' || in_groups('Guru')) && !in_groups('Admin')) {
            $lingkup = 'TPQ';
            $idTpq = $sessionIdTpq;
        }
        
        // Save Event
        // Create
        $data = [
            'NamaKegiatan' => $this->request->getPost('NamaKegiatan'),
            'Tanggal'      => $this->request->getPost('Tanggal'),
            'JamMulai'     => $this->request->getPost('JamMulai'),
            'JamSelesai'   => $this->request->getPost('JamSelesai'),
            'Tempat'       => $this->request->getPost('Tempat'),
            'Detail'       => $this->request->getPost('Detail'),
            'Lingkup'      => $lingkup,
            'IdTpq'        => $idTpq,
            'CreatedBy'    => session()->get('id_user'),
            'Token'        => bin2hex(random_bytes(16)),
            'IsActive'     => 1, // Default active
            'JenisJadwal'       => $this->request->getPost('JenisJadwal'),
            'Interval'          => $this->request->getPost('Interval') ?: 1,
            'TanggalMulaiRutin' => $this->request->getPost('TanggalMulaiRutin') ?: null,
            'TanggalAkhirRutin' => $this->request->getPost('TanggalAkhirRutin') ?: null,
            'JenisBatasAkhir'   => $this->request->getPost('JenisBatasAkhir') ?: 'Tanggal',
            'JumlahKejadian'    => $this->request->getPost('JumlahKejadian') ?: null,
            'TanggalDalamBulan' => $this->request->getPost('TanggalDalamBulan') ?: null,
            'OpsiPola'          => $this->request->getPost('OpsiPola') ?: 'Tanggal',
            'PosisiMinggu'      => $this->request->getPost('PosisiMinggu') ?: null,
            'BulanTahun'        => $this->request->getPost('BulanTahun') ?: null,
        ];

        // Handle HariDalamMinggu (Array to CSV)
        $hari = $this->request->getPost('HariDalamMinggu');
        if (is_array($hari)) {
            $data['HariDalamMinggu'] = implode(',', $hari);
        } else {
            $data['HariDalamMinggu'] = $hari ?: null;
        }

        // Handle Nth Day Target for Bulanan/Tahunan (Override if HariKe)
        if ($data['OpsiPola'] == 'HariKe') {
            if ($data['JenisJadwal'] == 'bulanan') {
                $data['HariDalamMinggu'] = $this->request->getPost('HariTarget_Bulanan');
            } elseif ($data['JenisJadwal'] == 'tahunan') {
                $data['HariDalamMinggu'] = $this->request->getPost('HariTarget_Tahunan');
            }
        }
        
        $this->kegiatanModel->save($data);
        $idKegiatan = $this->kegiatanModel->getInsertID();
        
        // Generate initial log if needed (skipping strict generation for recurring patterns on create to save space, 
        // will be generated on demand or by cron/first access)
        
        return redirect()->to(base_url('backend/kegiatan-absensi'))->with('success', 'Kegiatan berhasil dibuat.');
    }
    
    public function edit($id = null)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) {
             return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
        
        // Convert HariDalamMinggu CSV to Array for View
        if (!empty($kegiatan['HariDalamMinggu'])) {
            $kegiatan['HariDalamMinggu'] = explode(',', $kegiatan['HariDalamMinggu']);
        } else {
            $kegiatan['HariDalamMinggu'] = [];
        }
        
        $data = [
            'page_title' => 'Edit Kegiatan Absensi',
            'kegiatan' => $kegiatan,
            'tpq_list'   => $this->helpFunctionModel->getDataTpq(),
            'isGuru'     => in_groups('Guru') && !in_groups('Admin'),
        ];
        
        return view('backend/absensiGuru/form', $data);
    }
    
    public function update($id = null)
    {
         $rules = [
            'NamaKegiatan' => 'required',
            'Tanggal'      => 'required|valid_date',
            'JamMulai'     => 'required',
            'JamSelesai'   => 'required',
            'Tempat'       => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Note: Lingkup change is not handled here to avoid mismatch with generated attendance logs.
        
        $data = [
            'NamaKegiatan' => $this->request->getPost('NamaKegiatan'),
            'Tanggal'      => $this->request->getPost('Tanggal'),
            'JamMulai'     => $this->request->getPost('JamMulai'),
            'JamSelesai'   => $this->request->getPost('JamSelesai'),
            'Tempat'       => $this->request->getPost('Tempat'),
            'Detail'       => $this->request->getPost('Detail'),
            'JenisJadwal'       => $this->request->getPost('JenisJadwal'),
            'Interval'          => $this->request->getPost('Interval') ?: 1,
            'TanggalMulaiRutin' => $this->request->getPost('TanggalMulaiRutin') ?: null,
            'TanggalAkhirRutin' => $this->request->getPost('TanggalAkhirRutin') ?: null,
            'JenisBatasAkhir'   => $this->request->getPost('JenisBatasAkhir') ?: 'Tanggal',
            'JumlahKejadian'    => $this->request->getPost('JumlahKejadian') ?: null,
            'TanggalDalamBulan' => $this->request->getPost('TanggalDalamBulan') ?: null,
            'OpsiPola'          => $this->request->getPost('OpsiPola') ?: 'Tanggal',
            'PosisiMinggu'      => $this->request->getPost('PosisiMinggu') ?: null,
            'BulanTahun'        => $this->request->getPost('BulanTahun') ?: null,
        ];
        
        // Handle HariDalamMinggu (Array to CSV)
        $hari = $this->request->getPost('HariDalamMinggu');
        if (is_array($hari)) {
            $data['HariDalamMinggu'] = implode(',', $hari);
        } else {
             $data['HariDalamMinggu'] = $hari ?: null;
        }

        // Handle Nth Day Target for Bulanan/Tahunan (Override if HariKe)
        if ($data['OpsiPola'] == 'HariKe') {
             if ($data['JenisJadwal'] == 'bulanan') {
                 $data['HariDalamMinggu'] = $this->request->getPost('HariTarget_Bulanan');
             } elseif ($data['JenisJadwal'] == 'tahunan') {
                 $data['HariDalamMinggu'] = $this->request->getPost('HariTarget_Tahunan');
             }
        }
        
        $this->kegiatanModel->update($id, $data);
        
        return redirect()->to(base_url('backend/kegiatan-absensi'))->with('success', 'Kegiatan berhasil diperbarui.');
    }
    
    public function delete($id = null)
    {
        $this->kegiatanModel->delete($id);
        // Cascade delete is handled by DB constraint, but if not, logic needed here.
        // DB Schema has ON DELETE CASCADE.
        return redirect()->to(base_url('backend/kegiatan-absensi'))->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function setActive($id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) return $this->response->setJSON(['success' => false]);
        
        $isActive = $kegiatan['IsActive'];
        
        // Simply toggle the status - allow multiple active
        $newStatus = $isActive ? 0 : 1;
        $this->kegiatanModel->update($id, ['IsActive' => $newStatus]);
        
        return $this->response->setJSON(['success' => true, 'newStatus' => $newStatus]);
    }

    private function generateAbsensiLog($idKegiatan, $lingkup, $idTpq)
    {
        $guruQuery = $this->guruModel->where('Status', '1'); // Active teachers only

        if ($lingkup == 'TPQ' && !empty($idTpq)) {
            $guruQuery->where('IdTpq', $idTpq);
        }
        // If 'Umum', fetch all.

        $teachers = $guruQuery->findAll();
        
        $batchData = [];
        foreach ($teachers as $guru) {
            $batchData[] = [
                'IdKegiatan'      => $idKegiatan,
                'IdGuru'          => $guru['IdGuru'], // Assuming array return
                'StatusKehadiran' => 'Alfa',
                'created_at'      => date('Y-m-d H:i:s')
            ];
        }
        
        if (!empty($batchData)) {
            $this->absensiGuruModel->insertBatch($batchData);
        }
    }
}
