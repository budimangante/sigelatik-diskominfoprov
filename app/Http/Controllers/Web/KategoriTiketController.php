<?php

namespace App\Http\Controllers\Web;

use App\Charts\KategoriTicketChart;
use App\Charts\TicketCategoryChart;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class KategoriTiketController extends Controller
{
    /**
     * Halaman Analisis & Grafik Kategori Tiket
     */
    public function index(): View
    {
        return view('superadmin.kategori-ticket', [
            'kategoriData' => TicketCategoryChart::subbuild()
        ]);
    }
}