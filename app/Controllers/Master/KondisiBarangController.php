<?php

namespace App\Controllers\Master;

use App\Controllers\Master\Traits\SearchFilterTrait;
use App\Models\Master\KondisiBarangModel;
use CodeIgniter\Controller;

class KondisiBarangController extends Controller
{
    use SearchFilterTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new KondisiBarangModel();
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
        return view('master/kondisi_barang/index', $data);
    }

    protected function getData(int $perPage, int $page, array $params): array
    {
        $builder = $this->model->builder();
        $searchFields = ['aset_kondisi_barang.kode_kondisi', 'aset_kondisi_barang.nama_kondisi'];
        $this->applySearchFilters($builder, $searchFields, $params);

        if (!isset($params['is_active'])) {
            $builder->where('aset_kondisi_barang.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('aset_kondisi_barang.updated_at', 'DESC')
            ->get($perPage, ($page - 1) * $perPage)
            ->getResultArray();

        return [
            'title' => 'Master Kondisi Barang',
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
        return view('master/kondisi_barang/form', ['title' => 'Tambah Kondisi']);
    }

    public function create()
    {
        $rules = [
            'kode_kondisi' => 'required|max_length[30]|is_unique[aset_kondisi_barang.kode_kondisi]',
            'nama_kondisi' => 'required|max_length[100]|is_unique[aset_kondisi_barang.nama_kondisi]',
            'level_kondisi' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'kode_kondisi' => $this->request->getPost('kode_kondisi'),
            'nama_kondisi' => $this->request->getPost('nama_kondisi'),
            'level_kondisi' => $this->request->getPost('level_kondisi'),
            'is_available_for_use' => $this->request->getPost('is_available_for_use') ? 1 : 0,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ]);

        return redirect()->to(base_url('/master/kondisi-barang'))->with('success', 'Kondisi Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/kondisi-barang'))->with('error', 'Data tidak ditemukan.');
        }
        $data = ['title' => 'Edit Kondisi Barang', 'record' => $record];
        return view('master/kondisi_barang/form', $data);
    }

    public function update($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/kondisi-barang'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'kode_kondisi' => "required|max_length[30]|is_unique[aset_kondisi_barang.kode_kondisi,kd_id,{$id}]",
            'nama_kondisi' => "required|max_length[100]|is_unique[aset_kondisi_barang.nama_kondisi,kd_id,{$id}]",
            'level_kondisi' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'kode_kondisi' => $this->request->getPost('kode_kondisi'),
            'nama_kondisi' => $this->request->getPost('nama_kondisi'),
            'level_kondisi' => $this->request->getPost('level_kondisi'),
            'is_available_for_use' => $this->request->getPost('is_available_for_use') ? 1 : 0,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(base_url('/master/kondisi-barang'))->with('success', 'Kondisi Barang berhasil diperbarui.');
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/kondisi-barang'))->with('error', 'Data tidak ditemukan.');
        }
        $this->model->update($id, ['is_active' => 0]);
        return redirect()->to(base_url('/master/kondisi-barang'))->with('success', 'Kondisi Barang berhasil dinonaktifkan.');
    }
}
