<?php

namespace App\Controllers\Master;

use App\Controllers\Master\Traits\SearchFilterTrait;
use App\Models\Master\KategoriModel;
use CodeIgniter\Controller;

class KategoriController extends Controller
{
    use SearchFilterTrait;

    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;

        $filterFields = ['jenis_aset'];
        $params = $this->getFilterParams($filterFields);
        $searchQuery = $this->buildSearchQuery($params);
        $data = $this->getData($perPage, $page, $params);
        $data['searchQuery'] = $searchQuery;
        $data['params'] = $params;
        $data['filterOptions'] = $this->getFilterOptions();
        return view('master/kategori/index', $data);
    }

    protected function getData(int $perPage, int $page, array $params): array
    {
        $builder = $this->kategoriModel->builder();
        $searchFields = ['aset_kategori.kode_kategori', 'aset_kategori.nama_kategori'];
        $this->applySearchFilters($builder, $searchFields, $params);

        if (!empty($params['jenis_aset'])) {
            $builder->where('aset_kategori.jenis_aset', $params['jenis_aset']);
        }

        if (!isset($params['is_active'])) {
            $builder->where('aset_kategori.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('aset_kategori.kode_kategori', 'ASC')
            ->get($perPage, ($page - 1) * $perPage)
            ->getResultArray();

        return [
            'title' => 'Master Kategori',
            'kategori' => $records,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
        ];
    }

    protected function getFilterOptions(): array
    {
        return [
            'jenis_aset' => [
                ['value' => 'bangunan', 'label' => 'Bangunan'],
                ['value' => 'ruangan', 'label' => 'Ruangan'],
                ['value' => 'non_bangunan', 'label' => 'Non Bangunan'],
                ['value' => 'lainnya', 'label' => 'Lainnya'],
            ]
        ];
    }

    public function show($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('/master/kategori'))->with('error', 'Data kategori tidak ditemukan.');
        }
        $data = [
            'title' => 'Detail Kategori',
            'kategori' => $kategori
        ];
        return view('master/kategori/show', $data);
    }

    public function new()
    {
        $data = ['title' => 'Tambah Kategori'];
        return view('master/kategori/form', $data);
    }

    public function create()
    {
        $rules = [
            'kode_kategori' => 'required|max_length[30]|is_unique[aset_kategori.kode_kategori]',
            'nama_kategori' => 'required|max_length[150]|is_unique[aset_kategori.nama_kategori]',
            'jenis_aset' => 'required|in_list[bangunan,ruangan,non_bangunan,lainnya]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kategoriModel->save([
            'kode_kategori' => $this->request->getPost('kode_kategori'),
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'jenis_aset' => $this->request->getPost('jenis_aset'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/master/kategori'))->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('/master/kategori'))->with('error', 'Data kategori tidak ditemukan.');
        }
        $data = [
            'title' => 'Edit Kategori',
            'kategori' => $kategori
        ];
        return view('master/kategori/form', $data);
    }

    public function update($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('/master/kategori'))->with('error', 'Data kategori tidak ditemukan.');
        }

        $rules = [
            'kode_kategori' => "required|max_length[30]|is_unique[aset_kategori.kode_kategori,kt_id,{$id}]",
            'nama_kategori' => "required|max_length[150]|is_unique[aset_kategori.nama_kategori,kt_id,{$id}]",
            'jenis_aset' => 'required|in_list[bangunan,ruangan,non_bangunan,lainnya]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kategoriModel->update($id, [
            'kode_kategori' => $this->request->getPost('kode_kategori'),
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'jenis_aset' => $this->request->getPost('jenis_aset'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/master/kategori'))->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('/master/kategori'))->with('error', 'Data kategori tidak ditemukan.');
        }

        $this->kategoriModel->delete($id);
        $this->kategoriModel->update($id, ['is_active' => 0]);

        return redirect()->to(base_url('/master/kategori'))->with('success', 'Kategori berhasil dihapus.');
    }
}
