<?php

namespace App\Http\Controllers\Web;

use App\Charts\PemprovSlaChart;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PemprovSlaController extends Controller
{
    /**
     * Tampilkan Dasbor Pemantauan SLA Pemprov (Mode Pantau)
     */
    public function index(): View
    {
        return view('admin.index', [
            'pemprovData' => PemprovSlaChart::build()
        ]);
    }
}