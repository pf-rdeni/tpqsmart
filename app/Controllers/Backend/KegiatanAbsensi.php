<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\KegiatanAbsensiModel;
use App\Models\AbsensiGuruModel;
use App\Models\GuruModel;
use App\Models\TpqModel;

class KegiatanAbsensi extends BaseController
{
    protected $kegiatanModel;
    protected $absensiGuruModel;
    protected $guruModel;
    protected $tpqModel;

    public function __construct()
    {
        $this->kegiatanModel = new KegiatanAbsensiModel();
        $this->absensiGuruModel = new AbsensiGuruModel();
        $this->guruModel = new GuruModel();
        $this->tpqModel = new TpqModel();
    }

    public function index()
    {
        $activeRole = session()->get('active_role');
        $idTpqSession = session()->get('IdTpq');
        
        $query = $this->kegiatanModel;

        // Filter based on role
        if ($activeRole == 'operator' || (!empty($idTpqSession) && !in_groups('Admin'))) {
             // Operator sees shared 'Umum' events AND their own 'TPQ' events
             // OR maybe just their own? Requirement says "halaman untuk mengatur kegiatan apa untuk admin".
             // Assuming Operators manage their own events.
             $query->groupStart()
                   ->where('Lingkup', 'Umum')
                   ->orGroupStart()
                        ->where('Lingkup', 'TPQ')
                        ->where('IdTpq', $idTpqSession)
                   ->groupEnd()
                   ->groupEnd();
        }

        $kegiatan = $query->orderBy('Tanggal', 'DESC')->findAll();

        $data = [
            'page_title' => 'Data Kegiatan Absensi Guru',
            'kegiatan'   => $kegiatan,
        ];

        return view('backend/kegiatan_absensi/index', $data);
    }

    public function new()
    {
        $data = [
            'page_title' => 'Tambah Kegiatan Absensi',
            'tpq_list'   => $this->tpqModel->findAll(), // For Admin to select TPQ if needed
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
            'Lingkup'      => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lingkup = $this->request->getPost('Lingkup');
        $idTpq = null;

        // Determine IdTpq
        if ($lingkup == 'TPQ') {
            // If Admin, get from POST. If Operator, get from Session.
            if (in_groups('Admin')) {
                 $idTpq = $this->request->getPost('IdTpq'); // Admin selects
            } else {
                 $idTpq = session()->get('IdTpq');
            }
        }
        
        // Save Event
        $data = [
            'NamaKegiatan' => $this->request->getPost('NamaKegiatan'),
            'Tanggal'      => $this->request->getPost('Tanggal'),
            'JamMulai'     => $this->request->getPost('JamMulai'),
            'JamSelesai'   => $this->request->getPost('JamSelesai'),
            'Lingkup'      => $lingkup,
            'IdTpq'        => $idTpq,
            'IsActive'     => 0, // Default inactive
            'CreatedBy'    => user()->username,
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
            'tpq_list'   => $this->tpqModel->findAll(),
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
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Note: Changing Scope (Lingkup) or TPQ after creation is tricky because logs are already generated.
        // For simplicity, we assume Scope/TPQ doesn't change, or if it does, the user must regenerate logs manually (not implemented yet).
        // We only update basic info here.
        
        $data = [
            'NamaKegiatan' => $this->request->getPost('NamaKegiatan'),
            'Tanggal'      => $this->request->getPost('Tanggal'),
            'JamMulai'     => $this->request->getPost('JamMulai'),
            'JamSelesai'   => $this->request->getPost('JamSelesai'),
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
        
        // Deactivate similar events
        if ($lingkup == 'Umum') {
             $this->kegiatanModel->where('Lingkup', 'Umum')->set(['IsActive' => 0])->update();
        } else {
             $this->kegiatanModel->where('IdTpq', $idTpq)->set(['IsActive' => 0])->update();
        }
        
        // Set this one active
        $this->kegiatanModel->update($id, ['IsActive' => 1]);
        
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
