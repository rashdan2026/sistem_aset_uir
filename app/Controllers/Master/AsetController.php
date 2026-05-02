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
use App\Models\Reference\UnitKerjaReadOnlyModel;
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
        $this->subUnitModel = new SubUnitModel();
        $this->unitKerjaModel = new UnitKerjaReadOnlyModel();
        $this->assetNumberService = new AssetNumberService();
        helper(['form', 'url']);
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $status = $this->request->getGet('status');
        $kategori = $this->request->getGet('kategori_id');

        $builder = db_connect()->table('aset_all_uir a');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('a.nama_aset', $search)
                ->orLike('a.all_id', $search)
                ->orLike('a.nomor_aset_baru', $search)
                ->orLike('a.nomor_aset_lama', $search)
                ->groupEnd();
        }

        if (!empty($status)) {
            $builder->where('a.status_aset', $status);
        }

        if (!empty($kategori)) {
            $builder->where('a.kategori_id', $kategori);
        }

        $total = $builder->countAllResults(false);
        $perPage = 20;
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $currentPage = ($currentPage < 1) ? 1 : $currentPage;
        $offset = ($currentPage - 1) * $perPage;
        $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

        $records = $builder
            ->select('a.*, ak.nama_kategori, ask.nama_sub_kategori, akb.nama_kondisi')
            ->join('aset_kategori ak', 'ak.kt_id = a.kategori_id', 'left')
            ->join('aset_sub_kategori ask', 'ask.sk_id = a.sub_kategori_id', 'left')
            ->join('aset_kondisi_barang akb', 'akb.kd_id = a.kondisi_id', 'left')
            ->limit($perPage, $offset)
            ->orderBy('a.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Registrasi Aset',
            'records' => $records,
            'total' => $total,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'search' => $search,
            'status' => $status,
            'kategoriFilter' => $kategori,
            'kategoriOptions' => $this->kategoriModel->where('is_active', 1)->orderBy('nama_kategori', 'ASC')->findAll(),
        ];

        return view('master/aset/index', $data);
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

        $subKategori = $this->subKategoriModel->find($subKategoriId);
        $rules = [
            'kategori_id' => 'required|integer',
            'sub_kategori_id' => 'required|integer',
            'nama_aset' => 'required|max_length[200]',
            'unit_kerja_id' => 'required|integer',
            'kondisi_id' => 'required|integer',
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
            'nama_aset' => $this->request->getPost('nama_aset'),
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
        $subKategori = $this->subKategoriModel->find($subKategoriId);

        $rules = [
            'kategori_id' => 'required|integer',
            'sub_kategori_id' => 'required|integer',
            'nama_aset' => 'required|max_length[200]',
            'unit_kerja_id' => 'required|integer',
            'kondisi_id' => 'required|integer',
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
        $kategoriId = (int) $this->request->getPost('kategori_id');
        if (!empty($golonganId)) {
            $golongan = $this->golonganModel->find($golonganId);
            $kategori = $this->kategoriModel->find($kategoriId);
            if ($golongan && $kategori && $golongan['kelompok'] !== $kategori['jenis_aset']) {
                return redirect()->back()->withInput()->with('error', 'Golongan tidak sesuai dengan Kategori yang dipilih. Golongan "' . $golongan['nama_golongan'] . '" hanya berlaku untuk kategori jenis "' . ucfirst(str_replace('_', ' ', $kategori['jenis_aset'])) . '".');
            }
        }

        $updateData = [
            'nomor_aset_lama' => $this->request->getPost('nomor_aset_lama') ?: null,
            'nama_aset' => $this->request->getPost('nama_aset'),
            'kategori_id' => (int) $this->request->getPost('kategori_id'),
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
        $record = $this->asetModel->find($id);
        if (!$record) {
            return redirect()->to(base_url('/master/aset'))->with('error', 'Data tidak ditemukan.');
        }

        $this->asetModel->update($id, ['is_active' => 0, 'status_aset' => 'dihapus']);

        return redirect()->to(base_url('/master/aset'))->with('success', 'Aset berhasil dinonaktifkan.');
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