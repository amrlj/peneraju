<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Services\ExamAccessService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $r)
    {
        $ids = $r->user()->classes()->pluck('classes.id');
        $exams = Exam::with(['subject', 'classes'])->where('status', 'published')->whereHas('classes', fn($q) => $q->whereIn('classes.id', $ids))->orderBy('start_at')->paginate(12);
        return view('student.exams.index', compact('exams'));
    }
    public function show(Request $r, Exam $exam)
    {
        (new ExamAccessService)->assertAssigned($r->user(), $exam);
        $exam->load(['subject', 'classes']);
        $attempts = $exam->attempts()->where('student_id', $r->user()->id)->latest()->get();
        return view('student.exams.show', compact('exam', 'attempts'));
    }
}
