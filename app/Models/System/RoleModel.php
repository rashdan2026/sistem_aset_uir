<?php

namespace App\Models\System;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'sys_roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $allowedFields = [
        'role_code', 'role_name', 'description', 'is_active', 'created_at', 'updated_at'
    ];

    public function getActiveRoles()
    {
        return $this->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function getRoleByCode(string $code)
    {
        return $this->where('role_code', $code)->where('is_active', 1)->first();
    }

    public function getRolesByUserId(int $userId)
    {
        return $this->db->table($this->table . ' r')
            ->select('r.id, r.role_code, r.role_name')
            ->join('sys_user_roles ur', 'ur.role_id = r.id')
            ->where('ur.user_id', $userId)
            ->where('ur.is_active', 1)
            ->where('r.is_active', 1)
            ->orderBy('r.id', 'ASC')
            ->get()
            ->getResultArray();
    }
}