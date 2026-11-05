<?php

namespace App\Http\Controllers\Web;

use App\Charts\KinerjaOpdChart;
use App\Charts\MonitoringLayananCharts;
use App\Charts\LaporanSlaDinasService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MonitoringTiketControllerPemprov extends Controller
{
    /**
     * Menu Monitoring Tiket Layanan
     */
    public function index(): View
    {
        return view('admin.monitoring', [
            'monitoringData' => MonitoringLayananCharts::build()
        ]);
    }

    public function kinerjaIndex(): View
    {
        return view('admin.kinerja', [
            'kinerjaData' => KinerjaOpdChart::build()
        ]);
    }

    public function laporanIndex(): View
    {
        return view('admin.laporan', [
            'laporanData' => LaporanSlaDinasService::build()
        ]);
    }
}