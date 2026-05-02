<?php

namespace App\Controllers\Auth;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;

class PasswordController extends Controller
{
    protected $session;
    protected $request;
    protected $userModel;

    public function __construct()
    {
        $this->session = service('session');
        $this->request = service('request');
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    public function change()
    {
        // Ensure user is logged in
        if (!$this->session->has('user_id')) {
            return redirect()->to('/auth/login');
        }

        return view('auth/change_password');
    }

    public function update()
    {
        // Ensure user is logged in
        if (!$this->session->has('user_id')) {
            return redirect()->to('/auth/login');
        }

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]|differs[current_password]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return view('auth/change_password', [
                'validation' => $this->validator
            ]);
        }

        $userId = $this->session->get('user_id');
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');

        // Get user to verify current password
        $user = $this->userModel->find($userId);
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            $this->session->setFlashdata('error', 'Password saat ini salah.');
            return redirect()->to('/auth/change-password');
        }

        // Update password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->userModel->update($userId, [
            'password_hash' => $newPasswordHash,
            'force_password_change' => 0,  // Reset force change flag
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->setFlashdata('success', 'Password berhasil diubah.');

        // Redirect appropriately based on role
        $role = $this->userModel->getUserRole($userId);
        if ($role && in_array($role->role_code, ['super_admin', 'admin_aset_pusat', 'admin_unit'])) {
            return redirect()->to('/admin/dashboard');
        }

        return redirect()->to('/home');
    }
}