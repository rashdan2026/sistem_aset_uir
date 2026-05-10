<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Reference\UnitKerjaAllowedModel;
use App\Models\SubUnitModel;
use App\Models\GedungModel;
use App\Models\Master\RuanganModel;

class DashboardController extends Controller
{
    public function index()
    {
        $unitKerjaModel = new UnitKerjaAllowedModel();
        $subUnitModel = new SubUnitModel();
        $gedungModel = new GedungModel();
        $ruanganModel = new RuanganModel();

        $totalUnitKerja = $unitKerjaModel->where('flag_aktif', 1)->countAllResults();
        $totalSubUnit = $subUnitModel->where('is_active', 1)->countAllResults();
        $totalGedung = $gedungModel->where('is_active', 1)->countAllResults();
        $totalRuangan = $ruanganModel->where('is_active', 1)->countAllResults();

        $latestRuangan = $ruanganModel->select('aset_ruangan.*,
                aset_lantai.nama_lantai,
                aset_gedung.nama_gedung,
                ylpi_karyawan.nama_gelar as pj_nama')
            ->join('aset_lantai', 'aset_lantai.lt_id = aset_ruangan.lantai_id', 'left')
            ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
            ->join('aset_sub_units', 'aset_sub_units.su_id = aset_ruangan.sub_unit_id', 'left')
            ->join('ylpi_karyawan', 'ylpi_karyawan.id_kpe = aset_ruangan.penanggung_jawab_id_kpe', 'left')
            ->orderBy('aset_ruangan.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title' => 'Dashboard',
            'totalUnitKerja' => $totalUnitKerja,
            'totalSubUnit' => $totalSubUnit,
            'totalGedung' => $totalGedung,
            'totalRuangan' => $totalRuangan,
            'latestRuangan' => $latestRuangan,
        ];
        
        return view('dashboard/index', $data);
    }
}