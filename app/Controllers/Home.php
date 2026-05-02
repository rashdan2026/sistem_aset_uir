<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Home extends Controller
{
    public function index()
    {
        // If user is logged in, redirect to dashboard; otherwise, show welcome page
        if (session()->has('user_id')) {
            return redirect()->to('/admin/dashboard');
        }
        
        return view('welcome_message');
    }
}