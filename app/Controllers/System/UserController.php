<?php

namespace App\Controllers\System;

use CodeIgniter\Controller;

class UserController extends Controller
{
    public function __construct() { helper(['form', 'url']); }

    public function index()
    {
        return view('system/users/index', ['title' => 'Kelola User']);
    }

    public function show($id)
    {
        return view('system/users/show', ['title' => 'Detail User', 'id' => $id]);
    }

    public function new()
    {
        return view('system/users/form', ['title' => 'Tambah User']);
    }

    public function create()
    {
        return redirect()->to('/system/users')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('system/users/form', ['title' => 'Edit User', 'id' => $id]);
    }

    public function update($id)
    {
        return redirect()->to('/system/users')->with('success', 'User berhasil diperbarui.');
    }

    public function delete($id)
    {
        return redirect()->to('/system/users')->with('success', 'User berhasil dihapus.');
    }
}
