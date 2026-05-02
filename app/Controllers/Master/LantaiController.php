<?php

namespace App\Controllers\Master;

use App\Models\Master\LantaiModel;
use App\Models\GedungModel;
use App\Controllers\Master\Traits\SearchFilterTrait;
use CodeIgniter\Controller;

class LantaiController extends Controller
{
    use SearchFilterTrait;

    protected $model;
    protected $gedungModel;

    public function __construct()
    {
        $this->model = new LantaiModel();
        $this->gedungModel = new GedungModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;

        $filterFields = ['gedung_id'];
        $params = $this->getFilterParams($filterFields);
        $searchQuery = $this->buildSearchQuery($params);

        $data = $this->getLantaiData($perPage, $page, $params);
        $data['searchQuery'] = $searchQuery;
        $data['params'] = $params;
        $data['filterOptions'] = $this->getFilterOptions();

        return view('master/lantai/index', $data);
    }

    private function getLantaiData(int $perPage, int $page, array $params): array
    {
        $offset = ($page - 1) * $perPage;
        $builder = $this->model->builder();

        $builder->select('aset_lantai.*, aset_gedung.nama_gedung, aset_gedung.kode_gedung, tbl_unit_kerja.nama_unit as unit_nama')
            ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left');

        if (!empty($params['q'])) {
            $builder->groupStart();
            $builder->orLike('aset_lantai.kode_lantai', $params['q'], 'both');
            $builder->orLike('aset_lantai.nama_lantai', $params['q'], 'both');
            $builder->orLike('aset_gedung.nama_gedung', $params['q'], 'both');
            $builder->groupEnd();
        }

        if (!empty($params['gedung_id'])) {
            $builder->where('aset_lantai.gedung_id', $params['gedung_id']);
        }

        if (isset($params['is_active'])) {
            $builder->where('aset_lantai.is_active', $params['is_active']);
        } else {
            $builder->where('aset_lantai.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('aset_gedung.nama_gedung', 'ASC')
            ->orderBy('aset_lantai.nomor_lantai', 'ASC')
            ->get($perPage, $offset)
            ->getResultArray();

        return [
            'title' => 'Master Lantai',
            'records' => $records,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page
        ];
    }

    public function getFilterOptions(): array
    {
        return [
            'gedung' => $this->gedungModel->where('is_active', 1)->orderBy('nama_gedung', 'ASC')->findAll()
        ];
    }

    public function new()
    {
        $data = [
            'title' => 'Tambah Lantai',
            'gedung' => $this->gedungModel->where('is_active', 1)->orderBy('nama_gedung', 'ASC')->findAll()
        ];
        return view('master/lantai/form', $data);
    }

    public function create()
    {
        $rules = [
            'gedung_id' => 'required|integer',
            'kode_lantai' => 'required|max_length[30]',
            'nama_lantai' => 'required|max_length[100]',
            'nomor_lantai' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'gedung_id' => $this->request->getPost('gedung_id'),
            'kode_lantai' => $this->request->getPost('kode_lantai'),
            'nama_lantai' => $this->request->getPost('nama_lantai'),
            'nomor_lantai' => $this->request->getPost('nomor_lantai'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ]);

        return redirect()->to(base_url('/master/lantai'))->with('success', 'Lantai berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/lantai'))->with('error', 'Data tidak ditemukan.');
        }
        $data = [
            'title' => 'Edit Lantai',
            'record' => $record,
            'gedung' => $this->gedungModel->where('is_active', 1)->orderBy('nama_gedung', 'ASC')->findAll()
        ];
        return view('master/lantai/form', $data);
    }

    public function update($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/lantai'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'gedung_id' => 'required|integer',
            'kode_lantai' => 'required|max_length[30]',
            'nama_lantai' => 'required|max_length[100]',
            'nomor_lantai' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'gedung_id' => $this->request->getPost('gedung_id'),
            'kode_lantai' => $this->request->getPost('kode_lantai'),
            'nama_lantai' => $this->request->getPost('nama_lantai'),
            'nomor_lantai' => $this->request->getPost('nomor_lantai'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/master/lantai'))->with('success', 'Lantai berhasil diperbarui.');
    }

    public function byGedung($gedungId)
    {
        $lantai = $this->model->select('lt_id as id, gedung_id, kode_lantai, nama_lantai, nomor_lantai')
            ->where('gedung_id', $gedungId)
            ->where('is_active', 1)
            ->orderBy('nomor_lantai', 'ASC')
            ->findAll();
        return $this->response->setJSON($lantai);
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/lantai'))->with('error', 'Data tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $gedungModel = new \App\Models\GedungModel();
            $gedung = $gedungModel->find($record['gedung_id']);

            if ($gedung && $gedung['jumlah_lantai'] > 0) {
                $gedungModel->update($record['gedung_id'], [
                    'jumlah_lantai' => $gedung['jumlah_lantai'] - 1
                ]);
            }

            $this->model->delete($id);
            $this->model->update($id, ['is_active' => 0]);

            $db->transCommit();
            return redirect()->to(base_url('/master/lantai'))->with('success', 'Lantai berhasil dihapus.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(base_url('/master/lantai'))->with('error', 'Gagal menghapus lantai: ' . $e->getMessage());
        }
    }
}