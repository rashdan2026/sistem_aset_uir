<?php

namespace App\Controllers\Master;

use App\Models\Master\AsetModel;
use App\Models\Master\KategoriModel;
use App\Models\Master\SubKategoriModel;
use App\Models\Master\GolonganModel;
use App\Models\Master\MerkModel;
use App\Models\Master\TypeModel;
use App\Models\Master\KondisiBarangModel;
use App\Models\Master\SumberDanaModel;
use App\Models\SubUnitModel;
use App\Models\Reference\UnitKerjaAllowedModel;
use App\Models\GedungModel;
use App\Models\Master\LantaiModel;
use App\Models\Master\RuanganModel;
use App\Services\AssetNumberService;
use CodeIgniter\Controller;

class AsetController extends Controller
{
    protected $asetModel;
    protected $kategoriModel;
    protected $subKategoriModel;
    protected $golonganModel;
    protected $merkModel;
    protected $typeModel;
    protected $kondisiModel;
    protected $sumberDanaModel;
    protected $subUnitModel;
    protected $unitKerjaModel;
    protected $assetNumberService;

public function __construct()
    {
        $this->asetModel = new AsetModel();
        $this->kategoriModel = new KategoriModel();
        $this->subKategoriModel = new SubKategoriModel();
        $this->golonganModel = new GolonganModel();
        $this->merkModel = new MerkModel();
        $this->typeModel = new TypeModel();
        $this->kondisiModel = new KondisiBarangModel();
        $this->sumberDanaModel = new SumberDanaModel();
        $this->unitKerjaModel = new UnitKerjaAllowedModel();
        $this->subUnitModel = new SubUnitModel();
        $this->gedungModel = new GedungModel();
        $this->lantaiModel = new LantaiModel();
        $this->ruanganModel = new RuanganModel();
        $this->assetNumberService = new AssetNumberService();
        helper(['form', 'url', 'pj', 'aset_column']);
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $searchCol = $this->request->getGet('search_col');
        $status = $this->request->getGet('status');
        $kategori = $this->request->getGet('kategori_id');
        $inputMode = $this->request->getGet('input_mode');
        $sortCol = $this->request->getGet('sort');
        $sortDir = $this->request->getGet('dir');

        helper('cookie');
        $visibleColumns = get_aset_visible_columns();
        $allColumns = get_aset_all_columns();
        $columnMap = get_aset_column_map();

        $validSortDirs = ['asc', 'desc'];
        if (!in_array($sortDir, $validSortDirs)) {
            $sortDir = 'asc';
        }
        if ($sortCol && !array_key_exists($sortCol, $allColumns)) {
            $sortCol = null;
        }

        $selectFields = ['a.all_id', 'a.nomor_aset_baru', 'a.nama_aset', 'a.status_aset', 'a.input_mode', 'a.batch_id', 'a.updated_at'];
        $joinTables = [
            'aset_kategori ak' => 'ak.kt_id = a.kategori_id',
            'aset_sub_kategori ask' => 'ask.sk_id = a.sub_kategori_id',
            'aset_kondisi_barang akb' => 'akb.kd_id = a.kondisi_id',
        ];

        if (in_array('nama_merk', $visibleColumns) || $searchCol === 'nama_merk' || $sortCol === 'nama_merk') {
            $selectFields[] = 'am.nama_merk';
            $joinTables['aset_merk am'] = 'am.mr_id = a.merk_id';
        }
        if (in_array('nama_type', $visibleColumns) || $searchCol === 'nama_type' || $sortCol === 'nama_type') {
            $selectFields[] = 'at.nama_type';
            $joinTables['aset_type at'] = 'at.ty_id = a.type_id';
        }
        if (in_array('nama_sumber_dana', $visibleColumns) || $searchCol === 'nama_sumber_dana' || $sortCol === 'nama_sumber_dana') {
            $selectFields[] = 'asd.nama_sumber_dana';
            $joinTables['aset_sumber_dana asd'] = 'asd.sd_id = a.sumber_dana_id';
        }
        if (in_array('nama_unit', $visibleColumns) || $searchCol === 'nama_unit' || $sortCol === 'nama_unit') {
            $selectFields[] = 'tu.nama_unit';
            $joinTables['tbl_unit_kerja tu'] = 'tu.id_unit_kerja = a.unit_kerja_id';
        }
        if (in_array('nama_sub_unit', $visibleColumns) || $searchCol === 'nama_sub_unit' || $sortCol === 'nama_sub_unit') {
            $selectFields[] = 'asu.nama_sub_unit';
            $joinTables['aset_sub_units asu'] = 'asu.su_id = a.sub_unit_id';
        }
        if (in_array('nama_ruangan', $visibleColumns) || $searchCol === 'nama_ruangan' || $sortCol === 'nama_ruangan') {
            $selectFields[] = 'ar.nama_ruangan';
            $joinTables['aset_ruangan ar'] = 'ar.rg_id = a.ruangan_id';
        }
        if (in_array('nama_penanggung_jawab', $visibleColumns) || $searchCol === 'nama_penanggung_jawab' || $sortCol === 'nama_penanggung_jawab') {
            $selectFields[] = 'yk.nama_gelar as nama_penanggung_jawab';
            $selectFields[] = 'yk.tgl_lahir as pj_tgl_lahir';
            $selectFields[] = 'jp.nama_jabatan as pj_nama_jabatan';
            $joinTables['ylpi_karyawan yk'] = 'yk.id_kpe = a.penanggung_jawab_id_kpe';
            $joinTables['tbl_jabatan_pegawai jp'] = 'jp.id_jabatan_pegawai = yk.jabatan_struktural AND jp.flag_aktif = 1';
        }
        if (in_array('nilai_perolehan', $visibleColumns) || $sortCol === 'nilai_perolehan') {
            $selectFields[] = 'a.nilai_perolehan';
        }
        if (in_array('tahun_perolehan', $visibleColumns) || $sortCol === 'tahun_perolehan') {
            $selectFields[] = 'a.tahun_perolehan';
        }
        if (in_array('nama_kategori', $visibleColumns) || $searchCol === 'nama_kategori' || $sortCol === 'nama_kategori') {
            $selectFields[] = 'ak.nama_kategori';
        }
        if (in_array('nama_sub_kategori', $visibleColumns) || $searchCol === 'nama_sub_kategori' || $sortCol === 'nama_sub_kategori') {
            $selectFields[] = 'ask.nama_sub_kategori';
        }
        if (in_array('nama_kondisi', $visibleColumns) || $searchCol === 'nama_kondisi' || $sortCol === 'nama_kondisi') {
            $selectFields[] = 'akb.nama_kondisi';
        }

        $selectFields = array_unique($selectFields);

        $builder = db_connect()->table('aset_all_uir a');

        if (!empty($search)) {
            if (!empty($searchCol) && array_key_exists($searchCol, $columnMap)) {
                $builder->like($columnMap[$searchCol], $search);
            } else {
                $builder->groupStart();
                $builder->orLike('a.all_id', $search);
                if (in_array('nomor_aset_baru', $visibleColumns)) {
                    $builder->orLike('a.nomor_aset_baru', $search);
                }
                if (in_array('nama_kategori', $visibleColumns) || isset($joinTables['aset_kategori ak'])) {
                    $builder->orLike('ak.nama_kategori', $search);
                }
                if (in_array('nama_sub_kategori', $visibleColumns) || isset($joinTables['aset_sub_kategori ask'])) {
                    $builder->orLike('ask.nama_sub_kategori', $search);
                }
                if (isset($joinTables['aset_merk am'])) {
                    $builder->orLike('am.nama_merk', $search);
                }
                if (isset($joinTables['aset_type at'])) {
                    $builder->orLike('at.nama_type', $search);
                }
                $builder->groupEnd();
            }
        }

        if (!empty($status)) {
            $builder->where('a.status_aset', $status);
        }

        if (!empty($kategori)) {
            $builder->where('a.kategori_id', $kategori);
        }

        if (!empty($inputMode)) {
            $builder->where('a.input_mode', $inputMode);
        }

        $builder->where('a.deleted_at', null);

        foreach ($joinTables as $table => $on) {
            $builder->join($table, $on, 'left');
        }

        $total = $builder->countAllResults(false);
        $perPage = 50;
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $currentPage = ($currentPage < 1) ? 1 : $currentPage;
        $offset = ($currentPage - 1) * $perPage;
        $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

        if ($sortCol && isset($columnMap[$sortCol])) {
            $builder->orderBy($columnMap[$sortCol], $sortDir);
        } else {
            $builder->orderBy('a.updated_at', 'DESC');
        }

        $records = $builder
            ->select(implode(', ', $selectFields))
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $searchableColumns = [];
        foreach ($visibleColumns as $col) {
            if (isset($allColumns[$col])) {
                $searchableColumns[$col] = $allColumns[$col];
            }
        }

        $data = [
            'title' => 'Registrasi Aset',
            'records' => $records,
            'total' => $total,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'search' => $search,
            'searchCol' => $searchCol,
            'status' => $status,
            'inputMode' => $inputMode,
            'kategoriFilter' => $kategori,
            'kategoriOptions' => $this->kategoriModel->where('is_active', 1)->orderBy('nama_kategori', 'ASC')->findAll(),
            'visibleColumns' => $visibleColumns,
            'allColumns' => $allColumns,
            'searchableColumns' => $searchableColumns,
            'sortCol' => $sortCol,
            'sortDir' => $sortDir,
        ];

        return view('master/aset/index', $data);
    }

    public function save_column_preferences()
    {
        helper('cookie');

        $columns = $this->request->getGet('columns');
        $decoded = json_decode($columns, true);

        if (!is_array($decoded) || count($decoded) === 0) {
            return redirect()->to('/master/aset')->with('error', 'Pilih minimal 1 kolom.');
        }

        $validColumns = array_keys(get_aset_all_columns());
        foreach ($decoded as $col) {
            if (!in_array($col, $validColumns)) {
                return redirect()->to('/master/aset');
            }
        }

        setcookie('aset_column_prefs', json_encode($decoded), time() + 60 * 60 * 24 * 365, '/');

        return redirect()->to('/master/aset');
    }

    public function new()
    {
        $kategoriId = (int) $this->request->getGet('kategori_id');
        $subKategoriId = (int) $this->request->getGet('sub_kategori_id');

        $data = [
            'title' => 'Registrasi Aset Baru',
            'record' => null,
            'kategori' => $this->kategoriModel->where('is_active', 1)->orderBy('nama_kategori', 'ASC')->findAll(),
            'subKategori' => $kategoriId ? $this->subKategoriModel->where('kategori_id', $kategoriId)->where('is_active', 1)->orderBy('nama_sub_kategori', 'ASC')->findAll() : [],
            'golongan' => $kategoriId ? $this->golonganModel->getByKategoriId($kategoriId) : [],
            'merk' => $this->merkModel->where('is_active', 1)->orderBy('nama_merk', 'ASC')->findAll(),
            'type' => [],
            'kondisi' => $this->kondisiModel->where('is_active', 1)->orderBy('level_kondisi', 'ASC')->findAll(),
            'sumberDana' => $this->sumberDanaModel->where('is_active', 1)->orderBy('nama_sumber_dana', 'ASC')->findAll(),
            'unitKerja' => $this->unitKerjaModel->getActiveOptions(),
            'subUnit' => [],
            'ruangan' => [],
            'selectedKategoriId' => $kategoriId,
            'selectedSubKategoriId' => $subKategoriId,
        ];

        return view('master/aset/form', $data);
    }

    public function create()
    {
        $kategoriId = (int) $this->request->getPost('kategori_id');
        $subKategoriId = (int) $this->request->getPost('sub_kategori_id');
        $unitKerjaId = (int) $this->request->getPost('unit_kerja_id');

        $subKategori = $this->subKategoriModel->find($subKategoriId);
        if (!$subKategori) {
            return redirect()->back()->withInput()->with('error', 'Sub Kategori tidak ditemukan.');
        }

        $unitKerja = $this->unitKerjaModel->getById($unitKerjaId);
        if (!$unitKerja) {
            return redirect()->back()->withInput()->with('error', 'Unit Kerja tidak ditemukan atau tidak valid.');
        }

        $namaSubKategori = $subKategori['nama_sub_kategori'];
        $namaUnitKerja = $unitKerja['nama_unit'];
        $namaAset = $namaSubKategori . ' / ' . $namaUnitKerja;

        $rules = [
            'kategori_id' => 'required|integer',
            'sub_kategori_id' => 'required|integer',
            'unit_kerja_id' => 'required|integer',
            'kondisi_id' => 'required|integer',
            'sumber_dana_id' => 'required|integer',
        ];

        if (!empty($subKategori['wajib_merk'])) {
            $rules['merk_id'] = 'required|integer';
        }
        if (!empty($subKategori['wajib_type'])) {
            $rules['type_id'] = 'required|integer';
        }
        if (!empty($subKategori['wajib_ruangan'])) {
            $rules['ruangan_id'] = 'required|integer';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $golonganId = $this->request->getPost('golongan_id');
        if (!empty($golonganId)) {
            $golongan = $this->golonganModel->find($golonganId);
            $kategori = $this->kategoriModel->find($kategoriId);
            if ($golongan && $kategori && $golongan['kelompok'] !== $kategori['jenis_aset']) {
                return redirect()->back()->withInput()->with('error', 'Golongan tidak sesuai dengan Kategori yang dipilih. Golongan "' . $golongan['nama_golongan'] . '" hanya berlaku untuk kategori jenis "' . ucfirst(str_replace('_', ' ', $kategori['jenis_aset'])) . '".');
            }
        }

        $allId = $this->assetNumberService->generateAllId($subKategoriId);
        $tahun = $this->request->getPost('tahun_perolehan') ?: (int) date('Y');
        $nomorAsetBaru = $this->assetNumberService->generateNomorAsetBaru($kategoriId, (int) $tahun);

        $saveData = [
            'all_id' => $allId,
            'nomor_aset_baru' => $nomorAsetBaru,
            'nomor_aset_lama' => $this->request->getPost('nomor_aset_lama') ?: null,
            'nama_aset' => $namaAset,
            'kategori_id' => $kategoriId,
            'sub_kategori_id' => $subKategoriId,
            'golongan_id' => $this->request->getPost('golongan_id') ?: null,
            'merk_id' => $this->request->getPost('merk_id') ?: null,
            'type_id' => $this->request->getPost('type_id') ?: null,
            'kondisi_id' => $this->request->getPost('kondisi_id'),
            'sumber_dana_id' => $this->request->getPost('sumber_dana_id') ?: null,
            'unit_kerja_id' => $this->request->getPost('unit_kerja_id'),
            'sub_unit_id' => $this->request->getPost('sub_unit_id') ?: null,
            'ruangan_id' => $this->request->getPost('ruangan_id') ?: null,
            'penanggung_jawab_id_kpe' => $this->request->getPost('penanggung_jawab_id_kpe') ?: null,
            'serial_number' => $this->request->getPost('serial_number') ?: null,
            'spesifikasi' => $this->request->getPost('spesifikasi') ?: null,
            'tahun_perolehan' => $tahun,
            'tanggal_perolehan' => $this->request->getPost('tanggal_perolehan') ?: null,
            'nilai_perolehan' => $this->request->getPost('nilai_perolehan') ?: null,
            'status_aset' => $this->request->getPost('status_aset') ?: 'draft',
            'is_active' => 1,
            'input_mode' => 'single',
            'batch_id' => null,
        ];

        $this->asetModel->save($saveData);

        return redirect()->to(base_url('/master/aset'))->with('success', 'Aset berhasil diregistrasi. ID: ' . $allId);
    }

    public function edit($id)
    {
        $record = $this->asetModel->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/aset'))->with('error', 'Data tidak ditemukan.');
        }

        $kategoriId = (int) ($record['kategori_id'] ?? 0);
        $merkId = (int) ($record['merk_id'] ?? 0);

        $data = [
            'title' => 'Edit Aset',
            'record' => $record,
            'kategori' => $this->kategoriModel->where('is_active', 1)->orderBy('nama_kategori', 'ASC')->findAll(),
            'subKategori' => $kategoriId ? $this->subKategoriModel->where('kategori_id', $kategoriId)->where('is_active', 1)->orderBy('nama_sub_kategori', 'ASC')->findAll() : [],
            'golongan' => $kategoriId ? $this->golonganModel->getByKategoriId($kategoriId) : [],
            'merk' => $this->merkModel->where('is_active', 1)->orderBy('nama_merk', 'ASC')->findAll(),
            'type' => $merkId ? $this->typeModel->where('merk_id', $merkId)->where('is_active', 1)->orderBy('nama_type', 'ASC')->findAll() : [],
            'kondisi' => $this->kondisiModel->where('is_active', 1)->orderBy('level_kondisi', 'ASC')->findAll(),
            'sumberDana' => $this->sumberDanaModel->where('is_active', 1)->orderBy('nama_sumber_dana', 'ASC')->findAll(),
            'unitKerja' => $this->unitKerjaModel->getActiveOptions(),
            'subUnit' => $record['unit_kerja_id'] ? $this->subUnitModel->where('unit_kerja_id', $record['unit_kerja_id'])->where('is_active', 1)->orderBy('nama_sub_unit', 'ASC')->findAll() : [],
            'ruangan' => $record['sub_unit_id'] ? $this->getRuanganBySubUnit($record['sub_unit_id']) : [],
            'selectedKategoriId' => $kategoriId,
            'selectedSubKategoriId' => (int) ($record['sub_kategori_id'] ?? 0),
        ];

        return view('master/aset/form', $data);
    }

    public function update($id)
    {
        $record = $this->asetModel->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/aset'))->with('error', 'Data tidak ditemukan.');
        }

        $subKategoriId = (int) $this->request->getPost('sub_kategori_id');
        $unitKerjaId = (int) $this->request->getPost('unit_kerja_id');

        $subKategori = $this->subKategoriModel->find($subKategoriId);
        if (!$subKategori) {
            return redirect()->back()->withInput()->with('error', 'Sub Kategori tidak ditemukan.');
        }

        $unitKerja = $this->unitKerjaModel->getById($unitKerjaId);
        if (!$unitKerja) {
            return redirect()->back()->withInput()->with('error', 'Unit Kerja tidak ditemukan atau tidak valid.');
        }

        $namaSubKategori = $subKategori['nama_sub_kategori'];
        $namaUnitKerja = $unitKerja['nama_unit'];
        $namaAset = $namaSubKategori . ' / ' . $namaUnitKerja;

        $rules = [
            'kategori_id' => 'required|integer',
            'sub_kategori_id' => 'required|integer',
            'unit_kerja_id' => 'required|integer',
            'kondisi_id' => 'required|integer',
            'sumber_dana_id' => 'required|integer',
        ];

        if (!empty($subKategori['wajib_merk'])) {
            $rules['merk_id'] = 'required|integer';
        }
        if (!empty($subKategori['wajib_type'])) {
            $rules['type_id'] = 'required|integer';
        }
        if (!empty($subKategori['wajib_ruangan'])) {
            $rules['ruangan_id'] = 'required|integer';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kategoriId = (int) $this->request->getPost('kategori_id');

        $golonganId = $this->request->getPost('golongan_id');
        if (!empty($golonganId)) {
            $golongan = $this->golonganModel->find($golonganId);
            $kategori = $this->kategoriModel->find($kategoriId);
            if ($golongan && $kategori && $golongan['kelompok'] !== $kategori['jenis_aset']) {
                return redirect()->back()->withInput()->with('error', 'Golongan tidak sesuai dengan Kategori yang dipilih. Golongan "' . $golongan['nama_golongan'] . '" hanya berlaku untuk kategori jenis "' . ucfirst(str_replace('_', ' ', $kategori['jenis_aset'])) . '".');
            }
        }

        $updateData = [
            'nomor_aset_lama' => $this->request->getPost('nomor_aset_lama') ?: null,
            'nama_aset' => $namaAset,
            'kategori_id' => $kategoriId,
            'sub_kategori_id' => $subKategoriId,
            'golongan_id' => $this->request->getPost('golongan_id') ?: null,
            'merk_id' => $this->request->getPost('merk_id') ?: null,
            'type_id' => $this->request->getPost('type_id') ?: null,
            'kondisi_id' => (int) $this->request->getPost('kondisi_id'),
            'sumber_dana_id' => $this->request->getPost('sumber_dana_id') ?: null,
            'unit_kerja_id' => (int) $this->request->getPost('unit_kerja_id'),
            'sub_unit_id' => $this->request->getPost('sub_unit_id') ?: null,
            'ruangan_id' => $this->request->getPost('ruangan_id') ?: null,
            'penanggung_jawab_id_kpe' => $this->request->getPost('penanggung_jawab_id_kpe') ?: null,
            'serial_number' => $this->request->getPost('serial_number') ?: null,
            'spesifikasi' => $this->request->getPost('spesifikasi') ?: null,
            'tahun_perolehan' => $this->request->getPost('tahun_perolehan') ?: null,
            'tanggal_perolehan' => $this->request->getPost('tanggal_perolehan') ?: null,
            'nilai_perolehan' => $this->request->getPost('nilai_perolehan') ?: null,
            'status_aset' => $this->request->getPost('status_aset') ?: 'draft',
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->asetModel->update($id, $updateData);

        return redirect()->to(base_url('/master/aset'))->with('success', 'Aset berhasil diperbarui.');
    }

    public function delete($id)
    {
        $record = $this->asetModel->withDeleted()->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/aset'))->with('error', 'Data tidak ditemukan.');
        }

        if ($record['is_active'] == 0 && $record['deleted_at'] !== null) {
            return redirect()->to(base_url('/master/aset'))->with('error', 'Data sudah tidak aktif.');
        }

        $this->asetModel->withDeleted()->update($id, [
            'is_active' => 0,
            'status_aset' => 'dihapus',
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('/master/aset'))->with('success', 'Aset berhasil dinonaktifkan.');
    }

    public function search()
    {
        $q = $this->request->getGet('q');
        $kategoriId = $this->request->getGet('kategori_id');

        if (!$q || strlen($q) < 4 || !$kategoriId) {
            return $this->response->setJSON([]);
        }

        $results = $this->asetModel
            ->select('all_id, nama_aset, nomor_aset_baru')
            ->like('nama_aset', $q, 'both')
            ->where('is_active', 1)
            ->where('kategori_id', (int) $kategoriId)
            ->orderBy('nama_aset', 'ASC')
            ->findAll(10);

        return $this->response->setJSON($results);
    }

    public function show($id)
    {
        $record = $this->asetModel->withDetails()
            ->where('aset_all_uir.all_id', $id)
            ->asArray()
            ->first();

        if (!$record) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan.'])->setStatusCode(404);
        }
        return $this->response->setJSON($record);
    }

    public function getSubKategoriByKategori($kategoriId)
    {
        $subKategori = $this->subKategoriModel
            ->where('kategori_id', $kategoriId)
            ->where('is_active', 1)
            ->orderBy('nama_sub_kategori', 'ASC')
            ->findAll();

        return $this->response->setJSON($subKategori);
    }

    public function getTypeByMerk($merkId)
    {
        $type = $this->typeModel
            ->where('merk_id', $merkId)
            ->where('is_active', 1)
            ->orderBy('nama_type', 'ASC')
            ->findAll();

        return $this->response->setJSON($type);
    }

    public function getSubUnitByUnitKerja($unitKerjaId)
    {
        $subUnit = $this->subUnitModel
            ->where('unit_kerja_id', $unitKerjaId)
            ->where('is_active', 1)
            ->orderBy('nama_sub_unit', 'ASC')
            ->findAll();

        return $this->response->setJSON($subUnit);
    }

    public function getRuanganBySubUnit($subUnitId)
    {
        $db = db_connect();
        $ruangan = $db->table('aset_ruangan r')
            ->select('r.rg_id, r.kode_ruangan, r.nama_ruangan, l.nama_lantai, g.nama_gedung')
            ->join('aset_lantai l', 'l.lt_id = r.lantai_id', 'left')
            ->join('aset_gedung g', 'g.gd_id = l.gedung_id', 'left')
            ->where('r.sub_unit_id', $subUnitId)
            ->where('r.is_active', 1)
            ->orderBy('r.nama_ruangan', 'ASC')
            ->get()
            ->getResultArray();

        return $ruangan;
    }

    public function getRuanganBySubUnitJson($subUnitId)
    {
        return $this->response->setJSON($this->getRuanganBySubUnit($subUnitId));
    }

    public function lookupSubKategori($id)
    {
        $sk = $this->subKategoriModel->find($id);
        if (!$sk) {
            return $this->response->setJSON(['wajib_merk' => 0, 'wajib_type' => 0, 'wajib_ruangan' => 0]);
        }
        return $this->response->setJSON($sk);
    }

    public function getGolonganByKategori($kategoriId)
    {
        $golongan = $this->golonganModel->getByKategoriId((int) $kategoriId);
        return $this->response->setJSON($golongan);
    }
}