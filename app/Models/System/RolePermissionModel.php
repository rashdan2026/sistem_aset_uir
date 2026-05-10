<?php

namespace App\Models\System;

use CodeIgniter\Model;

class RolePermissionModel extends Model
{
    protected $table = 'sys_role_permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $allowedFields = [
        'role_id', 'permission_id', 'is_active', 'created_at', 'updated_at'
    ];

    public function getPermissionsByRoleId(int $roleId)
    {
        return $this->db->table($this->table . ' rp')
            ->select('p.permission_code, p.module_code, p.action_code')
            ->join('sys_permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $roleId)
            ->where('rp.is_active', 1)
            ->where('p.is_active', 1)
            ->get()
            ->getResultArray();
    }

    public function getPermissionsByUserId(int $userId)
    {
        return $this->db->table($this->table . ' rp')
            ->select('p.permission_code, p.module_code, p.action_code')
            ->join('sys_permissions p', 'p.id = rp.permission_id')
            ->join('sys_user_roles ur', 'ur.role_id = rp.role_id')
            ->where('ur.user_id', $userId)
            ->where('ur.is_active', 1)
            ->where('rp.is_active', 1)
            ->where('p.is_active', 1)
            ->get()
            ->getResultArray();
    }
}