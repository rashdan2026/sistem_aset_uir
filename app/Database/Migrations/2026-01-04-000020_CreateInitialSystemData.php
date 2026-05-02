<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInitialSystemData extends Migration
{
    public function up()
    {
        // Get role_id for super_admin (will be inserted by CreateSysRolesTable)
        $roleId = $this->db->table('sys_roles')
            ->select('id')
            ->where('role_code', 'super_admin')
            ->get()
            ->getRow();

        if (!$roleId) {
            // Insert super_admin role if not exists
            $this->db->table('sys_roles')->insert([
                'role_code' => 'super_admin',
                'role_name' => 'Super Administrator',
                'description' => 'Full system access',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $roleId = $this->db->insertID();
        } else {
            $roleId = $roleId->id;
        }

        // Generate correct password hash for 'myUIR2026'
        $passwordHash = password_hash('myUIR2026', PASSWORD_DEFAULT);

        // Insert admin user
        $this->db->table('sys_users')->insert([
            'username' => 'admin',
            'password_hash' => $passwordHash,
            'full_name' => 'Administrator UIR',
            'email' => 'admin@uir.ac.id',
            'is_active' => 1,
            'force_password_change' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $adminId = $this->db->insertID();

        // Link admin → super_admin
        $this->db->table('sys_user_roles')->insert([
            'user_id' => $adminId,
            'role_id' => $roleId,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Insert default permissions
        $modules = [
            ['module' => 'dashboard', 'actions' => ['view']],
            ['module' => 'unit_kerja', 'actions' => ['view', 'lookup']],
            ['module' => 'penanggung_jawab', 'actions' => ['view', 'lookup']],
            ['module' => 'sub_unit', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'gedung', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'lantai', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'ruangan', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'kategori', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'sub_kategori', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'golongan', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'merk', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'type', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'kondisi_barang', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'sumber_dana', 'actions' => ['view', 'create', 'update', 'delete', 'export']],
            ['module' => 'user', 'actions' => ['view', 'create', 'update', 'delete']],
            ['module' => 'role', 'actions' => ['view', 'create', 'update', 'delete']],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($modules as $module) {
            foreach ($module['actions'] as $action) {
                $this->db->table('sys_permissions')->insert([
                    'permission_code' => $module['module'] . '.' . $action,
                    'module_code' => $module['module'],
                    'action_code' => $action,
                    'description' => ucfirst($action) . ' ' . str_replace('_', ' ', ucfirst($module['module'])),
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        // Give super_admin all permissions
        $permissions = $this->db->table('sys_permissions')->select('id')->get()->getResult();
        foreach ($permissions as $perm) {
            $this->db->table('sys_role_permissions')->insert([
                'role_id' => $roleId,
                'permission_id' => $perm->id,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        // Seed kondisi barang default
        $kondisi = [
            ['kode' => 'BK', 'nama' => 'Baik', 'level' => 1, 'can_use' => 1],
            ['kode' => 'RR', 'nama' => 'Rusak Ringan', 'level' => 2, 'can_use' => 1],
            ['kode' => 'RB', 'nama' => 'Rusak Berat', 'level' => 3, 'can_use' => 0],
            ['kode' => 'TD', 'nama' => 'Tidak Ditemukan', 'level' => 4, 'can_use' => 0],
            ['kode' => 'DP', 'nama' => 'Dalam Perbaikan', 'level' => 5, 'can_use' => 0],
            ['kode' => 'NH', 'nama' => 'Nonaktif/Dihapus', 'level' => 6, 'can_use' => 0],
        ];

        foreach ($kondisi as $k) {
            $this->db->table('aset_kondisi_barang')->insert([
                'kode_kondisi' => $k['kode'],
                'nama_kondisi' => $k['nama'],
                'level_kondisi' => $k['level'],
                'is_available_for_use' => $k['can_use'],
                'keterangan' => '',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }

        // Seed sumber dana default
        $sumberDana = [
            ['kode' => 'AU', 'nama' => 'Anggaran UIR'],
            ['kode' => 'HB', 'nama' => 'Hibah'],
            ['kode' => 'BP', 'nama' => 'Bantuan Pemerintah'],
            ['kode' => 'CR', 'nama' => 'CSR'],
            ['kode' => 'DF', 'nama' => 'Dana Fakultas/Unit'],
            ['kode' => 'LN', 'nama' => 'Lainnya'],
        ];

        foreach ($sumberDana as $sd) {
            $this->db->table('aset_sumber_dana')->insert([
                'kode_sumber_dana' => $sd['kode'],
                'nama_sumber_dana' => $sd['nama'],
                'keterangan' => '',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }

    public function down()
    {
        // Get admin user
        $admin = $this->db->table('sys_users')
            ->select('id')
            ->where('username', 'admin')
            ->get()
            ->getRow();

        if ($admin) {
            $this->db->table('sys_user_roles')->delete(['user_id' => $admin->id]);
            $this->db->table('sys_users')->delete(['username' => 'admin']);
        }

        // Clear permissions for super_admin role
        $role = $this->db->table('sys_roles')
            ->select('id')
            ->where('role_code', 'super_admin')
            ->get()
            ->getRow();

        if ($role) {
            $this->db->table('sys_role_permissions')->delete(['role_id' => $role->id]);
            $this->db->table('sys_permissions')->delete();
            $this->db->table('sys_roles')->delete(['role_code' => 'super_admin']);
        }

        // Clear seed data
        $this->db->table('aset_kondisi_barang')->delete();
        $this->db->table('aset_sumber_dana')->delete();
    }
}
