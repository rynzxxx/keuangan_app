<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LogAktivitasModel;

class History extends BaseController
{
    public function index()
    {
        $logModel = new LogAktivitasModel();
        $data = [
            'title' => 'Riwayat Aktivitas (Log)',
            'logs' => $logModel->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('dashboard_keuangan/history/index', $data);
    }
}
