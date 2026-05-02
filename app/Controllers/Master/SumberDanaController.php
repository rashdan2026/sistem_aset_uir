<?php

namespace App\Controllers\Master;

use App\Controllers\Master\Traits\SearchFilterTrait;
use App\Models\Master\SumberDanaModel;
use CodeIgniter\Controller;

class SumberDanaController extends Controller
{
    use SearchFilterTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new SumberDanaModel();
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
        return view('master/sumber_dana/index', $data);
    }

    protected function getData(int $perPage, int $page, array $params): array
    {
        $builder = $this->model->builder();
        $searchFields = ['aset_sumber_dana.kode_sumber_dana', 'aset_sumber_dana.nama_sumber_dana'];
        $this->applySearchFilters($builder, $searchFields, $params);

        if (!isset($params['is_active'])) {
            $builder->where('aset_sumber_dana.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('aset_sumber_dana.kode_sumber_dana', 'ASC')
            ->get($perPage, ($page - 1) * $perPage)
            ->getResultArray();

        return [
            'title' => 'Master Sumber Dana',
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
        return view('master/sumber_dana/form', ['title' => 'Tambah Sumber Dana']);
    }

    public function create()
    {
        $rules = [
            'kode_sumber_dana' => 'required|max_length[30]|is_unique[aset_sumber_dana.kode_sumber_dana]',
            'nama_sumber_dana' => 'required|max_length[150]|is_unique[aset_sumber_dana.nama_sumber_dana]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'kode_sumber_dana' => $this->request->getPost('kode_sumber_dana'),
            'nama_sumber_dana' => $this->request->getPost('nama_sumber_dana'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ]);

        return redirect()->to(base_url('/master/sumber-dana'))->with('success', 'Sumber Dana berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/sumber-dana'))->with('error', 'Data tidak ditemukan.');
        }
        $data = ['title' => 'Edit Sumber Dana', 'record' => $record];
        return view('master/sumber_dana/form', $data);
    }

    public function update($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/sumber-dana'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'kode_sumber_dana' => "required|max_length[30]|is_unique[aset_sumber_dana.kode_sumber_dana,sd_id,{$id}]",
            'nama_sumber_dana' => "required|max_length[150]|is_unique[aset_sumber_dana.nama_sumber_dana,sd_id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'kode_sumber_dana' => $this->request->getPost('kode_sumber_dana'),
            'nama_sumber_dana' => $this->request->getPost('nama_sumber_dana'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(base_url('/master/sumber-dana'))->with('success', 'Sumber Dana berhasil diperbarui.');
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/sumber-dana'))->with('error', 'Data tidak ditemukan.');
        }
        $this->model->update($id, ['is_active' => 0]);
        return redirect()->to(base_url('/master/sumber-dana'))->with('success', 'Sumber Dana berhasil dinonaktifkan.');
    }
}
