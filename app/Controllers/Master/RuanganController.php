<?php

namespace App\Controllers\Master;

use App\Models\Master\RuanganModel;
use App\Models\Master\LantaiModel;
use App\Models\SubUnitModel;
use App\Models\GedungModel;
use App\Models\Reference\PenanggungJawabReadOnlyModel;
use App\Controllers\Master\Traits\SearchFilterTrait;
use CodeIgniter\Controller;

class RuanganController extends Controller
{
    use SearchFilterTrait;

    protected $model;
    protected $lantaiModel;
    protected $subUnitModel;
    protected $gedungModel;
    protected $pjModel;

    public function __construct()
    {
        $this->model = new RuanganModel();
        $this->lantaiModel = new LantaiModel();
        $this->subUnitModel = new SubUnitModel();
        $this->gedungModel = new GedungModel();
        $this->pjModel = new PenanggungJawabReadOnlyModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;

        $filterFields = ['gedung_id', 'sub_unit_id', 'jenis_ruangan'];
        $params = $this->getFilterParams($filterFields);
        $searchQuery = $this->buildSearchQuery($params);

        $data = $this->getRuanganData($perPage, $page, $params);
        $data['searchQuery'] = $searchQuery;
        $data['params'] = $params;
        $data['filterOptions'] = $this->getFilterOptions();

        return view('master/ruangan/index', $data);
    }

    private function getRuanganData(int $perPage, int $page, array $params): array
    {
        $offset = ($page - 1) * $perPage;
        $builder = $this->model->builder();

        $builder->select('aset_ruangan.*,
                    aset_lantai.nama_lantai,
                    aset_lantai.nomor_lantai,
                    aset_gedung.gd_id as gedung_id_col, aset_gedung.nama_gedung,
                    aset_sub_units.nama_sub_unit,
                    tbl_unit_kerja.nama_unit as unit_nama,
                    ylpi_karyawan.nama_gelar as pj_nama,
                    ylpi_karyawan.npk as pj_npk')
            ->join('aset_lantai', 'aset_lantai.lt_id = aset_ruangan.lantai_id', 'left')
            ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
            ->join('aset_sub_units', 'aset_sub_units.su_id = aset_ruangan.sub_unit_id', 'left')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left')
            ->join('ylpi_karyawan', 'ylpi_karyawan.id_kpe = aset_ruangan.penanggung_jawab_id_kpe', 'left');

        if (!empty($params['q'])) {
            $builder->groupStart();
            $builder->orLike('aset_ruangan.kode_ruangan', $params['q'], 'both');
            $builder->orLike('aset_ruangan.nama_ruangan', $params['q'], 'both');
            $builder->orLike('aset_gedung.nama_gedung', $params['q'], 'both');
            $builder->orLike('aset_sub_units.nama_sub_unit', $params['q'], 'both');
            $builder->orLike('ylpi_karyawan.nama_gelar', $params['q'], 'both');
            $builder->groupEnd();
        }

        if (!empty($params['gedung_id'])) {
            $builder->where('aset_lantai.gedung_id', $params['gedung_id']);
        }

        if (!empty($params['sub_unit_id'])) {
            $builder->where('aset_ruangan.sub_unit_id', $params['sub_unit_id']);
        }

        if (!empty($params['jenis_ruangan'])) {
            $builder->where('aset_ruangan.jenis_ruangan', $params['jenis_ruangan']);
        }

        if (isset($params['is_active'])) {
            $builder->where('aset_ruangan.is_active', $params['is_active']);
        } else {
            $builder->where('aset_ruangan.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('aset_gedung.nama_gedung', 'ASC')
            ->orderBy('aset_lantai.nomor_lantai', 'ASC')
            ->orderBy('aset_ruangan.nama_ruangan', 'ASC')
            ->get($perPage, $offset)
            ->getResultArray();

        return [
            'title' => 'Master Ruangan',
            'records' => $records,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page
        ];
    }

    public function getFilterOptions(): array
    {
        return [
            'gedung' => $this->gedungModel->where('is_active', 1)->orderBy('nama_gedung', 'ASC')->findAll(),
            'sub_unit' => $this->subUnitModel->where('is_active', 1)->orderBy('nama_sub_unit', 'ASC')->findAll(),
            'jenis_ruangan' => $this->model->getJenisRuanganOptions()
        ];
    }

    public function new()
    {
        $data = [
            'title' => 'Tambah Ruangan',
            'gedung' => $this->gedungModel->where('is_active', 1)->orderBy('nama_gedung', 'ASC')->findAll(),
            'subUnit' => $this->subUnitModel->where('is_active', 1)->orderBy('nama_sub_unit', 'ASC')->findAll(),
            'pjData' => null,
            'jenisRuanganOptions' => $this->model->getJenisRuanganOptions(),
        ];
        return view('master/ruangan/form', $data);
    }

    public function create()
    {
        $rules = [
            'lantai_id' => 'required|integer',
            'sub_unit_id' => 'required|integer',
            'kode_ruangan' => 'required|max_length[30]',
            'nama_ruangan' => 'required|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'lantai_id' => $this->request->getPost('lantai_id'),
            'sub_unit_id' => $this->request->getPost('sub_unit_id'),
            'kode_ruangan' => $this->request->getPost('kode_ruangan'),
            'nama_ruangan' => $this->request->getPost('nama_ruangan'),
            'jenis_ruangan' => $this->request->getPost('jenis_ruangan'),
            'penanggung_jawab_id_kpe' => $this->request->getPost('penanggung_jawab_id_kpe') ?: null,
            'kapasitas' => $this->request->getPost('kapasitas') ?: null,
            'luas_m2' => $this->request->getPost('luas_m2') ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/master/ruangan'))->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/ruangan'))->with('error', 'Data tidak ditemukan.');
        }

        $lantai = $this->lantaiModel->find($record['lantai_id']);
        $gedungId = $lantai ? ($lantai['gedung_id'] ?? null) : null;

        $record['gedung_id'] = $gedungId;

        $pjData = null;
        if (!empty($record['penanggung_jawab_id_kpe'])) {
            $pjData = $this->pjModel->getById($record['penanggung_jawab_id_kpe']);
        }

        $data = [
            'title' => 'Edit Ruangan',
            'record' => $record,
            'gedung' => $this->gedungModel->where('is_active', 1)->orderBy('nama_gedung', 'ASC')->findAll(),
            'lantai' => $gedungId ? $this->lantaiModel->where('gedung_id', $gedungId)->where('is_active', 1)->orderBy('nomor_lantai', 'ASC')->findAll() : [],
            'subUnit' => $this->subUnitModel->where('is_active', 1)->orderBy('nama_sub_unit', 'ASC')->findAll(),
            'pjData' => $pjData,
            'jenisRuanganOptions' => $this->model->getJenisRuanganOptions(),
        ];
        return view('master/ruangan/form', $data);
    }

    public function update($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/ruangan'))->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'lantai_id' => 'required|integer',
            'sub_unit_id' => 'required|integer',
            'kode_ruangan' => 'required|max_length[30]',
            'nama_ruangan' => 'required|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'lantai_id' => $this->request->getPost('lantai_id'),
            'sub_unit_id' => $this->request->getPost('sub_unit_id'),
            'kode_ruangan' => $this->request->getPost('kode_ruangan'),
            'nama_ruangan' => $this->request->getPost('nama_ruangan'),
            'jenis_ruangan' => $this->request->getPost('jenis_ruangan'),
            'penanggung_jawab_id_kpe' => $this->request->getPost('penanggung_jawab_id_kpe') ?: null,
            'kapasitas' => $this->request->getPost('kapasitas') ?: null,
            'luas_m2' => $this->request->getPost('luas_m2') ?: null,
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/master/ruangan'))->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/ruangan'))->with('error', 'Data tidak ditemukan.');
        }
        $this->model->delete($id);
        $this->model->update($id, ['is_active' => 0]);
        return redirect()->to(base_url('/master/ruangan'))->with('success', 'Ruangan berhasil dihapus.');
    }
}