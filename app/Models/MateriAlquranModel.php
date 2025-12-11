<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriAlquranModel extends Model
{
    protected $table = 'tbl_materi_alquran';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'IdMateri',
        'IdKategoriMateri',
        'IdTpq',
        'IdSurah',
        'AyatMulai',
        'AyatAkhir',
        'NamaSurah',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'IdMateri' => 'required|max_length[50]',
        'IdKategoriMateri' => 'required|max_length[50]',
        'IdTpq' => 'permit_empty|max_length[50]',
        'IdSurah' => 'required|max_length[50]',
        'AyatMulai' => 'required|integer',
        'AyatAkhir' => 'permit_empty|integer',
        'NamaSurah' => 'required|max_length[255]'
    ];

    protected $validationMessages = [
        'IdMateri' => [
            'required' => 'Id Materi harus diisi',
            'max_length' => 'Id Materi maksimal 50 karakter'
        ],
        'IdKategoriMateri' => [
            'required' => 'Id Kategori harus diisi',
            'max_length' => 'Id Kategori maksimal 50 karakter'
        ],
        'IdTpq' => [
            'required' => 'Id TPQ harus diisi',
            'max_length' => 'Id TPQ maksimal 50 karakter'
        ],
        'IdSurah' => [
            'required' => 'Id Surah harus diisi',
            'max_length' => 'Id Surah maksimal 50 karakter'
        ],
        'AyatMulai' => [
            'required' => 'Ayat Mulai harus diisi',
            'integer' => 'Ayat Mulai harus berupa angka'
        ],
        'AyatAkhir' => [
            'integer' => 'Ayat Akhir harus berupa angka'
        ],
        'NamaSurah' => [
            'required' => 'Nama Surah harus diisi',
            'max_length' => 'Nama Surah maksimal 255 karakter'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}

