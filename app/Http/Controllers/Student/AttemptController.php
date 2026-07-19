<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\QuestionOption;
use App\Models\StudentAnswer;
use App\Services\ExamAccessService;
use App\Services\ExamScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttemptController extends Controller
{
    public function start(Request $r, Exam $exam, ExamAccessService $access)
    {
        $attempt = $access->start($r->user(), $exam);
        return redirect()->route('student.attempts.show', $attempt);
    }
    public function show(Request $r, ExamAttempt $attempt, ExamScoringService $scorer)
    {
        $this->own($r, $attempt);
        if ($attempt->status === 'in_progress' && now()->gt($attempt->expires_at)) {
            $scorer->submit($attempt, true);
            return redirect()->route('student.attempts.result', $attempt)->with('success', 'Time expired. Your exam was submitted automatically.');
        }
        $attempt->load(['exam.questions.options', 'answers']);
        $questions = $attempt->exam->questions;
        if ($attempt->exam->randomize_questions) $questions = $questions->shuffle();
        return view('student.attempts.show', compact('attempt', 'questions'));
    }
    public function saveAnswer(Request $r, ExamAttempt $attempt)
    {
        $this->own($r, $attempt);
        if (!$attempt->isActive()) throw ValidationException::withMessages(['attempt' => 'This attempt is no longer active.']);
        $data = $r->validate(['question_id' => 'required|integer', 'question_option_id' => 'nullable|integer', 'answer_text' => 'nullable|string|max:10000']);
        $question = $attempt->exam->questions()->where('questions.id', $data['question_id'])->firstOrFail();
        $optionId = null;
        $text = null;
        if ($question->isMultipleChoice()) {
            $option = QuestionOption::where('question_id', $question->id)->findOrFail($data['question_option_id']);
            $optionId = $option->id;
        } else {
            $text = $data['answer_text'] ?? null;
        }
        StudentAnswer::updateOrCreate(['exam_attempt_id' => $attempt->id, 'question_id' => $question->id], ['question_option_id' => $optionId, 'answer_text' => $text]);
        return response()->json(['saved' => true, 'saved_at' => now()->format('H:i:s')]);
    }
    public function submit(Request $r, ExamAttempt $attempt, ExamScoringService $scorer)
    {
        $this->own($r, $attempt);
        $automatic = now()->gt($attempt->expires_at);
        $attempt = $scorer->submit($attempt, $automatic);
        return redirect()->route('student.attempts.result', $attempt)->with('success', 'Your exam has been submitted.');
    }
    public function result(Request $r, ExamAttempt $attempt)
    {
        $this->own($r, $attempt);
        abort_if($attempt->status === 'in_progress', 403, 'Submit the exam before viewing the result.');
        $attempt->load(['exam.subject', 'exam.questions.options', 'answers.selectedOption']);
        return view('student.attempts.result', compact('attempt'));
    }
    private function own(Request $r, ExamAttempt $a): void
    {
        abort_unless($a->student_id === $r->user()->id, 403);
    }
}
