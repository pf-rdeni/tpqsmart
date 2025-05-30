<?php

namespace App\Controllers\Backend;

use App\Controllers\BaseController;
use App\Models\ToolsModel;

class Tools extends BaseController
{
    protected $toolsModel;

    public function __construct()
    {
        $this->toolsModel = new ToolsModel();
    }

    public function index()
    {
        $data = [
            'page_title' => 'Tools Setting',
            'tools' => $this->toolsModel->findAll()
        ];

        return view('backend/tools/index', $data);
    }

    public function create()
    {
        $data = [
            'page_title' => 'Tambah Tools Setting'
        ];

        return view('backend/tools/create', $data);
    }

    public function store()
    {
        $rules = [
            'IdTpq' => 'required|numeric',
            'SettingKey' => 'required|min_length[3]',
            'SettingValue' => 'required',
            'SettingType' => 'required',
            'Description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->toolsModel->insert([
            'IdTpq' => $this->request->getPost('IdTpq'),
            'SettingKey' => $this->request->getPost('SettingKey'),
            'SettingValue' => $this->request->getPost('SettingValue'),
            'SettingType' => $this->request->getPost('SettingType'),
            'Description' => $this->request->getPost('Description')
        ]);

        return redirect()->to('/backend/tools')->with('message', 'Tools Setting berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'page_title' => 'Edit Tools Setting',
            'tool' => $this->toolsModel->find($id)
        ];

        return view('backend/tools/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'IdTpq' => 'required|numeric',
            'SettingKey' => 'required|min_length[3]',
            'SettingValue' => 'required',
            'SettingType' => 'required',
            'Description' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->toolsModel->update($id, [
            'IdTpq' => $this->request->getPost('IdTpq'),
            'SettingKey' => $this->request->getPost('SettingKey'),
            'SettingValue' => $this->request->getPost('SettingValue'),
            'SettingType' => $this->request->getPost('SettingType'),
            'Description' => $this->request->getPost('Description')
        ]);

        return redirect()->to('/backend/tools')->with('message', 'Tools Setting berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->toolsModel->delete($id);
        return redirect()->to('/backend/tools')->with('message', 'Tools Setting berhasil dihapus');
    }
}
