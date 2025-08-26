<?php

namespace App\Http\Controllers\Psikolog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $psychologist = Auth::user();
        return view('psikolog.dashboard', compact('psychologist'));
    }
}
