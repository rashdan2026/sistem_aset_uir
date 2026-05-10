<?php

namespace App\Controllers\Setting;

use App\Models\UserModel;
use App\Models\System\RoleModel;
use App\Models\System\UserRoleModel;
use App\Models\Reference\UnitKerjaAllowedModel;
use CodeIgniter\Controller;

class SettingUserController extends Controller
{
    protected $userModel;
    protected $roleModel;
    protected $userRoleModel;
    protected $unitKerjaModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->userRoleModel = new UserRoleModel();
        $this->unitKerjaModel = new UnitKerjaAllowedModel();
        helper(['form', 'url']);
    }

    protected function isAdmin(): bool
    {
        if (!session()->has('user_id')) {
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

        return $role && in_array($role['role_code'], ['super_admin', 'admin_aset_pusat']);
    }

    public function index()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $perPage = 20;
        $page = (int) ($this->request->getGet('page') ?? 1);
        $page = $page > 0 ? $page : 1;
        $search = trim($this->request->getGet('q') ?? '');

        $result = $this->userModel->getAllWithRoles($perPage, $page, $search);
        $users = $result['users'];
        $total = $result['total'];
        $totalPages = $total > 0 ? ceil($total / $perPage) : 1;

        $data = [
            'title' => 'Setting User',
            'users' => $users,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
        ];

        return view('setting/user/index', $data);
    }

    public function new()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $roles = $this->roleModel->getActiveRoles();
        $unitKerjaList = $this->unitKerjaModel->getActiveOptions();

        $data = [
            'title' => 'Tambah User',
            'roles' => $roles,
            'unitKerjaList' => $unitKerjaList,
            'user' => null,
            'userRoleIds' => [],
        ];

        return view('setting/user/form', $data);
    }

    public function create()
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[sys_users.username]',
            'password' => 'required|min_length[6]',
            'full_name' => 'required|max_length[150]',
            'email' => 'permit_empty|valid_email|max_length[150]|is_unique[sys_users.email]',
            'is_active' => 'in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = trim($this->request->getPost('username'));
        $existingUser = $this->userModel->where('username', $username)->first();
        if ($existingUser) {
            return redirect()->back()->withInput()->with('error', 'Username "' . $username . '" sudah digunakan.');
        }

        $roleIds = $this->request->getPost('roles') ?? [];

        $data = [
            'username' => $username,
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'full_name' => trim($this->request->getPost('full_name')),
            'email' => trim($this->request->getPost('email')) ?: null,
            'id_kpe' => $this->request->getPost('id_kpe') ?: null,
            'default_unit_kerja_id' => $this->request->getPost('default_unit_kerja_id') ?: null,
            'is_active' => (int) ($this->request->getPost('is_active') ?? 1),
            'force_password_change' => (int) ($this->request->getPost('force_password_change') ?? 1),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $userId = $this->userModel->insert($data);

        if (!$userId) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user. Silakan coba lagi.');
        }

        if (!empty($roleIds)) {
            $this->userRoleModel->syncRoles($userId, $roleIds);
        }

        return redirect()->to(base_url('/setting/user'))->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $user = $this->userModel->withDeleted()->find($id);
        if (!$user) {
            return redirect()->to(base_url('/setting/user'))->with('error', 'User tidak ditemukan.');
        }

        $roles = $this->roleModel->getActiveRoles();
        $userRoleIds = $this->userRoleModel->getRoleIdsByUserId((int) $id);
        $unitKerjaList = $this->unitKerjaModel->getActiveOptions();

        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'roles' => $roles,
            'userRoleIds' => $userRoleIds,
            'unitKerjaList' => $unitKerjaList,
        ];

        return view('setting/user/form', $data);
    }

    public function update($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $user = $this->userModel->withDeleted()->find($id);
        if (!$user) {
            return redirect()->to(base_url('/setting/user'))->with('error', 'User tidak ditemukan.');
        }

        $usernameRules = 'required|min_length[3]|max_length[50]|is_unique[sys_users.username,id,' . $id . ']';
        $emailValue = trim($this->request->getPost('email') ?? '');
        $emailRules = $emailValue ? 'valid_email|max_length[150]|is_unique[sys_users.email,id,' . $id . ']' : 'permit_empty|max_length[150]';

        $rules = [
            'username' => $usernameRules,
            'full_name' => 'required|max_length[150]',
            'email' => $emailRules,
            'is_active' => 'in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleIds = $this->request->getPost('roles') ?? [];

        $data = [
            'username' => trim($this->request->getPost('username')),
            'full_name' => trim($this->request->getPost('full_name')),
            'email' => $emailValue ?: null,
            'id_kpe' => $this->request->getPost('id_kpe') ?: null,
            'default_unit_kerja_id' => $this->request->getPost('default_unit_kerja_id') ?: null,
            'is_active' => (int) ($this->request->getPost('is_active') ?? 1),
            'force_password_change' => (int) ($this->request->getPost('force_password_change') ?? 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                return redirect()->back()->withInput()->with('error', 'Password baru minimal 6 karakter.');
            }
            $data['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            $data['force_password_change'] = 1;
        }

        if ($user['is_active'] == 0 && $user['deleted_at'] !== null) {
            $data['deleted_at'] = null;
            $data['is_active'] = 1;
        }

        $this->userModel->withDeleted()->update($id, $data);

        $this->userRoleModel->syncRoles((int) $id, $roleIds);

        return redirect()->to(base_url('/setting/user'))->with('success', 'User berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $user = $this->userModel->withDeleted()->find($id);
        if (!$user) {
            return redirect()->to(base_url('/setting/user'))->with('error', 'User tidak ditemukan.');
        }

        if ($user['is_active'] == 0 && $user['deleted_at'] !== null) {
            return redirect()->to(base_url('/setting/user'))->with('error', 'User sudah tidak aktif.');
        }

        $currentUserId = session('user_id');
        if ($id == $currentUserId) {
            return redirect()->to(base_url('/setting/user'))->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $this->userModel->withDeleted()->update($id, [
            'is_active' => 0,
            'deleted_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->userRoleModel->where('user_id', $id)->delete();

        return redirect()->to(base_url('/setting/user'))->with('success', 'User berhasil dinonaktifkan.');
    }
}