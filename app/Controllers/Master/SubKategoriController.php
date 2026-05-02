<?php

namespace App\Controllers\Master;

use App\Controllers\Master\Traits\SearchFilterTrait;
use App\Models\Master\SubKategoriModel;
use App\Models\Master\KategoriModel;
use CodeIgniter\Controller;

class SubKategoriController extends Controller
{
    use SearchFilterTrait;

    protected $model;
    protected $kategoriModel;

    public function __construct()
    {
        $this->model = new SubKategoriModel();
        $this->kategoriModel = new KategoriModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;

        $filterFields = ['kategori_id'];
        $params = $this->getFilterParams($filterFields);
        $searchQuery = $this->buildSearchQuery($params);
        $data = $this->getData($perPage, $page, $params);
        $data['searchQuery'] = $searchQuery;
        $data['params'] = $params;
        $data['filterOptions'] = $this->getFilterOptions();
        return view('master/sub_kategori/index', $data);
    }

    protected function getData(int $perPage, int $page, array $params): array
    {
        $builder = $this->model->builder();
        $builder->select('aset_sub_kategori.*, aset_kategori.nama_kategori, aset_kategori.kode_kategori')
            ->join('aset_kategori', 'aset_kategori.kt_id = aset_sub_kategori.kategori_id', 'left');

        if (!empty($params['q'])) {
            $builder->groupStart();
            $builder->orLike('aset_sub_kategori.kode_sub_kategori', $params['q'], 'both');
            $builder->orLike('aset_sub_kategori.nama_sub_kategori', $params['q'], 'both');
            $builder->orLike('aset_kategori.nama_kategori', $params['q'], 'both');
            $builder->groupEnd();
        }

        if (!empty($params['kategori_id'])) {
            $builder->where('aset_sub_kategori.kategori_id', $params['kategori_id']);
        }

        if (!isset($params['is_active'])) {
            $builder->where('aset_sub_kategori.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('aset_sub_kategori.kode_sub_kategori', 'ASC')
            ->get($perPage, ($page - 1) * $perPage)
            ->getResultArray();

        return [
            'title' => 'Master Sub Kategori',
            'records' => $records,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
        ];
    }

    protected function getFilterOptions(): array
    {
        $kategoriRecords = $this->kategoriModel->getActiveOptions();
        $options = [];
        foreach ($kategoriRecords as $kat) {
            $options[] = ['value' => $kat['kt_id'], 'label' => $kat['nama_kategori']];
        }
        return [
            'kategori' => $options
        ];
    }

    public function new()
    {
        $data = [
            'title' => 'Tambah Sub Kategori',
            'kategori' => $this->kategoriModel->getActiveOptions()
        ];
        return view('master/sub_kategori/form', $data);
    }

    public function create()
    {
        $rules = [
            'kategori_id' => 'required|integer',
            'kode_sub_kategori' => 'required|max_length[30]',
            'nama_sub_kategori' => 'required|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'kategori_id' => $this->request->getPost('kategori_id'),
            'kode_sub_kategori' => $this->request->getPost('kode_sub_kategori'),
            'nama_sub_kategori' => $this->request->getPost('nama_sub_kategori'),
            'wajib_merk' => $this->request->getPost('wajib_merk') ? 1 : 0,
            'wajib_type' => $this->request->getPost('wajib_type') ? 1 : 0,
            'wajib_ruangan' => $this->request->getPost('wajib_ruangan') ? 1 : 0,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ]);

        return redirect()->to(base_url('/master/sub-kategori'))->with('success', 'Sub Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/sub-kategori'))->with('error', 'Data tidak ditemukan.');
        }
        $data = [
            'title' => 'Edit Sub Kategori',
            'record' => $record,
            'kategori' => $this->kategoriModel->getActiveOptions()
        ];
        return view('master/sub_kategori/form', $data);
    }

    public function update($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/sub-kategori'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'kategori_id' => 'required|integer',
            'kode_sub_kategori' => 'required|max_length[30]',
            'nama_sub_kategori' => 'required|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'kategori_id' => $this->request->getPost('kategori_id'),
            'kode_sub_kategori' => $this->request->getPost('kode_sub_kategori'),
            'nama_sub_kategori' => $this->request->getPost('nama_sub_kategori'),
            'wajib_merk' => $this->request->getPost('wajib_merk') ? 1 : 0,
            'wajib_type' => $this->request->getPost('wajib_type') ? 1 : 0,
            'wajib_ruangan' => $this->request->getPost('wajib_ruangan') ? 1 : 0,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(base_url('/master/sub-kategori'))->with('success', 'Sub Kategori berhasil diperbarui.');
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/sub-kategori'))->with('error', 'Data tidak ditemukan.');
        }
        $this->model->delete($id);
        return redirect()->to(base_url('/master/sub-kategori'))->with('success', 'Sub Kategori berhasil dihapus.');
    }
}
