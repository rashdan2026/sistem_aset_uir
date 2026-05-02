<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SystemSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();

        // Seed super_admin role
        if (!$db->table('sys_roles')->where('role_code', 'super_admin')->countAllResults()) {
            $db->table('sys_roles')->insert([
                'role_code' => 'super_admin',
                'role_name' => 'Super Administrator',
                'description' => 'Full system access',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "✓ Seeded: super_admin role\n";
        } else {
            echo "→ Skipped: super_admin role already exists\n";
        }

        // Seed admin user
        if (!$db->table('sys_users')->where('username', 'admin')->countAllResults()) {
            $db->table('sys_users')->insert([
                'username' => 'admin',
                'password_hash' => password_hash('myUIR2026', PASSWORD_DEFAULT),
                'full_name' => 'Administrator UIR',
                'email' => 'admin@uir.ac.id',
                'is_active' => 1,
                'force_password_change' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "✓ Seeded: admin user\n";
        } else {
            echo "→ Skipped: admin user already exists\n";
        }

        // Link admin → super_admin
        $roleId = $db->table('sys_roles')->select('id')->where('role_code', 'super_admin')->get()->getRow('id');
        $userId = $db->table('sys_users')->select('id')->where('username', 'admin')->get()->getRow('id');
        if ($roleId && $userId) {
            $exists = $db->table('sys_user_roles')->where(['user_id' => $userId, 'role_id' => $roleId])->countAllResults();
            if (!$exists) {
                $db->table('sys_user_roles')->insert([
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                echo "✓ Seeded: admin → super_admin link\n";
            } else {
                echo "→ Skipped: admin role link already exists\n";
            }
        }

        // Seed core permissions using permission_code (module.action format)
        $permissions = [
            ['module_code' => 'dashboard', 'action_code' => 'view', 'description' => 'View admin dashboard'],
            ['module_code' => 'subunit', 'action_code' => 'view', 'description' => 'View sub unit list'],
            ['module_code' => 'subunit', 'action_code' => 'create', 'description' => 'Create new sub unit'],
            ['module_code' => 'subunit', 'action_code' => 'update', 'description' => 'Edit existing sub unit'],
            ['module_code' => 'subunit', 'action_code' => 'delete', 'description' => 'Delete (soft-delete) sub unit'],
            ['module_code' => 'gedung', 'action_code' => 'view', 'description' => 'View building list'],
            ['module_code' => 'gedung', 'action_code' => 'create', 'description' => 'Create new building'],
            ['module_code' => 'gedung', 'action_code' => 'update', 'description' => 'Edit existing building'],
            ['module_code' => 'gedung', 'action_code' => 'delete', 'description' => 'Delete (soft-delete) building'],
            ['module_code' => 'lantai', 'action_code' => 'view', 'description' => 'View floor list'],
            ['module_code' => 'lantai', 'action_code' => 'create', 'description' => 'Create new floor'],
            ['module_code' => 'lantai', 'action_code' => 'update', 'description' => 'Edit existing floor'],
            ['module_code' => 'lantai', 'action_code' => 'delete', 'description' => 'Delete (soft-delete) floor'],
            ['module_code' => 'ruangan', 'action_code' => 'view', 'description' => 'View room list'],
            ['module_code' => 'ruangan', 'action_code' => 'create', 'description' => 'Create new room'],
            ['module_code' => 'ruangan', 'action_code' => 'update', 'description' => 'Edit existing room'],
            ['module_code' => 'ruangan', 'action_code' => 'delete', 'description' => 'Delete (soft-delete) room'],
            ['module_code' => 'kategori', 'action_code' => 'view', 'description' => 'View category list'],
            ['module_code' => 'kategori', 'action_code' => 'create', 'description' => 'Create new category'],
            ['module_code' => 'kategori', 'action_code' => 'update', 'description' => 'Edit existing category'],
            ['module_code' => 'kategori', 'action_code' => 'delete', 'description' => 'Delete (soft-delete) category'],
        ];

        $insertedIds = [];
        foreach ($permissions as $perm) {
            $permissionCode = $perm['module_code'] . '.' . $perm['action_code'];
            $existing = $db->table('sys_permissions')->where('permission_code', $permissionCode)->get()->getRow();
            if (!$existing) {
                $db->table('sys_permissions')->insert(array_merge($perm, [
                    'permission_code' => $permissionCode,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]));
                $insertedIds[] = $db->insertID();
                echo "✓ Seeded: {$permissionCode}\n";
            } else {
                $insertedIds[] = $existing->id;
                echo "→ Skipped: {$permissionCode} already exists\n";
            }
        }

        // Assign all permissions to super_admin role
        if ($roleId) {
            $allPerms = $db->table('sys_permissions')->select('id')->get()->getResultArray();
            foreach ($allPerms as $p) {
                $exists = $db->table('sys_role_permissions')->where(['role_id' => $roleId, 'permission_id' => $p['id']])->countAllResults();
                if (!$exists) {
                    $db->table('sys_role_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $p['id'],
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            echo "✓ Assigned all permissions to super_admin role\n";
        }
    }
}
