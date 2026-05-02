<?php

namespace App\Controllers\System;

use CodeIgniter\Controller;

class RoleController extends Controller
{
    public function __construct() { helper(['form', 'url']); }

    public function index()
    {
        return view('system/roles/index', ['title' => 'Kelola Role']);
    }

    public function show($id)
    {
        return view('system/roles/show', ['title' => 'Detail Role', 'id' => $id]);
    }

    public function new()
    {
        return view('system/roles/form', ['title' => 'Tambah Role']);
    }

    public function create()
    {
        return redirect()->to('/system/roles')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('system/roles/form', ['title' => 'Edit Role', 'id' => $id]);
    }

    public function update($id)
    {
        return redirect()->to('/system/roles')->with('success', 'Role berhasil diperbarui.');
    }

    public function delete($id)
    {
        return redirect()->to('/system/roles')->with('success', 'Role berhasil dihapus.');
    }
}
