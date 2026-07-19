<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route(auth()->user()->isLecturer() ? 'lecturer.dashboard' : 'student.dashboard');
    }
}
