<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedAdditionalRolesAndUserPermissions extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $additionalRoles = [
            ['role_code' => 'admin_aset_pusat', 'role_name' => 'Admin Aset Pusat', 'description' => 'Pengelola master dan validasi data aset pusat', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'admin_unit', 'role_name' => 'Admin Unit', 'description' => 'Pengelola data terbatas pada unit/fakultas tertentu', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'operator_unit', 'role_name' => 'Operator Unit', 'description' => 'Input data master operasional sesuai unit', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'viewer_pimpinan', 'role_name' => 'Viewer Pimpinan', 'description' => 'Melihat dashboard/listing/laporan tanpa ubah data', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['role_code' => 'auditor', 'role_name' => 'Auditor', 'description' => 'Melihat audit trail, data historis, dan exception', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($additionalRoles as $role) {
            $exists = $this->db->table('sys_roles')->where('role_code', $role['role_code'])->countAllResults();
            if ($exists == 0) {
                $this->db->table('sys_roles')->insert($role);
            }
        }

        $userPermissions = [
            ['permission_code' => 'user.view', 'module_code' => 'user', 'action_code' => 'view', 'description' => 'View user management', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['permission_code' => 'user.create', 'module_code' => 'user', 'action_code' => 'create', 'description' => 'Create user', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['permission_code' => 'user.update', 'module_code' => 'user', 'action_code' => 'update', 'description' => 'Update user', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['permission_code' => 'user.delete', 'module_code' => 'user', 'action_code' => 'delete', 'description' => 'Soft delete user', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['permission_code' => 'setting.view', 'module_code' => 'setting', 'action_code' => 'view', 'description' => 'View settings', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['permission_code' => 'setting.update', 'module_code' => 'setting', 'action_code' => 'update', 'description' => 'Update settings', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($userPermissions as $perm) {
            $exists = $this->db->table('sys_permissions')->where('permission_code', $perm['permission_code'])->countAllResults();
            if ($exists == 0) {
                $this->db->table('sys_permissions')->insert($perm);
            }
        }

        $superAdminRole = $this->db->table('sys_roles')->select('id')->where('role_code', 'super_admin')->get()->getRow();
        if ($superAdminRole) {
            foreach ($userPermissions as $perm) {
                $permRecord = $this->db->table('sys_permissions')->select('id')->where('permission_code', $perm['permission_code'])->get()->getRow();
                if ($permRecord) {
                    $exists = $this->db->table('sys_role_permissions')
                        ->where('role_id', $superAdminRole->id)
                        ->where('permission_id', $permRecord->id)
                        ->countAllResults();
                    if ($exists == 0) {
                        $this->db->table('sys_role_permissions')->insert([
                            'role_id' => $superAdminRole->id,
                            'permission_id' => $permRecord->id,
                            'is_active' => 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }

            $adminAsetRole = $this->db->table('sys_roles')->select('id')->where('role_code', 'admin_aset_pusat')->get()->getRow();
            if ($adminAsetRole) {
                foreach ($userPermissions as $perm) {
                    $permRecord = $this->db->table('sys_permissions')->select('id')->where('permission_code', $perm['permission_code'])->get()->getRow();
                    if ($permRecord) {
                        $exists = $this->db->table('sys_role_permissions')
                            ->where('role_id', $adminAsetRole->id)
                            ->where('permission_id', $permRecord->id)
                            ->countAllResults();
                        if ($exists == 0) {
                            $this->db->table('sys_role_permissions')->insert([
                                'role_id' => $adminAsetRole->id,
                                'permission_id' => $permRecord->id,
                                'is_active' => 1,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down()
    {
        $newRoleCodes = ['admin_aset_pusat', 'admin_unit', 'operator_unit', 'viewer_pimpinan', 'auditor'];
        foreach ($newRoleCodes as $code) {
            $role = $this->db->table('sys_roles')->select('id')->where('role_code', $code)->get()->getRow();
            if ($role) {
                $this->db->table('sys_role_permissions')->where('role_id', $role->id)->delete();
            }
            $this->db->table('sys_roles')->where('role_code', $code)->delete();
        }

        $newPermCodes = ['user.view', 'user.create', 'user.update', 'user.delete', 'setting.view', 'setting.update'];
        foreach ($newPermCodes as $code) {
            $perm = $this->db->table('sys_permissions')->select('id')->where('permission_code', $code)->get()->getRow();
            if ($perm) {
                $this->db->table('sys_role_permissions')->where('permission_id', $perm->id)->delete();
            }
            $this->db->table('sys_permissions')->where('permission_code', $code)->delete();
        }
    }
}