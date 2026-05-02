<?php

namespace App\Controllers\Master;

use App\Controllers\Master\Traits\SearchFilterTrait;
use App\Models\Master\TypeModel;
use App\Models\Master\MerkModel;
use CodeIgniter\Controller;

class TypeController extends Controller
{
    use SearchFilterTrait;

    protected $model;
    protected $merkModel;

    public function __construct()
    {
        $this->model = new TypeModel();
        $this->merkModel = new MerkModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;

        $filterFields = ['merk_id'];
        $params = $this->getFilterParams($filterFields);
        $searchQuery = $this->buildSearchQuery($params);
        $data = $this->getData($perPage, $page, $params);
        $data['searchQuery'] = $searchQuery;
        $data['params'] = $params;
        $data['filterOptions'] = $this->getFilterOptions();
        return view('master/type/index', $data);
    }

    protected function getData(int $perPage, int $page, array $params): array
    {
        $builder = $this->model->builder();
        $builder->select('aset_type.*, aset_merk.nama_merk')
            ->join('aset_merk', 'aset_merk.mr_id = aset_type.merk_id', 'left');

        if (!empty($params['q'])) {
            $builder->groupStart();
            $builder->orLike('aset_type.kode_type', $params['q'], 'both');
            $builder->orLike('aset_type.nama_type', $params['q'], 'both');
            $builder->orLike('aset_merk.nama_merk', $params['q'], 'both');
            $builder->groupEnd();
        }

        if (!empty($params['merk_id'])) {
            $builder->where('aset_type.merk_id', $params['merk_id']);
        }

        if (!isset($params['is_active'])) {
            $builder->where('aset_type.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('aset_type.kode_type', 'ASC')
            ->get($perPage, ($page - 1) * $perPage)
            ->getResultArray();

        return [
            'title' => 'Master Type/Model',
            'records' => $records,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
        ];
    }

    protected function getFilterOptions(): array
    {
        $merkRecords = $this->merkModel->getActiveOptions();
        $options = [];
        foreach ($merkRecords as $merk) {
            $options[] = ['value' => $merk['mr_id'], 'label' => $merk['nama_merk']];
        }
        return [
            'merk' => $options
        ];
    }

    public function new()
    {
        $data = [
            'title' => 'Tambah Type',
            'merk' => $this->merkModel->orderBy('nama_merk', 'ASC')->where('is_active', 1)->findAll()
        ];
        return view('master/type/form', $data);
    }

    public function create()
    {
        $rules = [
            'kode_type' => 'required|max_length[30]',
            'nama_type' => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'merk_id' => $this->request->getPost('merk_id') ?: null,
            'kode_type' => $this->request->getPost('kode_type'),
            'nama_type' => $this->request->getPost('nama_type'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ]);

        return redirect()->to(base_url('/master/type'))->with('success', 'Type berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/type'))->with('error', 'Data tidak ditemukan.');
        }
        $data = [
            'title' => 'Edit Type',
            'record' => $record,
            'merk' => $this->merkModel->orderBy('nama_merk', 'ASC')->where('is_active', 1)->findAll()
        ];
        return view('master/type/form', $data);
    }

    public function update($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/type'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'kode_type' => 'required|max_length[30]',
            'nama_type' => 'required|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'merk_id' => $this->request->getPost('merk_id') ?: null,
            'kode_type' => $this->request->getPost('kode_type'),
            'nama_type' => $this->request->getPost('nama_type'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(base_url('/master/type'))->with('success', 'Type berhasil diperbarui.');
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/type'))->with('error', 'Data tidak ditemukan.');
        }
        $this->model->update($id, ['is_active' => 0]);
        return redirect()->to(base_url('/master/type'))->with('success', 'Type berhasil dihapus.');
    }
}
