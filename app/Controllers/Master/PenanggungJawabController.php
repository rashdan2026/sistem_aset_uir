<?php

namespace App\Controllers\Master;

use App\Models\Reference\PenanggungJawabReadOnlyModel;
use CodeIgniter\Controller;

class PenanggungJawabController extends Controller
{
    protected $pjModel;

    public function __construct()
    {
        $this->pjModel = new PenanggungJawabReadOnlyModel();
        helper(['form', 'url']);
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $db = db_connect();

        $builder = $db->table('ylpi_karyawan');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama_gelar', $search)
                ->orLike('npk', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        $builder->where('flag_karyawan', '1');

        $perPage = 20;
        $total = $builder->countAllResults(false);

        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $currentPage = ($currentPage < 1) ? 1 : $currentPage;
        $offset = ($currentPage - 1) * $perPage;

        $records = $builder->select('id_kpe, npk, nama_gelar, unit_kerja, kategori, email')
                           ->limit($perPage, $offset)
                           ->orderBy('nama_gelar', 'ASC')
                           ->get()
                           ->getResultArray();

        $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

        $data = [
            'title' => 'Referensi Penanggung Jawab',
            'penanggungJawab' => $records,
            'total' => $total,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'search' => $search,
            'perPage' => $perPage,
        ];

        return view('master/penanggung_jawab/index', $data);
    }

    public function show($id)
    {
        $pj = $this->pjModel->find(urldecode($id));
        if (!$pj) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan'])->setStatusCode(404);
        }

        return $this->response->setJSON($pj);
    }
}