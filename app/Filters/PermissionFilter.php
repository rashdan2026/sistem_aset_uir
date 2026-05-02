<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Require login
        if (!session()->has('user_id')) {
            return redirect()->to('/auth/login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $userId = session('user_id');
        $uri = service('uri');
        $currentPath = $uri->getPath();

        // Map URI paths to permission keys
        $permissionMap = [
            '/admin/dashboard' => 'dashboard.view',
            '/subunit'         => 'subunit.view',
            '/subunit/create'  => 'subunit.create',
            '/subunit/store'   => 'subunit.create',
            '/subunit/edit'    => 'subunit.update',
            '/subunit/update'  => 'subunit.update',
            '/subunit/delete'  => 'subunit.delete',
            '/gedung'          => 'gedung.view',
            '/gedung/create'   => 'gedung.create',
            '/gedung/store'    => 'gedung.create',
            '/gedung/edit'     => 'gedung.update',
            '/gedung/update'   => 'gedung.update',
            '/gedung/delete'   => 'gedung.delete',
        ];

        $requiredPermission = $permissionMap[$currentPath] ?? null;
        if (!$requiredPermission) {
            return; // No permission check required for this route
        }

        // Fetch user's permissions
        $db = db_connect();
        $builder = $db->table('sys_permissions');
        $builder->select('sys_permissions.permission_code');
        $builder->join('sys_role_permissions', 'sys_role_permissions.permission_id = sys_permissions.id', 'inner');
        $builder->join('sys_user_roles', 'sys_user_roles.role_id = sys_role_permissions.role_id', 'inner');
        $builder->where('sys_user_roles.user_id', $userId);
        $builder->where('sys_user_roles.is_active', 1);
        $builder->where('sys_role_permissions.is_active', 1);
        $builder->where('sys_permissions.is_active', 1);

        $userPermissions = $builder->get()->getResultArray();
        $userPermissionCodes = array_column($userPermissions, 'permission_code');

        if (!in_array($requiredPermission, $userPermissionCodes)) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after
    }
}
