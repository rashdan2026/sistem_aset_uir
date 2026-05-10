<?php

namespace App\Models\System;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table = 'sys_user_roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $allowedFields = [
        'user_id', 'role_id', 'is_active', 'created_at', 'updated_at'
    ];

    public function getByUserId(int $userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_active', 1)
            ->findAll();
    }

    public function getRoleIdsByUserId(int $userId): array
    {
        $records = $this->where('user_id', $userId)
            ->where('is_active', 1)
            ->findAll();
        return array_column($records, 'role_id');
    }

    public function syncRoles(int $userId, array $roleIds)
    {
        $now = date('Y-m-d H:i:s');

        $this->where('user_id', $userId)->delete();

        foreach ($roleIds as $roleId) {
            $this->insert([
                'user_id' => $userId,
                'role_id' => $roleId,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return true;
    }
}