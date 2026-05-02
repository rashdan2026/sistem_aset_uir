<?php

namespace App\Controllers\Master;

use App\Models\SubUnitModel;
use App\Models\Reference\UnitKerjaReadOnlyModel;
use App\Controllers\Master\Traits\SearchFilterTrait;
use CodeIgniter\Controller;

class SubUnitController extends Controller
{
    use SearchFilterTrait;

    protected $subUnitModel;
    protected $unitKerjaModel;

    public function __construct()
    {
        $this->subUnitModel = new SubUnitModel();
        $this->unitKerjaModel = new UnitKerjaReadOnlyModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;

        $filterFields = ['unit_kerja_id'];
        $params = $this->getFilterParams($filterFields);
        $searchQuery = $this->buildSearchQuery($params);

        $data = $this->getSubUnitData($perPage, $page, $params);
        $data['searchQuery'] = $searchQuery;
        $data['params'] = $params;
        $data['filterOptions'] = $this->getFilterOptions();

        return view('master/subunit/index', $data);
    }

    private function getSubUnitData(int $perPage, int $page, array $params): array
    {
        $offset = ($page - 1) * $perPage;
        $builder = $this->subUnitModel->builder();

        $builder->select('aset_sub_units.*, tbl_unit_kerja.nama_unit as unit_nama')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_sub_units.unit_kerja_id', 'left');

        if (!empty($params['q'])) {
            $builder->groupStart();
            $builder->orLike('aset_sub_units.kode_sub_unit', $params['q'], 'both');
            $builder->orLike('aset_sub_units.nama_sub_unit', $params['q'], 'both');
            $builder->orLike('tbl_unit_kerja.nama_unit', $params['q'], 'both');
            $builder->groupEnd();
        }

        if (!empty($params['unit_kerja_id'])) {
            $builder->where('aset_sub_units.unit_kerja_id', $params['unit_kerja_id']);
        }

        if (isset($params['is_active'])) {
            $builder->where('aset_sub_units.is_active', $params['is_active']);
        } else {
            $builder->where('aset_sub_units.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('tbl_unit_kerja.nama_unit', 'ASC')
            ->orderBy('aset_sub_units.nama_sub_unit', 'ASC')
            ->get($perPage, $offset)
            ->getResultArray();

        return [
            'title' => 'Master Sub Unit',
            'subUnits' => $records,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page
        ];
    }

    public function getFilterOptions(): array
    {
        return [
            'unit_kerja' => $this->unitKerjaModel->getActiveOptions()
        ];
    }

    public function show($id)
    {
        $subUnit = $this->subUnitModel->withUnitKerja()->find($id);

        if (!$subUnit) {
            return redirect()->back()->with('error', 'Data sub unit tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Sub Unit',
            'subUnit' => $subUnit
        ];

        return view('master/subunit/show', $data);
    }

    public function new()
    {
        $data = [
            'title' => 'Tambah Sub Unit',
            'unitKerja' => $this->unitKerjaModel->getActiveOptions()
        ];

        return view('master/subunit/form', $data);
    }

    public function create()
    {
        $rules = [
            'unit_kerja_id' => 'required|integer',
            'kode_sub_unit' => 'required|max_length[30]',
            'nama_sub_unit' => 'required|max_length[150]',
            'jenis_sub_unit' => 'permit_empty|max_length[50]',
            'keterangan' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $unitKerjaId = $this->request->getPost('unit_kerja_id');
        $kodeSubUnit = $this->request->getPost('kode_sub_unit');

        if (!$this->subUnitModel->isKodeUnique($kodeSubUnit, $unitKerjaId)) {
            return redirect()->back()->withInput()->with('errors', ['kode_sub_unit' => 'Kode sub unit sudah digunakan dalam unit kerja ini.']);
        }

        $data = [
            'unit_kerja_id' => $unitKerjaId,
            'kode_sub_unit' => $kodeSubUnit,
            'nama_sub_unit' => $this->request->getPost('nama_sub_unit'),
            'jenis_sub_unit' => $this->request->getPost('jenis_sub_unit'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ];

        $this->subUnitModel->save($data);

        return redirect()->to(base_url('/master/sub-units'))->with('success', 'Sub Unit berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $subUnit = $this->subUnitModel->find($id);

        if (!$subUnit) {
            return redirect()->back()->with('error', 'Data sub unit tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Sub Unit',
            'subUnit' => $subUnit,
            'unitKerja' => $this->unitKerjaModel->getActiveOptions()
        ];

        return view('master/subunit/form', $data);
    }

    public function update($id)
    {
        $subUnit = $this->subUnitModel->find($id);

        if (!$subUnit) {
            return redirect()->back()->with('error', 'Data sub unit tidak ditemukan.');
        }

        $rules = [
            'unit_kerja_id' => 'required|integer',
            'kode_sub_unit' => 'required|max_length[30]',
            'nama_sub_unit' => 'required|max_length[150]',
            'jenis_sub_unit' => 'permit_empty|max_length[50]',
            'keterangan' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $unitKerjaId = $this->request->getPost('unit_kerja_id');
        $kodeSubUnit = $this->request->getPost('kode_sub_unit');

        if (!$this->subUnitModel->isKodeUnique($kodeSubUnit, $unitKerjaId, $id)) {
            return redirect()->back()->withInput()->with('errors', ['kode_sub_unit' => 'Kode sub unit sudah digunakan dalam unit kerja ini.']);
        }

        $data = [
            'unit_kerja_id' => $this->request->getPost('unit_kerja_id'),
            'kode_sub_unit' => $this->request->getPost('kode_sub_unit'),
            'nama_sub_unit' => $this->request->getPost('nama_sub_unit'),
            'jenis_sub_unit' => $this->request->getPost('jenis_sub_unit'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->subUnitModel->update($id, $data);

        return redirect()->to(base_url('/master/sub-units'))->with('success', 'Sub Unit berhasil diperbarui.');
    }

    public function delete($id)
    {
        $subUnit = $this->subUnitModel->find($id);

        if (!$subUnit) {
            return redirect()->back()->with('error', 'Data sub unit tidak ditemukan.');
        }

        $this->subUnitModel->delete($id);
        $this->subUnitModel->update($id, ['is_active' => 0]);

        return redirect()->to(base_url('/master/sub-units'))->with('success', 'Sub Unit berhasil dihapus.');
    }
}