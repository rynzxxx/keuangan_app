<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel; // Gunakan UserModel yang baru kita buat

class Auth extends BaseController
{

    protected $session;
    public function __construct()
    {
        // Memuat library session dan helper url
        $this->session = \Config\Services::session();
        helper('url');
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard_keuangan/dashboard');
        }

        return view('login');
    }

    public function processLogin()
    {
        // 1. Inisialisasi Model
        $userModel = new UserModel();

        // 2. Ambil data dari form
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // 3. Cari user di database berdasarkan username
        $user = $userModel->getUserByUsername($username);

        // 4. Lakukan verifikasi
        if ($user) {
            // Jika user ditemukan, verifikasi password hash-nya
            if (password_verify($password, $user['password_hash'])) {
                // Jika password cocok, set session
                $userData = [
                    'username'   => $user['username'],
                    'isLoggedIn' => TRUE
                ];
                $this->session->set($userData);

                return redirect()->to('/dashboard_keuangan/dashboard');
            }
        }

        // 5. Jika user tidak ditemukan atau password salah
        $this->session->setFlashdata('error', 'Username atau Password salah!');
        return redirect()->to('/login');
    }

    public function logout()
    {
        // Hancurkan session dan redirect ke halaman login
        $this->session->destroy();
        return redirect()->to('/login');
    }
}
