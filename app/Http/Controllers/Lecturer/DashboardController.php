<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $uid = auth()->id();
        return view(
            'lecturer.dashboard',
            ['stats' => ['classes' => SchoolClass::where('lecturer_id', $uid)->count(), 
            'subjects' => Subject::where('created_by', $uid)->count(), 
            'questions' => Question::where('created_by', $uid)->count(), 
            'exams' => Exam::where('created_by', $uid)->count(), 
            'students' => User::where('role', 'student')->whereHas('classes', fn($q) => $q->where('lecturer_id', $uid))->distinct()->count()], 
            'upcoming' => Exam::with('subject')->where('created_by', $uid)->where('end_at', '>=', now())->orderBy('start_at')->limit(5)->get(), 
            'pendingMarking' => ExamAttempt::with(['exam', 'student'])->whereHas('exam', fn($q) => $q->where('created_by', $uid))->whereIn('status', ['submitted', 'auto_submitted'])->where('result_status', 'pending')->latest('submitted_at')->limit(5)->get()]
        );
    }
}
