<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\IuranBulananModel;
use App\Models\EncryptModel;
use App\Models\HelpFunctionModel;
use App\Models\SantriModel;

class IuranBulanan extends BaseController
{
    protected $dataSantri;
    protected $encryptModel;
    protected $helpFunction;
    protected $iuranBulananModel;

    public function __construct()
    {
        $this->encryptModel = new EncryptModel();
        $this->dataSantri = new SantriModel();
        $this->helpFunction = new HelpFunctionModel();
        $this->iuranBulananModel = new IuranBulananModel();
    }

    /**
     * Handles the creation of a new record. If the request method is POST, it validates the data, checks for duplicate records, and inserts the new record into the database.
     *
     * @return mixed If the request method is POST and the data is valid, it redirects the user to the 'showPerKelas' page. If the data is not valid, it redirects the user back to the form with the input data and error messages. If the request method is not POST, it returns the 'create' view.
     */
    public function create()
    {
        if ($this->request->getMethod() == 'POST') {
            $data = $this->getPostData();

            // Get santri details
            $santri = $this->dataSantri->where('IdSantri', $data['IdSantri'])->first();
            $namaSantri = $santri ? $santri['Nama'] : 'Santri Tidak Ditemukan';

            // Convert bulan number to bulan name
            $namaBulan = $this->getBulanName($data['Bulan']);

            // Check if nominal is valid
            if ($data['Nominal'] < 0 || empty($data['Nominal'])) {
                $this->setFlashMessage('danger', 'Gagal disimpan: Nominal untuk Santri: <strong>' . $namaSantri . '</strong> harus lebih dari 0.');
                return redirect()->back()->withInput();
            }

            // Check for duplicate records if Kategori is "Iuran"
            if ($data['Kategori'] === 'Iuran' && $this->isDuplicateRecord($data)) {
                $this->setFlashMessage('danger', 'Gagal disimpan: Data iuran untuk Santri: <strong>' . $namaSantri . '</strong> pada bulan <strong>' . $namaBulan . '</strong> sudah ada!');
                return redirect()->back()->withInput();
            }

            // Insert data and show success message
            $this->iuranBulananModel->insert($data);
            $this->setFlashMessage('success', 'Uang sebesar <strong>Rp. ' . number_format($data['Nominal'], 0, ',', '.') . '</strong> untuk Santri: <strong>' . $namaSantri . '</strong> pada bulan <strong>' . $namaBulan . '</strong> berhasil disimpan!');
            return redirect()->to('/backend/iuranBulanan/showPerKelas/');
        }

        return redirect()->to('/backend/iuranBulanan/showPerKelas/');
    }

    /**
     * Retrieves the data from the POST request and returns it as an associative array.
     *
     * @return array The data from the POST request.
     */
    private function getPostData()
    {
        return [
            'page_title' => 'Iuran Bulanan',
            'Bulan' => $this->request->getPost('Bulan'),
            'Kategori' => $this->request->getPost('Kategori'),
            'Nominal' => $this->helpFunction->convertToNumber($this->request->getPost('Nominal')),
            'IdTahunAjaran' => $this->request->getPost('IdTahunAjaran'),
            'IdSantri' => $this->request->getPost('IdSantri'),
            'IdKelas' => $this->request->getPost('IdKelas'),
            'IdTpq' => $this->request->getPost('IdTpq'),
            'IdGuru' => $this->request->getPost('IdGuru')
        ];
    }

    /**
     * Converts a month number to its corresponding name in Indonesian.
     *
     * @param int $bulanNumber The number of the month (1-12).
     *
     * @return string The name of the month in Indonesian.
     */
    private function getBulanName($bulanNumber)
    {
        $bulanList = [
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', 
            '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus', 
            '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        return $bulanList[$bulanNumber];
    }

    /**
     * Checks if a record with the same 'IdSantri', 'IdTahunAjaran', 'Bulan', and 'Kategori' already exists in the database.
     *
     * @param array $data An associative array containing the keys 'IdSantri', 'IdTahunAjaran', 'Bulan', and 'Kategori'.
     *
     * @return mixed The first record that matches the provided data, or null if no such record exists.
     */
    private function isDuplicateRecord($data)
    {
        return $this->iuranBulananModel->where([
            'IdSantri' => $data['IdSantri'],
            'IdTahunAjaran' => $data['IdTahunAjaran'],
            'Bulan' => $data['Bulan'],
            'Kategori' => 'Iuran'
        ])->first();
    }

    /**
     * Sets a flash message in the user's session. Flash messages are temporary messages that are meant to be displayed on the user's next request only, and then cleared.
     *
     * @param string $type The type of the alert. This corresponds to the Bootstrap alert classes, and can be 'success', 'danger', 'warning', 'info', or 'primary'.
     * @param string $message The message to be displayed in the alert.
     *
     * @return void
     */
    private function setFlashMessage($type, $message)
    {
        session()->setFlashdata('pesan', '
        <div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
            ' . $message . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>');
    }

    /**
     * Displays the data per class based on the provided encrypted teacher's ID.
     *
     * @param string|null $encryptedIdGuru The encrypted ID of the teacher. If not provided, the function will display data for all classes.
     *
     * @return mixed The view displaying the data per class.
     */
    public function showPerKelas($encryptedIdGuru = null)
    {
        if($encryptedIdGuru !== null)
            $IdGuru = $this->encryptModel->decryptData($encryptedIdGuru);
        else 
            $IdGuru = $encryptedIdGuru;

        $IdGuru = session()->get('IdGuru');  
        $IdKelas = session()->get('IdKelas');
        $IdTahunAjaran = session()->get('IdTahunAjaran');
        $dataSantri = $this->dataSantri->GetDataSantriPerKelas($IdTahunAjaran, $IdKelas, $IdGuru);
        $data = [
            'page_title' => 'Data Iuran Santri',
            'dataSantri' => $dataSantri
        ];

        return view('backend/iuran/iuranPerKelas', $data);
    }

    /**
     * Displays the details of a specific record based on the provided 'IdSantri' and 'IdTahunAjaran'.
     *
     * @param int $IdSantri The ID of the 'santri'.
     * @param int $IdTahunAjaran The ID of the 'tahun ajaran'.
     *
     * @return mixed The view displaying the details of the record.
     */
    public function showDetail($IdSantri, $IdTahunAjaran)
    {
        $dataIuran = $this->iuranBulananModel->getIuranBulanan($IdSantri, $IdTahunAjaran);

        foreach ($dataIuran as $Iuran) {
            $Iuran->Nominal = 'Rp. ' . number_format($Iuran->Nominal, 0, ',', '.');
        }
        
        foreach ($dataIuran as $Iuran) {
            $Iuran->Bulan = $this->helpFunction->numberToMonth($Iuran->Bulan);
        }
        

        $data = [
            'page_title' => 'Data Iuran Santri',
            'dataIuran' => $dataIuran,
        ];

        return view('backend/iuran/iuranSantriDetail', $data);
    }

}
