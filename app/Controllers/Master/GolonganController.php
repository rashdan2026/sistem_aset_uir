<?php

namespace App\Controllers\Master;

use App\Controllers\Master\Traits\SearchFilterTrait;
use App\Models\Master\GolonganModel;
use CodeIgniter\Controller;

class GolonganController extends Controller
{
    use SearchFilterTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new GolonganModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;

        $filterFields = ['kelompok'];
        $params = $this->getFilterParams($filterFields);
        $searchQuery = $this->buildSearchQuery($params);
        $data = $this->getData($perPage, $page, $params);
        $data['searchQuery'] = $searchQuery;
        $data['params'] = $params;
        $data['filterOptions'] = $this->getFilterOptions();
        return view('master/golongan/index', $data);
    }

    protected function getData(int $perPage, int $page, array $params): array
    {
        $builder = $this->model->builder();
        $searchFields = ['aset_golongan.kode_golongan', 'aset_golongan.nama_golongan'];
        $this->applySearchFilters($builder, $searchFields, $params);

        if (!empty($params['kelompok'])) {
            $builder->where('aset_golongan.kelompok', $params['kelompok']);
        }

        if (!isset($params['is_active'])) {
            $builder->where('aset_golongan.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('aset_golongan.kode_golongan', 'ASC')
            ->get($perPage, ($page - 1) * $perPage)
            ->getResultArray();

        return [
            'title' => 'Master Golongan',
            'records' => $records,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
        ];
    }

    protected function getFilterOptions(): array
    {
        return [
            'kelompok' => [
                ['value' => 'bangunan', 'label' => 'Bangunan'],
                ['value' => 'non_bangunan', 'label' => 'Non Bangunan'],
                ['value' => 'lainnya', 'label' => 'Lainnya'],
            ]
        ];
    }

    public function show($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/golongan'))->with('error', 'Data tidak ditemukan.');
        }
        $data = ['title' => 'Detail Golongan', 'record' => $record];
        return view('master/golongan/show', $data);
    }

    public function new()
    {
        return view('master/golongan/form', ['title' => 'Tambah Golongan']);
    }

    public function create()
    {
        $rules = [
            'kode_golongan' => 'required|max_length[30]|is_unique[aset_golongan.kode_golongan]',
            'nama_golongan' => 'required|max_length[150]|is_unique[aset_golongan.nama_golongan]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'kode_golongan' => $this->request->getPost('kode_golongan'),
            'nama_golongan' => $this->request->getPost('nama_golongan'),
            'kelompok' => $this->request->getPost('kelompok'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ]);

        return redirect()->to(base_url('/master/golongan'))->with('success', 'Golongan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/golongan'))->with('error', 'Data tidak ditemukan.');
        }
        $data = ['title' => 'Edit Golongan', 'record' => $record];
        return view('master/golongan/form', $data);
    }

    public function update($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/golongan'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'kode_golongan' => "required|max_length[30]|is_unique[aset_golongan.kode_golongan,gl_id,{$id}]",
            'nama_golongan' => "required|max_length[150]|is_unique[aset_golongan.nama_golongan,gl_id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'kode_golongan' => $this->request->getPost('kode_golongan'),
            'nama_golongan' => $this->request->getPost('nama_golongan'),
            'kelompok' => $this->request->getPost('kelompok'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(base_url('/master/golongan'))->with('success', 'Golongan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/golongan'))->with('error', 'Data tidak ditemukan.');
        }

        $this->model->update($id, ['is_active' => 0]);
        return redirect()->to(base_url('/master/golongan'))->with('success', 'Golongan berhasil dihapus.');
    }
}
