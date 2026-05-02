<?php

namespace App\Controllers\System;

use CodeIgniter\Controller;

class PermissionController extends Controller
{
    public function __construct() { helper(['form', 'url']); }

    public function index()
    {
        return view('system/permissions/index', ['title' => 'Kelola Permission']);
    }

    public function show($id)
    {
        return view('system/permissions/show', ['title' => 'Detail Permission', 'id' => $id]);
    }

    public function new()
    {
        return view('system/permissions/form', ['title' => 'Tambah Permission']);
    }

    public function create()
    {
        return redirect()->to('/system/permissions')->with('success', 'Permission berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('system/permissions/form', ['title' => 'Edit Permission', 'id' => $id]);
    }

    public function update($id)
    {
        return redirect()->to('/system/permissions')->with('success', 'Permission berhasil diperbarui.');
    }

    public function delete($id)
    {
        return redirect()->to('/system/permissions')->with('success', 'Permission berhasil dihapus.');
    }
}
