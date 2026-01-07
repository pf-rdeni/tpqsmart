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
        
        $query = $this->kegiatanModel;

        // Filter based on role
        if ($activeRole == 'operator' || (!empty($idTpqSession) && !in_groups('Admin'))) {
             // Operator ONLY sees their own 'TPQ' events
             $query->groupStart()
                   ->where('Lingkup', 'TPQ')
                   ->where('IdTpq', $idTpqSession)
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

        return view('backend/kegiatan_absensi/index', $data);
    }

    public function new()
    {
        $data = [
            'page_title' => 'Tambah Kegiatan Absensi',
            'tpq_list'   => $this->helpFunctionModel->getDataTpq(), 
        ];
        return view('backend/kegiatan_absensi/form', $data);
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

        // Override for Operator
        if (session()->get('active_role') == 'operator' && !in_groups('Admin')) {
            $lingkup = 'TPQ';
            $idTpq = session()->get('IdTpq');
        }
        
        // Save Event
        $data = [
            'NamaKegiatan' => $this->request->getPost('NamaKegiatan'),
            'Tanggal'      => $this->request->getPost('Tanggal'),
            'JamMulai'     => $this->request->getPost('JamMulai'),
            'JamSelesai'   => $this->request->getPost('JamSelesai'),
            'Lingkup'      => $lingkup,
            'IdTpq'        => $idTpq,
            'Tempat'       => $this->request->getPost('Tempat'),
            'Detail'       => $this->request->getPost('Detail'),
            'IsActive'     => 0, // Default inactive
            'CreatedBy'    => user()->username,
            'Token'        => bin2hex(random_bytes(32)), // Generate 64-char hex token
        ];

        $idKegiatan = $this->kegiatanModel->insert($data, true);

        if ($idKegiatan) {
            // Batch Insert Absensi Guru with 'Alfa'
            $this->generateAbsensiLog($idKegiatan, $lingkup, $idTpq);
            
            return redirect()->to(base_url('backend/kegiatan-absensi'))->with('success', 'Kegiatan berhasil dibuat dan data absensi telah di-generate.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat kegiatan.');
        }
    }
    
    public function edit($id = null)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) {
             return redirect()->to(base_url('backend/kegiatan-absensi'))->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'page_title' => 'Edit Kegiatan Absensi',
            'kegiatan'   => $kegiatan,
            'tpq_list'   => $this->helpFunctionModel->getDataTpq(),
        ];
        return view('backend/kegiatan_absensi/form', $data);
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
        ];
        
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
        // Deactivate all others? Or allow multiple? plan said "Limit to 1 active event per scope?"
        // Simplicity: Deactivate EVERYTHING first.
        
        // Ideally should scope by TPQ/Umum.
        // If I activate an 'Umum' event, maybe deactivate other 'Umum' events.
        
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) return $this->response->setJSON(['success' => false]);
        
        $lingkup = $kegiatan['Lingkup'];
        $idTpq = $kegiatan['IdTpq'];
        $isActive = $kegiatan['IsActive'];
        
        if ($isActive) {
            // Toggle OFF
            $this->kegiatanModel->update($id, ['IsActive' => 0]);
        } else {
            // Toggle ON
            // Deactivate similar events
            if ($lingkup == 'Umum') {
                 $this->kegiatanModel->where('Lingkup', 'Umum')->set(['IsActive' => 0])->update();
            } else {
                 $this->kegiatanModel->where('IdTpq', $idTpq)->set(['IsActive' => 0])->update();
            }
            
            // Set this one active
            $this->kegiatanModel->update($id, ['IsActive' => 1]);
        }
        
        return $this->response->setJSON(['success' => true]);
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
