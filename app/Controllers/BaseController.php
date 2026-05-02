<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation.
     *
     * @var array
     */
    protected $helpers = ['auth', 'form', 'url'];

    /**
     * Constructor.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc.
        $this->session = service('session');
    }

    /**
     * Check if user is logged in
     */
    protected function requireLogin(): bool
    {
        if (!session()->has('user_id')) {
            return false;
        }
        return true;
    }

    /**
     * Get current user info
     */
    protected function getCurrentUser(): ?array
    {
        if (!$this->requireLogin()) {
            return null;
        }
        return [
            'id' => session('user_id'),
            'username' => session('username'),
            'full_name' => session('full_name'),
            'email' => session('email'),
        ];
    }

    /**
     * Render JSON response
     */
    protected function jsonResponse(array $data, int $status = 200): ResponseInterface
    {
        return $this->response->setJSON($data)->setStatusCode($status);
    }

    /**
     * Flash success message
     */
    protected function flashSuccess(string $message): void
    {
        session()->setFlashdata('success', $message);
    }

    /**
     * Flash error message
     */
    protected function flashError(string $message): void
    {
        session()->setFlashdata('error', $message);
    }

    /**
     * Log audit trail
     */
    protected function logAudit(string $action, ?int $recordId = null, ?array $oldData = null, ?array $newData = null): void
    {
        $userId = session('user_id') ?? 0;
        $db = db_connect();
        
        $db->table('sys_audit_logs')->insert([
            'user_id' => $userId,
            'action' => $action,
            'table_name' => 'multiple',
            'record_id' => $recordId,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}