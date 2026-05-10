<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\Master\RuanganModel;
use App\Models\Reference\PenanggungJawabReadOnlyModel;
use App\Models\Reference\UnitKerjaAllowedModel;
use App\Models\GedungModel;
use App\Models\SubUnitModel;

class SearchController extends ResourceController
{
    protected $ruanganModel;
    protected $pjModel;
    protected $unitKerjaModel;
    protected $gedungModel;
    protected $subUnitModel;

    public function __construct()
    {
        $this->ruanganModel = new RuanganModel();
        $this->pjModel = new PenanggungJawabReadOnlyModel();
        $this->unitKerjaModel = new UnitKerjaAllowedModel();
        $this->gedungModel = new GedungModel();
        $this->subUnitModel = new SubUnitModel();
        helper(['request']);
    }

    public function searchRuangan()
    {
        $q = $this->request->getGet('q');
        $limit = min((int)($this->request->getGet('limit') ?? 20), 50);

        if (!$q || strlen($q) < 3) {
            return $this->respond([]);
        }

        $ruangan = $this->ruanganModel
            ->select('aset_ruangan.rg_id, aset_ruangan.nama_ruangan, aset_ruangan.kode_ruangan, aset_gedung.nama_gedung')
            ->join('aset_lantai', 'aset_lantai.lt_id = aset_ruangan.lantai_id', 'left')
            ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
            ->where('aset_ruangan.is_active', 1)
            ->groupStart()
                ->like('aset_ruangan.nama_ruangan', $q, 'both')
                ->orLike('aset_ruangan.kode_ruangan', $q, 'both')
            ->groupEnd()
            ->orderBy('aset_ruangan.nama_ruangan', 'ASC')
            ->limit($limit)
            ->findAll();

        $results = array_map(function($r) {
            return [
                'id' => $r['rg_id'],
                'text' => $r['nama_ruangan'] . ' (' . $r['kode_ruangan'] . ') - ' . ($r['nama_gedung'] ?? '')
            ];
        }, $ruangan);

        return $this->respond($results);
    }

    public function searchPenanggungJawab()
    {
        $q = $this->request->getGet('q');
        $limit = min((int)($this->request->getGet('limit') ?? 20), 50);

        if (!$q || strlen($q) < 3) {
            return $this->respond([]);
        }

        $pj = $this->pjModel
            ->searchActive($q, $limit);

        $results = array_map(function($r) {
            return [
                'id' => $r['id_kpe'],
                'text' => $r['nama_gelar'] . ' (' . $r['npk'] . ')'
            ];
        }, $pj);

        return $this->respond($results);
    }

    public function searchUnitKerja()
    {
        $q = $this->request->getGet('q');
        $limit = min((int)($this->request->getGet('limit') ?? 20), 50);

        if (!$q || strlen($q) < 3) {
            return $this->respond([]);
        }

        $units = $this->unitKerjaModel
            ->search($q, $limit);

        $results = array_map(function($r) {
            return [
                'id' => $r['id_unit_kerja'],
                'text' => $r['nama_unit'] . ' (' . $r['id_unit_kerja'] . ')'
            ];
        }, $units);

        return $this->respond($results);
    }

    public function searchGedung()
    {
        $q = $this->request->getGet('q');
        $limit = min((int)($this->request->getGet('limit') ?? 20), 50);

        $gedungQuery = $this->gedungModel
            ->select('gd_id, kode_gedung, nama_gedung')
            ->where('is_active', 1);

        if ($q && strlen($q) >= 3) {
            $gedungQuery->groupStart()
                ->like('nama_gedung', $q, 'both')
                ->orLike('kode_gedung', $q, 'both')
            ->groupEnd();
        }

        $gedung = $gedungQuery
            ->orderBy('nama_gedung', 'ASC')
            ->limit($limit)
            ->findAll();

        $results = array_map(function($r) {
            return [
                'id' => $r['gd_id'],
                'text' => $r['nama_gedung'] . ' (' . $r['kode_gedung'] . ')'
            ];
        }, $gedung);

        return $this->respond($results);
    }

    public function searchSubUnit()
    {
        $q = $this->request->getGet('q');
        $limit = min((int)($this->request->getGet('limit') ?? 20), 50);

        $subUnitQuery = $this->subUnitModel
            ->select('su_id, kode_sub_unit, nama_sub_unit')
            ->where('is_active', 1);

        if ($q && strlen($q) >= 3) {
            $subUnitQuery->groupStart()
                ->like('nama_sub_unit', $q, 'both')
                ->orLike('kode_sub_unit', $q, 'both')
            ->groupEnd();
        }

        $subUnit = $subUnitQuery
            ->orderBy('nama_sub_unit', 'ASC')
            ->limit($limit)
            ->findAll();

        $results = array_map(function($r) {
            return [
                'id' => $r['su_id'],
                'text' => $r['nama_sub_unit'] . ' (' . $r['kode_sub_unit'] . ')'
            ];
        }, $subUnit);

        return $this->respond($results);
    }
}