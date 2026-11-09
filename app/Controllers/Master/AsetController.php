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
use App\Libraries\UnitScope;
use CodeIgniter\Controller;
use CodeIgniter\Database\Exceptions\DatabaseException;

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
    protected $unitScope;

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
        $this->unitScope = new UnitScope();
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
            'tbl_unit_kerja tu' => 'tu.id_unit_kerja = a.unit_kerja_id',
            'aset_sub_units asu' => 'asu.su_id = a.sub_unit_id',
        ];
        $selectFields[] = 'tu.nama_unit';
        $selectFields[] = 'asu.nama_sub_unit';

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
            $selectFields[] = 'akb.warna as kondisi_warna';
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

        $this->unitScope->applyToBuilder($builder, 'a.unit_kerja_id');

        foreach ($joinTables as $table => $on) {
            $builder->join($table, $on, 'left');
        }

        $total = $builder->countAllResults(false);
        $perPage = 50;
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $currentPage = ($currentPage < 1) ? 1 : $currentPage;
        $offset = ($currentPage - 1) * $perPage;

        $viewMode = $this->request->getGet('view') ?? 'detail';
        if (!in_array($viewMode, ['detail', 'grouped'])) {
            $viewMode = 'detail';
        }

        if ($viewMode === 'grouped') {
            $records = $this->getGroupedRecords($builder, $perPage, $currentPage);
            $total = $this->getGroupedTotal($builder);
            $totalPages = $total > 0 ? ceil($total / $perPage) : 1;
        } else {
            if ($sortCol && isset($columnMap[$sortCol])) {
                $builder->orderBy($columnMap[$sortCol], $sortDir);
            } else {
                $builder->orderBy('tu.nama_unit', 'ASC')
                    ->orderBy('asu.nama_sub_unit', 'ASC')
                    ->orderBy('ak.nama_kategori', 'ASC')
                    ->orderBy('a.nomor_aset_baru', 'ASC');
            }

            $records = $builder
                ->select(implode(', ', $selectFields))
                ->limit($perPage, $offset)
                ->get()
                ->getResultArray();
            $totalPages = $total > 0 ? ceil($total / $perPage) : 1;
        }

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
            'perPage' => $perPage,
            'search' => $search,
            'searchCol' => $searchCol,
            'status' => $status,
            'inputMode' => $inputMode,
            'kategoriFilter' => $kategori,
            'kategoriOptions' => $this->kategoriModel->where('is_active', 1)->where('deleted_at', null)->orderBy('nama_kategori', 'ASC')->findAll(),
            'visibleColumns' => $visibleColumns,
            'allColumns' => $allColumns,
            'searchableColumns' => $searchableColumns,
            'sortCol' => $sortCol,
            'sortDir' => $sortDir,
            'viewMode' => $viewMode,
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
        $golonganId = (int) $this->request->getGet('golongan_id');
        $kategoriId = (int) $this->request->getGet('kategori_id');
        $subKategoriId = (int) $this->request->getGet('sub_kategori_id');

        $golonganOptions = $this->golonganModel->where('is_active', 1)->orderBy('nama_golongan', 'ASC')->findAll();

        $kategoriOptions = [];
        if ($golonganId) {
            $kategoriOptions = $this->kategoriModel->getByGolonganId($golonganId);
        }

        $subKategoriOptions = [];
        if ($kategoriId) {
            $subKategoriOptions = $this->subKategoriModel->where('kategori_id', $kategoriId)->where('is_active', 1)->orderBy('nama_sub_kategori', 'ASC')->findAll();
        }

        $data = [
            'title' => 'Registrasi Aset Baru',
            'record' => null,
            'golonganOptions' => $golonganOptions,
            'kategoriOptions' => $kategoriOptions,
            'subKategoriOptions' => $subKategoriOptions,
            'merk' => $this->merkModel->where('is_active', 1)->orderBy('nama_merk', 'ASC')->findAll(),
            'type' => [],
            'kondisi' => $this->kondisiModel->where('is_active', 1)->orderBy('level_kondisi', 'ASC')->findAll(),
            'sumberDana' => $this->sumberDanaModel->where('is_active', 1)->orderBy('nama_sumber_dana', 'ASC')->findAll(),
            'unitKerja' => $this->unitKerjaModel->getActiveOptions(),
            'subUnit' => [],
            'ruangan' => [],
            'selectedGolonganId' => $golonganId,
            'selectedKategoriId' => $kategoriId,
            'selectedSubKategoriId' => $subKategoriId,
        ];

        return view('master/aset/form', $data);
    }

    public function create()
    {
        $golonganId = (int) $this->request->getPost('golongan_id');
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

        if (!$this->unitScope->canAccessUnit($unitKerjaId)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki akses ke Unit Kerja tersebut.');
        }

        $ownershipError = $this->validateUnitOwnership($unitKerjaId, $this->request->getPost('sub_unit_id') ?: null, $this->request->getPost('ruangan_id') ?: null);
        if ($ownershipError !== null) {
            return redirect()->back()->withInput()->with('error', $ownershipError);
        }

        $namaSubKategori = $subKategori['nama_sub_kategori'];
        $namaUnitKerja = $unitKerja['nama_unit'];
        $namaAset = $namaSubKategori . ' / ' . $namaUnitKerja;

        $rules = [
            'golongan_id' => 'required|integer',
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

        $golongan = $this->golonganModel->find($golonganId);
        if (!$golongan) {
            return redirect()->back()->withInput()->with('error', 'Golongan tidak ditemukan. Golongan harus diinput terlebih dahulu.');
        }

        $kategori = $this->kategoriModel->find($kategoriId);
        if ($kategori && (int) $kategori['gl_id'] !== $golonganId) {
            return redirect()->back()->withInput()->with('error', 'Kategori "' . $kategori['nama_kategori'] . '" tidak termasuk dalam Golongan "' . $golongan['nama_golongan'] . '". Pilih kategori yang sesuai dengan golongan.');
        }

        $allId = $this->assetNumberService->generateAllId($subKategoriId);
        $tahun = $this->request->getPost('tahun_perolehan') ?: (int) date('Y');

        $saveData = [
            'all_id' => $allId,
            'nomor_aset_lama' => $this->request->getPost('nomor_aset_lama') ?: null,
            'nama_aset' => $namaAset,
            'golongan_id' => $golonganId,
            'kategori_id' => $kategoriId,
            'sub_kategori_id' => $subKategoriId,
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

        $maxRetries = 5;
        $attempt = 0;
        $success = false;

        while ($attempt < $maxRetries) {
            try {
                $nomorAsetBaru = $this->assetNumberService->generateNomorAsetBaru($kategoriId, (int) $tahun, $attempt);
                $saveData['nomor_aset_baru'] = $nomorAsetBaru;

                $this->asetModel->save($saveData);
                $success = true;
                break;
            } catch (DatabaseException $e) {
                if (!$this->isDuplicateKeyError($e) || $attempt >= $maxRetries - 1) {
                    throw $e;
                }
                $attempt++;
                usleep(50000 * (1 << ($attempt - 1)));
            }
        }

        if (!$success) {
            return redirect()->back()->withInput()->with('error', 'Gagal generate ID Aset Baru yang unik setelah ' . $maxRetries . ' percobaan. Silakan coba lagi.');
        }

        return redirect()->to(base_url('/master/aset'))->with('success', 'Aset berhasil diregistrasi. ID: ' . $allId);
    }

    public function edit($id)
    {
        $record = $this->asetModel->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/aset'))->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->unitScope->canAccessUnit($record['unit_kerja_id'])) {
            return redirect()->to(base_url('/master/aset'))->with('error', 'Anda tidak memiliki akses ke aset pada Unit Kerja tersebut.');
        }

        $golonganId = (int) ($record['golongan_id'] ?? 0);
        $kategoriId = (int) ($record['kategori_id'] ?? 0);
        $merkId = (int) ($record['merk_id'] ?? 0);

        $golonganOptions = $this->golonganModel->where('is_active', 1)->orderBy('nama_golongan', 'ASC')->findAll();

        $kategoriOptions = [];
        if ($golonganId) {
            $kategoriOptions = $this->kategoriModel->getByGolonganId($golonganId);
        }
        if (empty($kategoriOptions)) {
            $kategoriOptions = $this->kategoriModel->where('is_active', 1)->where('deleted_at', null)->orderBy('nama_kategori', 'ASC')->findAll();
        }

        $subKategoriOptions = [];
        if ($kategoriId) {
            $subKategoriOptions = $this->subKategoriModel->where('kategori_id', $kategoriId)->where('is_active', 1)->orderBy('nama_sub_kategori', 'ASC')->findAll();
        }

        $data = [
            'title' => 'Edit Aset',
            'record' => $record,
            'golonganOptions' => $golonganOptions,
            'kategoriOptions' => $kategoriOptions,
            'subKategoriOptions' => $subKategoriOptions,
            'merk' => $this->merkModel->where('is_active', 1)->orderBy('nama_merk', 'ASC')->findAll(),
            'type' => $merkId ? $this->typeModel->where('merk_id', $merkId)->where('is_active', 1)->orderBy('nama_type', 'ASC')->findAll() : [],
            'kondisi' => $this->kondisiModel->where('is_active', 1)->orderBy('level_kondisi', 'ASC')->findAll(),
            'sumberDana' => $this->sumberDanaModel->where('is_active', 1)->orderBy('nama_sumber_dana', 'ASC')->findAll(),
            'unitKerja' => $this->unitKerjaModel->getActiveOptions(),
            'subUnit' => $record['unit_kerja_id'] ? $this->subUnitModel->where('unit_kerja_id', $record['unit_kerja_id'])->where('is_active', 1)->orderBy('nama_sub_unit', 'ASC')->findAll() : [],
            'ruangan' => $record['sub_unit_id'] ? $this->getRuanganBySubUnit($record['sub_unit_id']) : [],
            'selectedGolonganId' => $golonganId,
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

        if (!$this->unitScope->canAccessUnit($record['unit_kerja_id'])) {
            return redirect()->to(base_url('/master/aset'))->with('error', 'Anda tidak memiliki akses ke aset pada Unit Kerja tersebut.');
        }

        $golonganId = (int) $this->request->getPost('golongan_id');
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

        if (!$this->unitScope->canAccessUnit($unitKerjaId)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki akses ke Unit Kerja tersebut.');
        }

        $ownershipError = $this->validateUnitOwnership($unitKerjaId, $this->request->getPost('sub_unit_id') ?: null, $this->request->getPost('ruangan_id') ?: null);
        if ($ownershipError !== null) {
            return redirect()->back()->withInput()->with('error', $ownershipError);
        }

        $namaSubKategori = $subKategori['nama_sub_kategori'];
        $namaUnitKerja = $unitKerja['nama_unit'];
        $namaAset = $namaSubKategori . ' / ' . $namaUnitKerja;

        $rules = [
            'golongan_id' => 'required|integer',
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

        $golongan = $this->golonganModel->find($golonganId);
        if (!$golongan) {
            return redirect()->back()->withInput()->with('error', 'Golongan tidak ditemukan. Golongan harus diinput terlebih dahulu.');
        }

        $kategori = $this->kategoriModel->find($kategoriId);
        if ($kategori && (int) $kategori['gl_id'] !== $golonganId) {
            return redirect()->back()->withInput()->with('error', 'Kategori "' . $kategori['nama_kategori'] . '" tidak termasuk dalam Golongan "' . $golongan['nama_golongan'] . '". Pilih kategori yang sesuai dengan golongan.');
        }

        $updateData = [
            'nomor_aset_lama' => $this->request->getPost('nomor_aset_lama') ?: null,
            'nama_aset' => $namaAset,
            'golongan_id' => $golonganId,
            'kategori_id' => $kategoriId,
            'sub_kategori_id' => $subKategoriId,
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

        if (!$this->unitScope->canAccessUnit($record['unit_kerja_id'])) {
            return redirect()->to(base_url('/master/aset'))->with('error', 'Anda tidak memiliki akses ke aset pada Unit Kerja tersebut.');
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

        $query = $this->asetModel
            ->select('all_id, nama_aset, nomor_aset_baru')
            ->like('nama_aset', $q, 'both')
            ->where('is_active', 1)
            ->where('kategori_id', (int) $kategoriId);

        $this->unitScope->applyToBuilder($query, 'unit_kerja_id');

        $results = $query
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

        if (!$this->unitScope->canAccessUnit($record['unit_kerja_id'])) {
            return $this->response->setJSON(['error' => 'Anda tidak memiliki akses ke aset ini.'])->setStatusCode(403);
        }

        unset($record['nomor_aset_lama']);

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

    public function getKategoriByGolongan($golonganId)
    {
        if (empty($golonganId)) {
            return $this->response->setJSON([]);
        }

        $kategori = $this->kategoriModel->getByGolonganId((int) $golonganId);
        return $this->response->setJSON($kategori);
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
        if (!$this->unitScope->canAccessUnit($unitKerjaId)) {
            return $this->response->setJSON([]);
        }

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
        if (!$this->canAccessSubUnit($subUnitId)) {
            return $this->response->setJSON([]);
        }

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

    public function getBatchItems($batchId)
    {
        $query = $this->asetModel
            ->select('aset_all_uir.all_id, aset_all_uir.nomor_aset_baru, aset_all_uir.status_aset, aset_all_uir.kondisi_id, aset_all_uir.tahun_perolehan, aset_all_uir.deleted_at, aset_all_uir.created_at, ak.nama_kategori, ask.nama_sub_kategori, akb.nama_kondisi, akb.warna as kondisi_warna, tu.nama_unit')
            ->join('aset_kategori ak', 'ak.kt_id = aset_all_uir.kategori_id', 'left')
            ->join('aset_sub_kategori ask', 'ask.sk_id = aset_all_uir.sub_kategori_id', 'left')
            ->join('aset_kondisi_barang akb', 'akb.kd_id = aset_all_uir.kondisi_id', 'left')
            ->join('tbl_unit_kerja tu', 'tu.id_unit_kerja = aset_all_uir.unit_kerja_id', 'left')
            ->where('aset_all_uir.batch_id', $batchId)
            ->where('aset_all_uir.input_mode', 'bulk');

        $this->unitScope->applyToBuilder($query, 'aset_all_uir.unit_kerja_id');

        $items = $query
            ->orderBy('aset_all_uir.nomor_aset_baru', 'ASC')
            ->findAll();

        $kondisiOptions = $this->kondisiModel
            ->where('is_active', 1)
            ->orderBy('level_kondisi', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'batch_id' => $batchId,
            'items' => $items,
            'total' => count($items),
            'kondisi_options' => $kondisiOptions,
        ]);
    }

    public function bulkUpdateBatch()
    {
        $allIds = $this->request->getPost('all_ids');
        $batchId = $this->request->getPost('batch_id');
        $statusAset = $this->request->getPost('status_aset');
        $kondisiId = $this->request->getPost('kondisi_id');

        if (empty($batchId) || empty($allIds) || !is_array($allIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Batch ID atau item kosong.',
            ])->setStatusCode(400);
        }

        $updateData = [];
        if (!empty($statusAset) && in_array($statusAset, ['draft', 'aktif', 'nonaktif', 'hilang', 'dihapus'])) {
            $updateData['status_aset'] = $statusAset;
        }
        if (!empty($kondisiId) && is_numeric($kondisiId)) {
            $updateData['kondisi_id'] = (int) $kondisiId;
        }

        if (empty($updateData)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada field yang diupdate. Pilih Status atau Kondisi.',
            ])->setStatusCode(400);
        }

        $updateData['updated_at'] = date('Y-m-d H:i:s');

        $db = db_connect();
        $builder = $db->table('aset_all_uir')
            ->where('input_mode', 'bulk')
            ->where('batch_id', $batchId)
            ->whereIn('all_id', $allIds);

        $this->unitScope->applyToBuilder($builder, 'unit_kerja_id');

        $count = count($allIds);
        $builder->update($updateData);

        return $this->response->setJSON([
            'success' => true,
            'updated' => $count,
            'message' => 'Berhasil update ' . $count . ' item.',
        ]);
    }

    private function getGroupedRecords($builder, int $perPage, int $currentPage): array
    {
        $offset = ($currentPage - 1) * $perPage;

        $rows = $builder
            ->select('
                a.batch_id,
                a.input_mode,
                a.nama_aset,
                a.kategori_id,
                a.sub_kategori_id,
                a.unit_kerja_id,
                MIN(a.created_at) AS tanggal_pertama,
                MAX(a.updated_at) AS tanggal_terakhir,
                COUNT(*) AS jumlah,
                MIN(a.nomor_aset_baru) AS nomor_awal,
                MAX(a.nomor_aset_baru) AS nomor_akhir,
                MIN(a.all_id) AS sample_all_id,
                ak.nama_kategori,
                ask.nama_sub_kategori,
                tu.nama_unit,
                SUM(CASE WHEN a.status_aset = "aktif" AND a.deleted_at IS NULL THEN 1 ELSE 0 END) AS count_aktif,
                SUM(CASE WHEN a.status_aset != "aktif" OR a.deleted_at IS NOT NULL THEN 1 ELSE 0 END) AS count_nonaktif
            ')
            ->where('a.input_mode', 'bulk')
            ->where('a.batch_id IS NOT NULL')
            ->groupBy('a.batch_id, a.input_mode, a.nama_aset, a.kategori_id, a.sub_kategori_id, a.unit_kerja_id, ak.nama_kategori, ask.nama_sub_kategori, tu.nama_unit')
            ->orderBy('jumlah', 'DESC')
            ->orderBy('tanggal_pertama', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return $rows;
    }

    private function getGroupedTotal($builder): int
    {
        $row = $builder
            ->select('COUNT(DISTINCT a.batch_id) AS total')
            ->where('a.input_mode', 'bulk')
            ->where('a.batch_id IS NOT NULL')
            ->get()
            ->getRowArray();

        return (int) ($row['total'] ?? 0);
    }

    private function isDuplicateKeyError(\Throwable $e): bool
    {
        $msg = $e->getMessage();
        return strpos($msg, 'Duplicate entry') !== false
            || strpos($msg, '1062') !== false
            || strpos($msg, 'UNIQUE constraint') !== false;
    }

    /**
     * Validasi bahwa sub_unit dan ruangan benar-benar berada di bawah
     * unit kerja yang dipilih (mencegah manipulasi parameter form).
     */
    private function validateUnitOwnership($unitKerjaId, $subUnitId, $ruanganId): ?string
    {
        $subUnitId = (int) $subUnitId;
        $ruanganId = (int) $ruanganId;

        if ($subUnitId > 0) {
            $subUnit = $this->subUnitModel->find($subUnitId);
            if (!$subUnit || (int) $subUnit['unit_kerja_id'] !== (int) $unitKerjaId) {
                return 'Sub Unit tidak valid untuk Unit Kerja yang dipilih.';
            }
        }

        if ($ruanganId > 0) {
            $ruangan = $this->ruanganModel->find($ruanganId);
            if (!$ruangan) {
                return 'Ruangan tidak ditemukan.';
            }
            if (!empty($ruangan['sub_unit_id'])) {
                if ($subUnitId > 0 && (int) $ruangan['sub_unit_id'] !== $subUnitId) {
                    return 'Ruangan tidak valid untuk Sub Unit yang dipilih.';
                }
                $ruanganUnit = $this->subUnitModel->find((int) $ruangan['sub_unit_id']);
                if ($ruanganUnit && (int) $ruanganUnit['unit_kerja_id'] !== (int) $unitKerjaId) {
                    return 'Ruangan tidak valid untuk Unit Kerja yang dipilih.';
                }
            }
        }

        return null;
    }

    /**
     * Cek scope unit untuk sebuah sub unit (dipakai endpoint JSON ruangan).
     */
    private function canAccessSubUnit($subUnitId): bool
    {
        $subUnit = $this->subUnitModel->find((int) $subUnitId);
        if (!$subUnit) {
            return false;
        }

        return $this->unitScope->canAccessUnit($subUnit['unit_kerja_id']);
    }

    /**
     * Print preview for QR asset labels.
     * Accepts 1..20 asset all_id values via POST.
     * Renders a stand-alone print-friendly view of 70x50mm labels.
     */
    public function printQrLabels()
    {
        helper(['qr']);

        $raw = $this->request->getPost('all_ids');
        $allIds = [];

        if (is_array($raw)) {
            $allIds = array_values(array_filter(array_map(function ($v) {
                return is_string($v) ? trim($v) : '';
            }, $raw), function ($v) {
                return $v !== '';
            }));
        } elseif (is_string($raw) && $raw !== '') {
            $allIds = [$raw];
        }

        if (count($allIds) === 0) {
            return redirect()->to(base_url('/master/aset'))
                ->with('error', 'Pilih minimal 1 aset untuk dicetak label QR-nya.');
        }

        if (count($allIds) > 20) {
            return redirect()->to(base_url('/master/aset'))
                ->with('error', 'Maksimal 20 aset per pencetakan. Anda memilih ' . count($allIds) . ' aset.');
        }

        foreach ($allIds as $id) {
            if (!preg_match('/^all_[0-9]{3}-[a-z0-9]{1,15}$/', $id)) {
                return redirect()->to(base_url('/master/aset'))
                    ->with('error', 'Terdapat ID aset yang tidak valid pada pilihan Anda.');
            }
        }

        $query = $this->asetModel
            ->withDetails()
            ->whereIn('aset_all_uir.all_id', $allIds)
            ->where('aset_all_uir.deleted_at', null);

        $this->unitScope->applyToBuilder($query, 'aset_all_uir.unit_kerja_id');

        $records = $query
            ->asArray()
            ->orderBy('aset_all_uir.nama_aset', 'ASC')
            ->findAll();

        if (count($records) === 0) {
            return redirect()->to(base_url('/master/aset'))
                ->with('error', 'Tidak ada aset valid yang dapat dicetak dari pilihan Anda.');
        }

        $labels = array_map(function (array $r) {
            $publicUrl = qr_public_url($r['all_id']);

            $unitParts = array_filter([
                $r['nama_unit']     ?? null,
                $r['nama_sub_unit'] ?? null,
            ], function ($v) { return is_string($v) ? trim($v) !== '' : !empty($v); });
            $unitKerjaText = strtoupper(implode(' / ', $unitParts));

            $lokasiParts = array_filter([
                $r['nama_gedung']  ?? null,
                $r['nama_lantai']  ?? null,
                $r['nama_ruangan'] ?? null,
            ], function ($v) { return is_string($v) ? trim($v) !== '' : !empty($v); });
            $lokasiText = strtoupper(implode(' / ', $lokasiParts));
            if ($lokasiText === '') {
                $lokasiText = '-';
            }

            $asetParts = array_filter([
                $r['nama_sub_kategori'] ?? null,
                $r['nama_merk']         ?? null,
                $r['nama_type']         ?? null,
            ], function ($v) { return is_string($v) ? trim($v) !== '' : !empty($v); });
            $asetText = strtoupper(implode(' | ', $asetParts));
            if ($asetText === '') {
                $asetText = strtoupper((string) ($r['nama_aset'] ?? '-'));
            }

            return [
                'record'        => $r,
                'publicUrl'     => $publicUrl,
                'qrSrc'         => qr_image_src($publicUrl, 320),
                'unitKerjaText' => $unitKerjaText !== '' ? $unitKerjaText : '-',
                'lokasiText'    => $lokasiText,
                'asetText'      => $asetText,
            ];
        }, $records);

        return view('master/aset/qr_label_print', [
            'title'   => 'Cetak Label QR — ' . count($labels) . ' Aset',
            'labels'  => $labels,
        ]);
    }
}