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
use App\Services\AssetNumberService;
use CodeIgniter\Controller;

class BulkAsetController extends Controller
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
        $this->unitKerjaModel = new UnitKerjaAllowedModel();
        $this->assetNumberService = new AssetNumberService();
        helper(['form', 'url']);
    }

    public function new()
    {
        $kategoriId = (int) ($this->request->getGet('kategori_id') ?? 0);
        $subKategoriId = (int) ($this->request->getGet('sub_kategori_id') ?? 0);

        $data = [
            'title' => 'Registrasi Aset Massal (Bulk)',
            'kategori' => $this->kategoriModel->where('is_active', 1)->orderBy('nama_kategori', 'ASC')->findAll(),
            'subKategori' => $kategoriId ? $this->subKategoriModel->where('kategori_id', $kategoriId)->where('is_active', 1)->orderBy('nama_sub_kategori', 'ASC')->findAll() : [],
            'golongan' => $kategoriId ? $this->golonganModel->getByKategoriId($kategoriId) : [],
            'merk' => $this->merkModel->where('is_active', 1)->orderBy('nama_merk', 'ASC')->findAll(),
            'type' => [],
            'kondisi' => $this->kondisiModel->where('is_active', 1)->orderBy('level_kondisi', 'ASC')->findAll(),
            'sumberDana' => $this->sumberDanaModel->where('is_active', 1)->orderBy('nama_sumber_dana', 'ASC')->findAll(),
            'unitKerja' => $this->unitKerjaModel->getActiveOptions(),
            'subUnit' => [],
            'selectedKategoriId' => $kategoriId,
            'selectedSubKategoriId' => $subKategoriId,
        ];

        return view('master/aset/bulk_form', $data);
    }

    public function create()
    {
        $rules = [
            'kategori_id' => 'required|integer',
            'sub_kategori_id' => 'required|integer',
            'nama_aset' => 'required|max_length[200]',
            'jumlah' => 'required|integer|greater_than[0]|less_than[101]',
            'kondisi_id' => 'required|integer',
            'sumber_dana_id' => 'required|integer',
            'unit_kerja_id' => 'required|integer',
        ];

        $subKategoriId = (int) $this->request->getPost('sub_kategori_id');
        $subKategori = $this->subKategoriModel->find($subKategoriId);

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
        $jumlah = (int) $this->request->getPost('jumlah');
        $namaAset = trim($this->request->getPost('nama_aset'));

        $batchId = 'BULK-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $tahun = $this->request->getPost('tahun_perolehan') ?: (int) date('Y');
            $successCount = 0;

            for ($i = 0; $i < $jumlah; $i++) {
                $allId = $this->assetNumberService->generateAllId($subKategoriId);
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
                    'kondisi_id' => (int) $this->request->getPost('kondisi_id'),
                    'sumber_dana_id' => $this->request->getPost('sumber_dana_id') ?: null,
                    'unit_kerja_id' => (int) $this->request->getPost('unit_kerja_id'),
                    'sub_unit_id' => $this->request->getPost('sub_unit_id') ?: null,
                    'ruangan_id' => $this->request->getPost('ruangan_id') ?: null,
                    'penanggung_jawab_id_kpe' => $this->request->getPost('penanggung_jawab_id_kpe') ?: null,
                    'serial_number' => null,
                    'spesifikasi' => $this->request->getPost('spesifikasi') ?: null,
                    'tahun_perolehan' => $tahun,
                    'tanggal_perolehan' => $this->request->getPost('tanggal_perolehan') ?: null,
                    'nilai_perolehan' => $this->request->getPost('nilai_perolehan') ?: null,
                    'status_aset' => $this->request->getPost('status_aset') ?: 'draft',
                    'is_active' => 1,
                    'input_mode' => 'bulk',
                    'batch_id' => $batchId,
                ];

                if ($this->asetModel->save($saveData)) {
                    $successCount++;
                }
            }

            $db->transCommit();

            return redirect()->to(base_url('/master/aset?input_mode=bulk&batch_id=' . $batchId))
                ->with('success', "Berhasil meregistrasi {$successCount} aset secara massal. Batch ID: {$batchId}");
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal meregistrasi aset massal: ' . $e->getMessage());
        }
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

    public function getSubUnitByUnitKerja($unitKerjaId)
    {
        $subUnit = $this->subUnitModel
            ->where('unit_kerja_id', $unitKerjaId)
            ->where('is_active', 1)
            ->orderBy('nama_sub_unit', 'ASC')
            ->findAll();
        return $this->response->setJSON($subUnit);
    }

    public function getGolonganByKategori($kategoriId)
    {
        $golongan = $this->golonganModel->getByKategoriId((int) $kategoriId);
        return $this->response->setJSON($golongan);
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

    public function getRuanganBySubUnitJson($subUnitId)
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
        return $this->response->setJSON($ruangan);
    }
}