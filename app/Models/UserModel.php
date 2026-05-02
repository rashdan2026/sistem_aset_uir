<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'sys_users';
    protected $primaryKey = 'id';
    protected $useSoftDeletes = true;
    protected $dateFormat = 'datetime';
    protected $deletedField = 'deleted_at';

    // Allow select * + join with roles
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
}
