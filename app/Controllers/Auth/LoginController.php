<?php

namespace App\Controllers\Auth;

use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\Session\Session;

class LoginController extends Controller
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

    public function index()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->has('user_id')) {
            return redirect()->to('/admin/dashboard');
        }
        
        return view('auth/login');
    }

    public function attemptLogin()
    {
        // Check for too many login attempts from this IP
        $ipAddress = $this->request->getIPAddress();
        if ($this->isTooManyAttempts($ipAddress)) {
            $this->session->setFlashdata('error', 'Terlalu banyak percobaan login. Silakan coba lagi nanti.');
            return redirect()->to('/auth/login');
        }

        // Validate input
        $rules = [
            'username' => 'required|trim|min_length[3]|max_length[50]',
            'password' => 'required|trim|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return view('auth/login', [
                'validation' => $this->validator
            ]);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Find user by username and active status
        $user = $this->userModel
            ->where('username', $username)
            ->where('is_active', 1)
            ->first();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            // Log failed login attempt
            $this->logLoginAttempt($user ? $user['id'] : null, false);
            
            $this->session->setFlashdata('error', 'Username atau password salah.');
            return redirect()->to('/auth/login');
        }

        // Regenerate session ID for security
        $this->session->regenerate();

        // Set user session data
        $this->session->set([
            'user_id'               => $user['id'],
            'username'              => $user['username'],
            'full_name'             => $user['full_name'],
            'email'                 => $user['email'],
            'force_password_change' => (bool) $user['force_password_change'],
            'logged_in'             => true,
        ]);

        // Log successful login
        $this->logLoginAttempt($user['id'], true);

        // Check if user needs to change password
        if ($user['force_password_change']) {
            return redirect()->to('/auth/change-password');
        }

        // Determine redirect destination based on user role
        $role = $this->userModel->getUserRole($user['id']);
        if ($role && in_array($role->role_code, ['super_admin', 'admin_aset_pusat', 'admin_unit'])) {
            return redirect()->to('/admin/dashboard');
        }

        return redirect()->to('/home');
    }

    public function logout()
    {
        // Log the logout
        if ($this->session->has('user_id')) {
            $this->logLogout();
        }
        
        // Destroy session
        $this->session->destroy();
        
        return redirect()->to('/auth/login');
    }

    private function isTooManyAttempts(string $ipAddress): bool
    {
        $db = db_connect();
        $builder = $db->table('sys_login_attempts');
        
        // Check failed attempts in last 15 minutes
        $limit = 5; // Max attempts
        $timeWindow = 15 * 60; // 15 minutes in seconds
        
        $attempts = $builder
            ->where('ip_address', $ipAddress)
            ->where('is_success', 0)
            ->where('attempt_time >=', date('Y-m-d H:i:s', time() - $timeWindow))
            ->countAllResults();
            
        return $attempts >= $limit;
    }

    private function logLoginAttempt(?int $userId, bool $success): void
    {
        $db = db_connect();
        $builder = $db->table('sys_login_attempts');
        
        $builder->insert([
            'user_id'      => $userId,
            'ip_address'   => $this->request->getIPAddress(),
            'attempt_time' => date('Y-m-d H:i:s'),
            'is_success'   => $success ? 1 : 0,
        ]);
    }

    private function logLogout(): void
    {
        // Optionally log logout in a separate table or update login attempt
        // For now, we'll just note the logout in session
        $this->session->remove('logged_in');
    }
}
