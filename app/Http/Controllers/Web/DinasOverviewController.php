<?php

namespace App\Http\Controllers\Web;

use App\Charts\DinasOverviewService;
use App\Charts\LaporanDinasService;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DinasOverviewController extends Controller
{
    public function index(): View
    {
        return view('dinas.index', [
            'overviewData' => DinasOverviewService::build()
        ]);
    }

    /**
     * Pastikan nama method ini adalah laporanIndex sesuai route context Anda
     */
    public function laporanIndex(): View
    {
        return view('dinas.laporan', [
            'laporanData' => LaporanDinasService::build()
        ]);
    }

    // Alias jika ada rute lain yang memanggil ->laporan()
    public function laporan(): View
    {
        return $this->laporanIndex();
    }
}