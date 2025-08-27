<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{

    public function index()
    {
        $data = [
            'title' => 'Halaman Dashboard Utama'
        ];

        return view('dashboard_keuangan/dashboard', $data);
    }
}
