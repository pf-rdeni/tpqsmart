<?php

namespace App\Models\Backend\Survey;

use CodeIgniter\Model;

class SurveyTargetModel extends Model
{
    protected $table            = 'tbl_survey_targets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'survey_id', 'target_type', 'target_ref_id', 'created_at',
    ];

    /**
     * Get all targets for a survey
     */
    public function getTargetsBySurvey(int $surveyId): array
    {
        return $this->where('survey_id', $surveyId)->findAll();
    }

    /**
     * Set targets for a survey (replace all existing)
     * @param array $targets [ ['target_type' => 'tpq', 'target_ref_id' => '01'], ... ]
     */
    public function setTargets(int $surveyId, array $targets): bool
    {
        // Delete existing
        $this->where('survey_id', $surveyId)->delete();

        if (empty($targets)) return true;

        $insertData = [];
        foreach ($targets as $target) {
            $insertData[] = [
                'survey_id'     => $surveyId,
                'target_type'   => $target['target_type'],
                'target_ref_id' => $target['target_ref_id'] ?? null,
                'created_at'    => date('Y-m-d H:i:s'),
            ];
        }

        return $this->insertBatch($insertData) !== false;
    }

    /**
     * Get target list with filled/unfilled status from responses
     * Joins with master data (tbl_guru / tbl_santri_baru / tbl_tpq)
     * Returns a flat list of target persons with status
     */
    public function getTargetListWithStatus(int $surveyId, string $targetType): array
    {
        $targets = $this->where('survey_id', $surveyId)
                        ->where('target_type', $targetType)
                        ->findAll();

        $list = [];
        $titleCase = function($str) {
            return ucwords(strtolower(trim($str)));
        };

        if ($targetType === 'guru') {
            // Ambil data guru dari target
            $tpqIds = array_filter(array_column($targets, 'target_ref_id'));

            $builder = $this->db->table('tbl_guru g')
                ->select('g.IdGuru as ref_id, g.Nama as name, g.IdTpq as tpq_id, t.NamaTpq as tpq_name')
                ->join('tbl_tpq t', 't.IdTpq = g.IdTpq', 'left')
                ->where('g.Status', true);

            // Jika ada target ref_id (spesifik per TPQ)
            if (!empty($tpqIds)) {
                $builder->whereIn('g.IdTpq', $tpqIds);
            }

            $rows = $builder->orderBy('g.IdTpq')->orderBy('g.Nama')->get()->getResultArray();

            foreach ($rows as $row) {
                $list[] = [
                    'ref_id'   => $row['ref_id'],
                    'name'     => $titleCase($row['name']),
                    'tpq_id'   => $row['tpq_id'],
                    'tpq_name' => $titleCase($row['tpq_name']),
                    'type'     => 'guru',
                ];
            }

        } elseif ($targetType === 'santri') {
            $tpqIds = array_filter(array_column($targets, 'target_ref_id'));

            $builder = $this->db->table('tbl_santri_baru s')
                ->select('s.IdSantri as ref_id, s.NamaSantri as name, s.IdTpq as tpq_id, t.NamaTpq as tpq_name')
                ->join('tbl_tpq t', 't.IdTpq = s.IdTpq', 'left')
                ->where('s.Active', 1);

            if (!empty($tpqIds)) {
                $builder->whereIn('s.IdTpq', $tpqIds);
            }

            $rows = $builder->orderBy('s.IdTpq')->orderBy('s.NamaSantri')->get()->getResultArray();

            foreach ($rows as $row) {
                $list[] = [
                    'ref_id'   => $row['ref_id'],
                    'name'     => $titleCase($row['name']),
                    'tpq_id'   => $row['tpq_id'],
                    'tpq_name' => $titleCase($row['tpq_name']),
                    'type'     => 'santri',
                ];
            }

        } elseif ($targetType === 'tpq') {
            $tpqIds = array_filter(array_column($targets, 'target_ref_id'));

            $builder = $this->db->table('tbl_tpq')->select('IdTpq, NamaTpq, KelurahanDesa');

            if (!empty($tpqIds)) {
                $builder->whereIn('IdTpq', $tpqIds);
            }

            $rows = $builder->orderBy('NamaTpq')->get()->getResultArray();

            $namesCount = [];
            foreach ($rows as $row) {
                $name = trim($row['NamaTpq']);
                $namesCount[$name] = ($namesCount[$name] ?? 0) + 1;
            }

            foreach ($rows as $row) {
                $name = $titleCase($row['NamaTpq']);
                if (($namesCount[trim($row['NamaTpq'])] ?? 0) > 1 && !empty($row['KelurahanDesa'])) {
                    $name = $name . ' - ' . $titleCase($row['KelurahanDesa']);
                }
                $list[] = [
                    'ref_id'   => $row['IdTpq'],
                    'name'     => $name,
                    'tpq_id'   => $row['IdTpq'],
                    'tpq_name' => $name,
                    'type'     => 'tpq',
                ];
            }
        }

        return $list;
    }
}
