<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\ExamScoringService;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Exam $exam)
    {
        $this->own($exam);
        return view('lecturer.results.index', ['exam' => $exam, 'attempts' => $exam->attempts()->with('student')->latest('submitted_at')->paginate(20)]);
    }
    public function show(ExamAttempt $attempt)
    {
        $this->own($attempt->exam);
        $attempt->load(['student', 'exam.questions.options', 'answers.selectedOption']);
        return view('lecturer.results.show', compact('attempt'));
    }
    public function update(Request $r, ExamAttempt $attempt, ExamScoringService $scorer)
    {
        $this->own($attempt->exam);
        $data = $r->validate(['answers' => 'required|array', 'answers.*.marks_awarded' => 'required|numeric|min:0', 'answers.*.lecturer_feedback' => 'nullable|string|max:2000']);
        $scorer->markOpenText($attempt, $r->user(), $data['answers']);
        return back()->with('success', 'Marks and feedback saved successfully.');
    }
    public function export(Exam $exam)
    {
        $this->own($exam);
        $rows = $exam->attempts()->with('student')->get();
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Student', 'Email', 'Attempt', 'Status', 'Score', 'Percentage', 'Result', 'Submitted At']);
            foreach ($rows as $a) fputcsv($out, [$a->student->name, $a->student->email, $a->attempt_number, $a->status, $a->total_score, $a->percentage, $a->result_status, $a->submitted_at]);
            fclose($out);
        }, str($exam->title)->slug() . '-results.csv', ['Content-Type' => 'text/csv']);
    }
    private function own(Exam $e): void
    {
        abort_unless($e->created_by === auth()->id(), 403);
    }
}
