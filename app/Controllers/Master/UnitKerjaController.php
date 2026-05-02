<?php

namespace App\Controllers\Master;

use App\Models\Reference\UnitKerjaReadOnlyModel;
use CodeIgniter\Controller;

class UnitKerjaController extends Controller
{
    protected $unitKerjaModel;

    public function __construct()
    {
        $this->unitKerjaModel = new UnitKerjaReadOnlyModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $search = $this->request->getGet('q');

        $builder = db_connect()->table('tbl_unit_kerja')->where('flag_aktif', 1);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama_unit', $search)
                ->orLike('id_unit_kerja', $search)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $perPage = 20;
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $currentPage = ($currentPage < 1) ? 1 : $currentPage;
        $offset = ($currentPage - 1) * $perPage;

        $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

        $data = [
            'title' => 'Referensi Unit Kerja',
            'unitKerja' => $builder->limit($perPage, $offset)->orderBy('nama_unit', 'ASC')->get()->getResultArray(),
            'total' => $total,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'search' => $search,
            'perPage' => $perPage,
        ];

        return view('master/unit_kerja/index', $data);
    }

    public function show($id)
    {
        $unit = $this->unitKerjaModel->getById($id);
        if (!$unit) {
            return redirect()->back()->with('error', 'Data unit kerja tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Unit Kerja',
            'unitKerja' => $unit
        ];

        return view('master/unit_kerja/show', $data);
    }
}
