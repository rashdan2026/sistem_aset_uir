<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'sys_users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes = true;
    protected $dateFormat = 'datetime';
    protected $deletedField = 'deleted_at';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'username', 'password_hash', 'full_name', 'email',
        'id_kpe', 'default_unit_kerja_id', 'is_active',
        'force_password_change', 'last_login_at',
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function getUserRole(int $userId)
    {
        $builder = $this->db->table($this->table . ' u');
        $builder->select('r.role_code, r.role_name, r.id as role_id');
        $builder->join('sys_user_roles ur', 'ur.user_id = u.id');
        $builder->join('sys_roles r', 'r.id = ur.role_id');
        $builder->where('u.id', $userId);
        $builder->where('r.is_active', 1);
        $builder->where('ur.is_active', 1);

        return $builder->get()->getRow();
    }

    public function getUserWithRoles(int $userId)
    {
        $user = $this->withDeleted()->find($userId);
        if (!$user) {
            return null;
        }

        $db = db_connect();
        $roles = $db->table('sys_roles r')
            ->select('r.id, r.role_code, r.role_name')
            ->join('sys_user_roles ur', 'ur.role_id = r.id')
            ->where('ur.user_id', $userId)
            ->where('ur.is_active', 1)
            ->where('r.is_active', 1)
            ->get()
            ->getResultArray();

        $user['roles'] = $roles;
        return $user;
    }

    public function getAllWithRoles(int $perPage = 20, int $page = 1, string $search = '')
    {
        $builder = $this->builder();
        $builder->select('sys_users.*');

        if (!empty($search)) {
            $builder->groupStart();
            $builder->like('sys_users.username', $search, 'both');
            $builder->orLike('sys_users.full_name', $search, 'both');
            $builder->orLike('sys_users.email', $search, 'both');
            $builder->groupEnd();
        }

        $builder->where('sys_users.deleted_at', null);

        $total = $builder->countAllResults(false);
        $users = $builder->orderBy('sys_users.updated_at', 'DESC')
            ->get($perPage, ($page - 1) * $perPage)
            ->getResultArray();

        $db = db_connect();
        foreach ($users as &$user) {
            $roles = $db->table('sys_roles r')
                ->select('r.id, r.role_code, r.role_name')
                ->join('sys_user_roles ur', 'ur.role_id = r.id')
                ->where('ur.user_id', $user['id'])
                ->where('ur.is_active', 1)
                ->where('r.is_active', 1)
                ->get()
                ->getResultArray();
            $user['roles'] = $roles;
        }

        return [
            'users' => $users,
            'total' => $total,
        ];
    }
}