<?php

namespace App\Models;

use CodeIgniter\Model;

class SantriModel extends Model
{
    public $db;
    public function init()
    {
        $db = db_connect();
    }

    protected $table      = 'tbl_santri_baru';
    protected $allowedFields = [
        'Active'
    ];

    public function GetData($id = false)
    {
        if ($id) {
            return $this->where(['IdTpq' => $id])->find();
        } else {
            return $this->findAll();
        }
    }


    public function GetDataSantriPerKelas($IdTpq, $IdTahunAjaran = 0, $IdKelas = 0, $IdGuru = null)
    {
        $db = db_connect();

        // Base SQL query
        $sql = 'SELECT 
                    ks.Id,
                    ks.IdTahunAjaran,
                    k.IdKelas,
                    k.NamaKelas,
                    g.IdGuru,
                    g.Nama AS GuruNama,
                    s.IdSantri,
                    s.NamaSantri,
                    s.JenisKelamin,
                    t.IdTpq,
                    t.NamaTpq,
                    t.Alamat,
                    w.IdJabatan
                FROM 
                    tbl_kelas_santri ks
                LEFT JOIN 
                    tbl_kelas k ON ks.IdKelas = k.IdKelas
                LEFT JOIN 
                    tbl_santri_baru s ON ks.IdSantri = s.IdSantri
                LEFT JOIN 
                    tbl_tpq t ON ks.IdTpq = t.IdTpq
                LEFT JOIN 
                    tbl_guru_kelas w ON w.IdKelas = k.IdKelas AND w.IdTpq = t.IdTpq
                LEFT JOIN 
                    tbl_guru g ON w.IdGuru = g.IdGuru
                WHERE 
                    1=1';  // Baseline query (always true)

        // Add filters to the SQL query
        $sql .= $this->addFilterById($db, 'ks.IdTahunAjaran', $IdTahunAjaran);
        $sql .= $this->addFilterById($db, 'w.IdGuru', $IdGuru);
        $sql .= $this->addFilterById($db, 'k.IdKelas', $IdKelas);
        $sql .= $this->addFilterById($db, 'ks.IdTpq', $IdTpq);
        $sql .= 'GROUP BY 
                ks.Id,
                ks.IdTahunAjaran,
                k.IdKelas,
                k.NamaKelas,
                g.IdGuru,
                g.Nama,
                s.IdSantri,
                s.NamaSantri,
                s.JenisKelamin,
                t.IdTpq,
                t.NamaTpq,
                t.Alamat,
                w.IdJabatan';
        

        // Add ORDER BY clause
        $sql .= ' ORDER BY k.NamaKelas ASC, s.NamaSantri ASC';

        // Execute the query
        $query = $db->query($sql)->getResultObject();

        return $query;
    }

    private function addFilterById($db, $column, $id)
    {
        $filter = '';

        if (!empty($id)) {
            if (is_array($id)) {
                // Escape each element in array and use WHERE IN
                $escapedId = array_map([$db, 'escape'], $id);
                $filter .= ' AND ' . $column . ' IN (' . implode(',', $escapedId) . ')';
            } else {
                // If id is a single value
                $filter .= ' AND ' . $column . ' = ' . $db->escape($id);
            }
        }

        return $filter;
    }

    // GetTotalSantri
    public function GetTotalSantri($IdTpq, $IdTahunAjaran = 0, $IdKelas = 0, $IdGuru = null)
    {
        $db = db_connect();

        // Base SQL query
        $sql = 'SELECT 
                    COUNT(s.IdSantri) AS TotalSantri
                FROM 
                    tbl_kelas_santri ks
                LEFT JOIN 
                    tbl_kelas k ON ks.IdKelas = k.IdKelas
                LEFT JOIN 
                    tbl_santri_baru s ON ks.IdSantri = s.IdSantri
                LEFT JOIN 
                    tbl_tpq t ON ks.IdTpq = t.IdTpq
                LEFT JOIN 
                    tbl_guru_kelas w ON w.IdKelas = k.IdKelas AND w.IdTpq = t.IdTpq
                LEFT JOIN 
                    tbl_guru g ON w.IdGuru = g.IdGuru
                WHERE 
                    1=1';  // Baseline query (always true)

        // Add filters to the SQL query
        $sql .= $this->addFilterById($db, 'ks.IdTpq', $IdTpq);
        $sql .= $this->addFilterById($db, 'ks.IdTahunAjaran', $IdTahunAjaran);

        // Khusus untuk IdGuru dan IdKelas
        if ($IdGuru !== null && $IdGuru != 0) {
            $sql .= $this->addFilterById($db, 'w.IdGuru', $IdGuru);
        }

        if ($IdKelas != 0) {
            $sql .= $this->addFilterById($db, 'k.IdKelas', $IdKelas);
        }


        // Execute the query
        return $db->query($sql)->getRow()->TotalSantri;
    }
}