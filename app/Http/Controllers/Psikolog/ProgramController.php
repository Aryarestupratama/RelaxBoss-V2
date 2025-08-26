<?php

namespace App\Http\Controllers\Psikolog;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramController extends Controller
{
    public function index()
    {
        $psychologist = Auth::user();
        $programs = Program::where('mentor_id', $psychologist->id)
            ->withCount('enrolledUsers')
            ->latest()
            ->paginate(10);
        return view('psikolog.programs.index', compact('programs'));
    }
}