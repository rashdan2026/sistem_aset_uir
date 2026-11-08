<?php

namespace App\Controllers\Master;

use App\Controllers\Master\Traits\SearchFilterTrait;
use App\Models\Master\KategoriModel;
use App\Models\Master\GolonganModel;
use CodeIgniter\Controller;

class KategoriController extends Controller
{
    use SearchFilterTrait;

    protected $kategoriModel;
    protected $golonganModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
        $this->golonganModel = new GolonganModel();
        helper(['form', 'url', 'kode']);
    }

    public function index()
    {
        $perPage = 50;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;

        $filterFields = ['gl_id'];
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
        $db = db_connect();
        $builder = $db->table('aset_kategori k');
        $builder->select('k.*, g.nama_golongan, g.kode_golongan, g.gl_id as gol_id')
            ->join('aset_golongan g', 'g.gl_id = k.gl_id', 'left');

        if (!empty($params['q'])) {
            $builder->groupStart();
            $builder->orLike('k.kode_kategori', $params['q'], 'both');
            $builder->orLike('k.nama_kategori', $params['q'], 'both');
            $builder->groupEnd();
        }

        if (!empty($params['gl_id'])) {
            $builder->where('k.gl_id', $params['gl_id']);
        }

        if (!isset($params['is_active'])) {
            $builder->where('k.is_active', 1);
        }

        $builder->where('k.deleted_at', null);

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('k.nama_kategori', 'ASC')
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
        $golonganOptions = [];
        $golongan = $this->golonganModel->getActiveOptions();
        foreach ($golongan as $g) {
            $golonganOptions[] = ['value' => $g['gl_id'], 'label' => $g['nama_golongan']];
        }

        return [
            'golongan' => $golonganOptions
        ];
    }

    public function show($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('/master/kategori'))->with('error', 'Data kategori tidak ditemukan.');
        }

        $golongan = null;
        if (!empty($kategori['gl_id'])) {
            $golongan = $this->golonganModel->find($kategori['gl_id']);
        }

        $data = [
            'title' => 'Detail Kategori',
            'kategori' => $kategori,
            'golongan' => $golongan
        ];
        return view('master/kategori/show', $data);
    }

    public function new()
    {
        $golonganOptions = $this->golonganModel->getActiveOptions();
        $data = ['title' => 'Tambah Kategori', 'golonganOptions' => $golonganOptions];
        return view('master/kategori/form', $data);
    }

    public function create()
    {
        $rules = [
            'nama_kategori' => 'required|max_length[150]|is_unique[aset_kategori.nama_kategori]',
            'gl_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $namaKategori = trim($this->request->getPost('nama_kategori'));

        $exists = $this->kategoriModel
            ->where('nama_kategori', $namaKategori)
            ->where('is_active', 1)
            ->first();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nama Kategori "' . $namaKategori . '" sudah pernah diinput.');
        }

        $golonganId = (int) $this->request->getPost('gl_id');
        $golongan = $this->golonganModel->find($golonganId);
        if (!$golongan) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Golongan yang dipilih tidak valid. Golongan harus diinput terlebih dahulu.');
        }

        $this->kategoriModel->save([
            'nama_kategori' => $namaKategori,
            'gl_id' => $golonganId,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $kategoriId = $this->kategoriModel->getInsertID();
        $kodeKategori = generate_kode_kategori($kategoriId);
        $this->kategoriModel->update($kategoriId, ['kode_kategori' => $kodeKategori]);

        return redirect()->to(base_url('/master/kategori'))->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('/master/kategori'))->with('error', 'Data kategori tidak ditemukan.');
        }
        $golonganOptions = $this->golonganModel->getActiveOptions();
        $data = [
            'title' => 'Edit Kategori',
            'kategori' => $kategori,
            'golonganOptions' => $golonganOptions
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
            'nama_kategori' => "required|max_length[150]|is_unique[aset_kategori.nama_kategori,kt_id,{$id}]",
            'gl_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $namaKategori = trim($this->request->getPost('nama_kategori'));

        $exists = $this->kategoriModel
            ->where('nama_kategori', $namaKategori)
            ->where('is_active', 1)
            ->where('kt_id !=', $id)
            ->first();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nama Kategori "' . $namaKategori . '" sudah pernah diinput.');
        }

        $golonganId = (int) $this->request->getPost('gl_id');
        $golongan = $this->golonganModel->find($golonganId);
        if (!$golongan) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Golongan yang dipilih tidak valid.');
        }

        $this->kategoriModel->update($id, [
            'nama_kategori' => $namaKategori,
            'gl_id' => $golonganId,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/master/kategori'))->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete($id)
    {
        $kategori = $this->kategoriModel->withDeleted()->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('/master/kategori'))->with('error', 'Data kategori tidak ditemukan.');
        }

        if ($kategori['is_active'] == 0 && $kategori['deleted_at'] !== null) {
            return redirect()->to(base_url('/master/kategori'))->with('error', 'Data sudah tidak aktif.');
        }

        $this->kategoriModel->withDeleted()->update($id, [
            'is_active' => 0,
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/master/kategori'))->with('success', 'Kategori berhasil dihapus.');
    }

    public function search()
    {
        $q = $this->request->getGet('q');
        $golonganId = $this->request->getGet('gl_id');

        if (!$q || strlen($q) < 4) {
            return $this->response->setJSON([]);
        }

        $builder = $this->kategoriModel->builder();
        $builder->select('kt_id, nama_kategori, kode_kategori')
            ->like('nama_kategori', $q, 'both')
            ->where('is_active', 1);

        if (!empty($golonganId)) {
            $builder->where('gl_id', $golonganId);
        }

        $results = $builder->orderBy('nama_kategori', 'ASC')
            ->findAll(10);

        return $this->response->setJSON($results);
    }

    public function getByGolongan($golonganId)
    {
        $kategori = $this->kategoriModel->getByGolonganId((int) $golonganId);
        return $this->response->setJSON($kategori);
    }
}
