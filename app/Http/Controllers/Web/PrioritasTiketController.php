<?php

namespace App\Http\Controllers\Web;

use App\Charts\PrioritasTicketChart;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PrioritasTiketController extends Controller
{
    public function prioritasIndex(): View
    {
        return view('superadmin.prioritas-ticket', [
            'prioritasData' => PrioritasTicketChart::build()
        ]);
    }
}