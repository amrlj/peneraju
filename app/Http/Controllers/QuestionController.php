<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with('subject')->where('created_by', auth()->id());
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->integer('subject_id'));
        }

        return view('lecturer.questions.index', [
            'questions' => $query->latest()->paginate(10)->withQueryString(),
            'subjects' => Subject::where('created_by', auth()->id())->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('lecturer.questions.form', [
            'question' => new Question,
            'subjects' => Subject::where('created_by', auth()->id())->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $question = DB::transaction(function () use ($data) {
            $options = $data['options'] ?? [];
            unset($data['options']);
            $data['created_by'] = auth()->id();
            $question = Question::create($data);
            $this->saveOptions($question, $options);
            return $question;
        });

        return redirect()->route('lecturer.questions.index')->with('success', 'Question created successfully.');
    }

    public function edit(Question $question)
    {
        $this->own($question);
        $question->load('options');
        return view('lecturer.questions.form', [
            'question' => $question,
            'subjects' => Subject::where('created_by', auth()->id())->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $this->own($question);
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $question) {
            $options = $data['options'] ?? [];
            unset($data['options']);
            $question->update($data);
            $question->options()->delete();
            $this->saveOptions($question, $options);
        });

        return redirect()->route('lecturer.questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        $this->own($question);
        abort_if($question->exams()->exists(), 422, 'Cannot delete a question already used in an exam.');
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }

    private function saveOptions(Question $question, array $options): void
    {
        if (!$question->isMultipleChoice()) {
            return;
        }

        $options = collect($options)
            ->filter(fn($option) => trim((string) ($option['option_text'] ?? '')) !== '')
            ->values();

        if ($options->count() < 2) {
            throw ValidationException::withMessages(['options' => 'A multiple-choice question needs at least two options.']);
        }

        $correctCount = $options->filter(fn($option) => filter_var($option['is_correct'] ?? false, FILTER_VALIDATE_BOOL))->count();
        if ($correctCount !== 1) {
            throw ValidationException::withMessages(['options' => 'A multiple-choice question must have exactly one correct answer.']);
        }

        foreach ($options as $index => $option) {
            $question->options()->create([
                'option_text' => $option['option_text'],
                'is_correct' => filter_var($option['is_correct'] ?? false, FILTER_VALIDATE_BOOL),
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function own(Question $question): void
    {
        abort_unless($question->created_by === auth()->id(), 403);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'subject_id' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id')->where(fn($query) => $query->where('created_by', auth()->id())),
            ],
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,open_text',
            'marks' => 'required|numeric|min:0.5|max:100',
            'is_active' => 'required|boolean',
            'options' => 'nullable|array',
            'options.*.option_text' => 'nullable|string|max:1000',
            'options.*.is_correct' => 'nullable|boolean',
        ]);
    }
}
