<?php

namespace App\Http\Controllers\Web;

use App\Charts\SeranganKeamananChart;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SeranganKeamananController extends Controller
{
    /**
     * Dashboard Analisis Serangan Siber & Kerentanan OPD
     */
    public function index(): View
    {
        return view('superadmin.serangan', [
            'securityData' => SeranganKeamananChart::build()
        ]);
    }
}