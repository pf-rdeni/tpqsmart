<?php

namespace App\Controllers\Backend\Administrasi;

use App\Controllers\BaseController;
use App\Models\Administrasi\SuratModel;
use App\Models\Administrasi\SuratTemplateModel;
use App\Models\Administrasi\SuratDisposisiModel;
use App\Models\HelpFunctionModel;

class AdministrasiDashboard extends BaseController
{
    protected $suratModel;
    protected $suratTemplateModel;
    protected $disposisiModel;
    protected $helpFunction;

    public function __construct()
    {
        $this->suratModel         = new SuratModel();
        $this->suratTemplateModel = new SuratTemplateModel();
        $this->disposisiModel     = new SuratDisposisiModel();
        $this->helpFunction       = new HelpFunctionModel();
    }

    public function index()
    {
        $idTpq = session()->get('IdTpq');
        $isAdmin = ($idTpq === '0' || $idTpq === 0 || empty($idTpq));
        $userId  = function_exists('user_id') ? user_id() : null;

        $stats = $this->suratModel->getStats($isAdmin ? null : $idTpq);
        $templates = $this->suratTemplateModel->getActiveByTpq($isAdmin ? null : $idTpq);
        $stats['TotalTemplateAktif'] = count($templates);

        $suratTerbaru  = $this->suratModel->getByTpq($isAdmin ? null : $idTpq, null, 7);
        $monthlyChart  = $this->suratModel->getMonthlyChartStats($isAdmin ? null : $idTpq);
        $categoryChart = $this->suratModel->getCategoryChartStats($isAdmin ? null : $idTpq);

        // Ambil reminder disposisi aktif
        $disposisiReminders = $this->disposisiModel->getReminderDisposisi($isAdmin ? null : (int)$idTpq, $userId);

        $data = [
            'page_title'         => 'Dashboard Administrasi Surat',
            'stats'              => $stats,
            'suratTerbaru'       => $suratTerbaru,
            'monthlyChart'       => $monthlyChart,
            'categoryChart'      => $categoryChart,
            'disposisiReminders' => $disposisiReminders,
            'idTpq'              => $idTpq,
            'isAdmin'            => $isAdmin,
        ];

        return view('backend/administrasi/dashboard/index', $data);
    }
}
