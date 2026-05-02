<?php

namespace App\Controllers\Master;

use App\Controllers\Master\Traits\SearchFilterTrait;
use App\Models\Master\MerkModel;
use CodeIgniter\Controller;

class MerkController extends Controller
{
    use SearchFilterTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new MerkModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;

        $filterFields = [];
        $params = $this->getFilterParams($filterFields);
        $searchQuery = $this->buildSearchQuery($params);
        $data = $this->getData($perPage, $page, $params);
        $data['searchQuery'] = $searchQuery;
        $data['params'] = $params;
        $data['filterOptions'] = $this->getFilterOptions();
        return view('master/merk/index', $data);
    }

    protected function getData(int $perPage, int $page, array $params): array
    {
        $builder = $this->model->builder();
        $searchFields = ['aset_merk.kode_merk', 'aset_merk.nama_merk'];
        $this->applySearchFilters($builder, $searchFields, $params);

        if (!isset($params['is_active'])) {
            $builder->where('aset_merk.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('aset_merk.nama_merk', 'ASC')
            ->get($perPage, ($page - 1) * $perPage)
            ->getResultArray();

        return [
            'title' => 'Master Merk',
            'records' => $records,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
        ];
    }

    protected function getFilterOptions(): array
    {
        return [];
    }

    public function new()
    {
        return view('master/merk/form', ['title' => 'Tambah Merk']);
    }

    public function create()
    {
        $rules = [
            'kode_merk' => 'required|max_length[30]|is_unique[aset_merk.kode_merk]',
            'nama_merk' => 'required|max_length[100]|is_unique[aset_merk.nama_merk]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'kode_merk' => $this->request->getPost('kode_merk'),
            'nama_merk' => $this->request->getPost('nama_merk'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ]);

        return redirect()->to(base_url('/master/merk'))->with('success', 'Merk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/merk'))->with('error', 'Data tidak ditemukan.');
        }
        $data = ['title' => 'Edit Merk', 'record' => $record];
        return view('master/merk/form', $data);
    }

    public function update($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/merk'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'kode_merk' => "required|max_length[30]|is_unique[aset_merk.kode_merk,mr_id,{$id}]",
            'nama_merk' => "required|max_length[100]|is_unique[aset_merk.nama_merk,mr_id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'kode_merk' => $this->request->getPost('kode_merk'),
            'nama_merk' => $this->request->getPost('nama_merk'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(base_url('/master/merk'))->with('success', 'Merk berhasil diperbarui.');
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/merk'))->with('error', 'Data tidak ditemukan.');
        }
        $this->model->update($id, ['is_active' => 0]);
        return redirect()->to(base_url('/master/merk'))->with('success', 'Merk berhasil dihapus.');
    }
}
