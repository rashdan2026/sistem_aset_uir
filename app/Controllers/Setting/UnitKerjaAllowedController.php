<?php

namespace App\Controllers\Setting;

use App\Models\Reference\SettingUnitKerjaModel;
use App\Models\Reference\UnitKerjaReadOnlyModel;
use CodeIgniter\Controller;

class UnitKerjaAllowedController extends Controller
{
    protected $settingModel;
    protected $unitKerjaModel;

    public function __construct()
    {
        $this->settingModel = new SettingUnitKerjaModel();
        $this->unitKerjaModel = new UnitKerjaReadOnlyModel();
        helper(['form', 'url']);
    }

    protected function isAdmin(): bool
    {
        if (!session()->has('user_id')) {
            log_message('warning', 'isAdmin: no user_id in session');
            return false;
        }

        $userId = session('user_id');
        $db = db_connect();
        $role = $db->table('sys_roles r')
            ->select('r.role_code')
            ->join('sys_user_roles ur', 'ur.role_id = r.id')
            ->where('ur.user_id', $userId)
            ->where('ur.is_active', 1)
            ->get()
            ->getRowArray();

        log_message('info', 'isAdmin check: userId=' . $userId . ' role=' . json_encode($role));

        return $role && in_array($role['role_code'], ['super_admin', 'admin_aset_pusat']);
    }

    public function index()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $allUnits = $this->unitKerjaModel->where('flag_aktif', 1)
            ->orderBy('id_unit_kerja', 'ASC')
            ->findAll();

        $allowedSettings = [];
        foreach ($this->settingModel->findAll() as $s) {
            $allowedSettings[$s['id_unit_kerja']] = $s;
        }

        $orphanedIds = [];
        foreach ($allowedSettings as $idUk => $s) {
            $found = false;
            foreach ($allUnits as $u) {
                if ($u['id_unit_kerja'] == $idUk) {
                    $found = true;
                    break;
                }
            }
            if (!$found && $s['is_active'] == 1) {
                $orphanedIds[] = $idUk;
            }
        }

        $units = [];
        foreach ($allUnits as $u) {
            $setting = $allowedSettings[$u['id_unit_kerja']] ?? null;
            $units[] = [
                'id_unit_kerja' => $u['id_unit_kerja'],
                'nama_unit' => $u['nama_unit'],
                'is_allowed' => $setting && $setting['is_active'] == 1,
                'setting_id' => $setting ? $setting['id'] : null,
            ];
        }

        $data = [
            'title' => 'Setting Unit Kerja',
            'units' => $units,
            'totalAllowed' => count(array_filter($units, function($u) { return $u['is_allowed']; })),
            'totalAll' => count($units),
            'orphanedIds' => $orphanedIds,
        ];

        return view('setting/unit_kerja_allowed/index', $data);
    }

    public function toggle()
    {
        log_message('info', 'toggle: method called, method=' . $this->request->getMethod());

        if (!$this->isAdmin()) {
            log_message('warning', 'toggle: isAdmin check failed');
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $idUnitKerja = (int) $this->request->getPost('id_unit_kerja');
        $isActive = (int) $this->request->getPost('is_active');

        if ($idUnitKerja <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid ID']);
        }

        log_message('info', 'toggle called: id=' . $idUnitKerja . ', isActive=' . $isActive);

        $unit = $this->unitKerjaModel->find($idUnitKerja);
        if (!$unit) {
            log_message('error', 'toggle: unit not found id=' . $idUnitKerja);
            return $this->response->setJSON(['success' => false, 'message' => 'Unit kerja tidak ditemukan.']);
        }

        try {
            $result = $this->settingModel->toggleStatus($idUnitKerja, $isActive == 1);
            log_message('info', 'toggle result: ' . ($result ? 'OK' : 'FAIL'));

            return $this->response->setJSON([
                'success' => $result,
                'message' => $isActive ? 'Unit kerja diaktifkan.' : 'Unit kerja dinonaktifkan.'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'toggle exception: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function saveAll()
    {
        if (!$this->isAdmin()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $allowedIds = $this->request->getPost('allowed_ids');
        if (!is_array($allowedIds)) {
            $allowedIds = [];
        }

        log_message('info', 'saveAll called. Allowed count: ' . count($allowedIds));

        $allUnits = $this->unitKerjaModel->where('flag_aktif', 1)->findAll();

        log_message('info', 'saveAll source count: ' . count($allUnits));

        try {
            $savedCount = 0;
            foreach ($allUnits as $u) {
                $isAllowed = in_array($u['id_unit_kerja'], $allowedIds);
                $this->settingModel->upsert((int) $u['id_unit_kerja'], $isAllowed);
                $savedCount++;
            }

            log_message('info', 'saveAll processed ' . $savedCount . ' units');

            return $this->response->setJSON([
                'success' => true,
                'message' => count($allowedIds) . ' unit kerja berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'saveAll exception: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ]);
        }
    }
}