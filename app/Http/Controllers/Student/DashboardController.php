<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $u = auth()->user();
        $classIds = $u->classes()->pluck('classes.id');
        $available = Exam::with('subject')->where('status', 'published')->whereHas('classes', fn($q) => $q->whereIn('classes.id', $classIds))->where('end_at', '>=', now())->orderBy('start_at')->limit(6)->get();
        $attempts = ExamAttempt::with('exam.subject')->where('student_id', $u->id)->latest()->limit(6)->get();
        return view('student.dashboard', compact('available', 'attempts'));
    }
}
