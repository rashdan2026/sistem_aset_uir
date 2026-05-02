<?php

namespace App\Controllers\Master;

use App\Models\GedungModel;
use App\Models\Master\LantaiModel;
use App\Models\Reference\UnitKerjaReadOnlyModel;
use App\Controllers\Master\Traits\SearchFilterTrait;
use CodeIgniter\Controller;

class GedungController extends Controller
{
    use SearchFilterTrait;

    protected $gedungModel;
    protected $unitKerjaModel;
    protected $lantaiModel;

    public function __construct()
    {
        $this->gedungModel = new GedungModel();
        $this->unitKerjaModel = new UnitKerjaReadOnlyModel();
        $this->lantaiModel = new LantaiModel();
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

        $data = $this->getGedungData($perPage, $page, $params);
        $data['searchQuery'] = $searchQuery;
        $data['params'] = $params;
        $data['filterOptions'] = $this->getFilterOptions();

        return view('master/gedung/index', $data);
    }

    private function getGedungData(int $perPage, int $page, array $params): array
    {
        $offset = ($page - 1) * $perPage;
        $builder = $this->gedungModel->builder();

        $builder->select('aset_gedung.*, tbl_unit_kerja.nama_unit')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left');

        if (!empty($params['q'])) {
            $builder->groupStart();
            $builder->orLike('aset_gedung.kode_gedung', $params['q'], 'both');
            $builder->orLike('aset_gedung.nama_gedung', $params['q'], 'both');
            $builder->orLike('tbl_unit_kerja.nama_unit', $params['q'], 'both');
            $builder->groupEnd();
        }

        if (!empty($params['unit_kerja_id'])) {
            $builder->where('aset_gedung.unit_kerja_id', $params['unit_kerja_id']);
        }

        if (isset($params['is_active'])) {
            $builder->where('aset_gedung.is_active', $params['is_active']);
        } else {
            $builder->where('aset_gedung.is_active', 1);
        }

        $total = $builder->countAllResults(false);
        $records = $builder->orderBy('tbl_unit_kerja.nama_unit', 'ASC')
            ->orderBy('aset_gedung.nama_gedung', 'ASC')
            ->get($perPage, $offset)
            ->getResultArray();

        return [
            'title' => 'Master Gedung',
            'gedung' => $records,
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
        $gedung = $this->gedungModel->withUnit()->find($id);

        if (!$gedung) {
            return redirect()->back()->with('error', 'Data gedung tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Gedung',
            'gedung' => $gedung,
            'lantai' => $this->lantaiModel->where('gedung_id', $id)->where('is_active', 1)->orderBy('nomor_lantai', 'ASC')->findAll()
        ];

        return view('master/gedung/show', $data);
    }

    public function new()
    {
        $data = [
            'title' => 'Tambah Gedung',
            'unitKerja' => $this->unitKerjaModel->getActiveOptions()
        ];

        return view('master/gedung/form', $data);
    }

    public function create()
    {
        $rules = [
            'unit_kerja_id' => 'required|integer',
            'kode_gedung' => 'required|max_length[30]',
            'nama_gedung' => 'required|max_length[150]',
            'jumlah_lantai' => 'required|integer|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kodeGedung = $this->request->getPost('kode_gedung');
        if (!$this->gedungModel->isKodeUnique($kodeGedung)) {
            return redirect()->back()->withInput()->with('errors', ['kode_gedung' => 'Kode gedung sudah digunakan.']);
        }

        $jumlahLantai = (int) $this->request->getPost('jumlah_lantai');

        $data = [
            'unit_kerja_id' => $this->request->getPost('unit_kerja_id'),
            'kode_gedung' => $this->request->getPost('kode_gedung'),
            'nama_gedung' => $this->request->getPost('nama_gedung'),
            'jumlah_lantai' => $jumlahLantai,
            'alamat_ringkas' => $this->request->getPost('alamat_ringkas'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => 1,
        ];

        try {
            $this->gedungModel->save($data);
            $gedungId = $this->gedungModel->getInsertID();

            for ($i = 1; $i <= $jumlahLantai; $i++) {
                $this->lantaiModel->save([
                    'gedung_id' => $gedungId,
                    'kode_lantai' => 'LT_' . $gedungId . '_' . $i,
                    'nama_lantai' => 'Lantai ' . $i,
                    'nomor_lantai' => $i,
                    'is_active' => 1,
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }

        return redirect()->to(base_url('/master/gedung'))->with('success', 'Gedung dan ' . $jumlahLantai . ' lantai berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $gedung = $this->gedungModel->find($id);

        if (!$gedung) {
            return redirect()->back()->with('error', 'Data gedung tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Gedung',
            'gedung' => $gedung,
            'unitKerja' => $this->unitKerjaModel->getActiveOptions()
        ];

        return view('master/gedung/form', $data);
    }

    public function update($id)
    {
        $gedung = $this->gedungModel->find($id);

        if (!$gedung) {
            return redirect()->back()->with('error', 'Data gedung tidak ditemukan.');
        }

        $rules = [
            'unit_kerja_id' => 'required|integer',
            'kode_gedung' => 'required|max_length[30]',
            'nama_gedung' => 'required|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kodeGedung = $this->request->getPost('kode_gedung');
        if (!$this->gedungModel->isKodeUnique($kodeGedung, $id)) {
            return redirect()->back()->withInput()->with('errors', ['kode_gedung' => 'Kode gedung sudah digunakan.']);
        }

        $data = [
            'unit_kerja_id' => $this->request->getPost('unit_kerja_id'),
            'kode_gedung' => $this->request->getPost('kode_gedung'),
            'nama_gedung' => $this->request->getPost('nama_gedung'),
            'alamat_ringkas' => $this->request->getPost('alamat_ringkas'),
            'keterangan' => $this->request->getPost('keterangan'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->gedungModel->update($id, $data);

        return redirect()->to(base_url('/master/gedung'))->with('success', 'Gedung berhasil diperbarui.');
    }

    public function delete($id)
    {
        $gedung = $this->gedungModel->find($id);

        if (!$gedung) {
            return redirect()->back()->with('error', 'Data gedung tidak ditemukan.');
        }

        $this->gedungModel->delete($id);
        $this->gedungModel->update($id, ['is_active' => 0]);

        return redirect()->to(base_url('/master/gedung'))->with('success', 'Gedung berhasil dihapus.');
    }
}