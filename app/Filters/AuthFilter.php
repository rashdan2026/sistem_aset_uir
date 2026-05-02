<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->has('user_id')) {
            return redirect()->to('/auth/login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        // Force password change if required
        if (session()->get('force_password_change') === true) {
            $currentPath = $request->getUri()->getPath();
            if (!str_contains($currentPath, 'auth/change-password')) {
                return redirect()->to('/auth/change-password')->with('error', 'Silakan ubah password Anda terlebih dahulu.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after
    }
}
