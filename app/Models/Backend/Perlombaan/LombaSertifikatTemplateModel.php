<?php

namespace App\Models\Backend\Perlombaan;

use CodeIgniter\Model;

class LombaSertifikatTemplateModel extends Model
{
    protected $table = 'tbl_lomba_sertifikat_template';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'cabang_id',
        'NamaTemplate',
        'FileTemplate',
        'Width',
        'Height',
        'Orientation',
        'Status',
        'RankSettings',
        'SignatorySettings'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'cabang_id' => 'required|integer',
        'NamaTemplate' => 'required|max_length[255]',
        'FileTemplate' => 'required|max_length[255]',
        'Width' => 'required|integer',
        'Height' => 'required|integer',
    ];

    protected $validationMessages = [
        'cabang_id' => [
            'required' => 'Cabang lomba harus dipilih',
        ],
        'NamaTemplate' => [
            'required' => 'Nama template harus diisi',
        ],
    ];

    /**
     * Get template by cabang ID
     */
    public function getTemplateByCabang($cabangId)
    {
        return $this->where('cabang_id', $cabangId)
                    ->where('Status', 'aktif')
                    ->first();
    }

    /**
     * Get template with fields configuration
     */
    public function getTemplateWithFields($templateId)
    {
        $template = $this->find($templateId);
        if (!$template) {
            return null;
        }

        $fieldModel = new LombaSertifikatFieldModel();
        $template['fields'] = $fieldModel->getFieldsByTemplate($templateId);

        return $template;
    }

    /**
     * Get template with cabang and lomba info
     */
    public function getTemplateWithCabang($templateId)
    {
        return $this->select('tbl_lomba_sertifikat_template.*, 
                             tbl_lomba_cabang.NamaCabang, 
                             tbl_lomba_cabang.lomba_id,
                             tbl_lomba_master.NamaLomba')
                    ->join('tbl_lomba_cabang', 'tbl_lomba_cabang.id = tbl_lomba_sertifikat_template.cabang_id')
                    ->join('tbl_lomba_master', 'tbl_lomba_master.id = tbl_lomba_cabang.lomba_id')
                    ->where('tbl_lomba_sertifikat_template.id', $templateId)
                    ->first();
    }

    /**
     * Delete template and associated file
     */
    public function deleteTemplate($id)
    {
        $template = $this->find($id);
        if ($template && !empty($template['FileTemplate'])) {
            $filePath = WRITEPATH . 'uploads/' . $template['FileTemplate'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        return $this->delete($id);
    }
}
