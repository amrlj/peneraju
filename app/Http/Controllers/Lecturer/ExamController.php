<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ExamController extends Controller
{
    public function index()
    {
        return view('lecturer.exams.index', [
            'exams' => Exam::with(['subject', 'classes'])
                ->withCount('attempts')
                ->where('created_by', auth()->id())
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create()
    {
        return view('lecturer.exams.form', $this->formData(new Exam));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->assertSelections($data);

        $exam = DB::transaction(function () use ($data) {
            $classIds = $data['class_ids'];
            $questionIds = $data['question_ids'];
            unset($data['class_ids'], $data['question_ids']);
            $data['created_by'] = auth()->id();

            $exam = Exam::create($data);
            $exam->classes()->sync($classIds);
            $this->syncQuestions($exam, $questionIds);
            return $exam;
        });

        return redirect()->route('lecturer.exams.index')->with('success', 'Exam created successfully.');
    }

    public function show(Exam $exam)
    {
        $this->own($exam);
        $exam->load(['subject', 'classes', 'questions.options']);
        return view('lecturer.exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $this->own($exam);
        abort_if($exam->attempts()->exists(), 422, 'An exam with attempts can no longer be edited.');
        return view('lecturer.exams.form', $this->formData($exam));
    }

    public function update(Request $request, Exam $exam)
    {
        $this->own($exam);
        abort_if($exam->attempts()->exists(), 422, 'An exam with attempts can no longer be edited.');
        $data = $this->validated($request);
        $this->assertSelections($data);

        DB::transaction(function () use ($data, $exam) {
            $classIds = $data['class_ids'];
            $questionIds = $data['question_ids'];
            unset($data['class_ids'], $data['question_ids']);
            $exam->update($data);
            $exam->classes()->sync($classIds);
            $this->syncQuestions($exam, $questionIds);
        });

        return redirect()->route('lecturer.exams.index')->with('success', 'Exam updated successfully.');
    }

    public function destroy(Exam $exam)
    {
        $this->own($exam);
        abort_if($exam->attempts()->exists(), 422, 'Cannot delete an exam with attempts.');
        $exam->delete();
        return back()->with('success', 'Exam deleted.');
    }

    public function publish(Exam $exam)
    {
        $this->own($exam);
        abort_if($exam->questions()->count() === 0 || $exam->classes()->count() === 0, 422, 'Add at least one class and question before publishing.');
        $exam->update(['status' => 'published']);
        return back()->with('success', 'Exam published successfully.');
    }

    private function formData(Exam $exam): array
    {
        return [
            'exam' => $exam,
            'subjects' => Subject::where('created_by', auth()->id())->where('is_active', true)->orderBy('name')->get(),
            'classes' => SchoolClass::where('lecturer_id', auth()->id())->where('is_active', true)->orderBy('name')->get(),
            'questions' => Question::with('subject')->where('created_by', auth()->id())->where('is_active', true)->orderBy('subject_id')->get(),
        ];
    }

    private function assertSelections(array $data): void
    {
        $subject = Subject::where('created_by', auth()->id())->findOrFail($data['subject_id']);

        $ownedClassCount = SchoolClass::where('lecturer_id', auth()->id())->whereIn('id', $data['class_ids'])->count();
        if ($ownedClassCount !== count(array_unique($data['class_ids']))) {
            throw ValidationException::withMessages(['class_ids' => 'One or more selected classes are invalid.']);
        }

        $subjectClassCount = $subject->classes()->whereIn('classes.id', $data['class_ids'])->count();
        if ($subjectClassCount !== count(array_unique($data['class_ids']))) {
            throw ValidationException::withMessages(['class_ids' => 'Every selected class must be associated with the selected subject.']);
        }

        $questionCount = Question::where('created_by', auth()->id())
            ->where('subject_id', $subject->id)
            ->whereIn('id', $data['question_ids'])
            ->count();
        if ($questionCount !== count(array_unique($data['question_ids']))) {
            throw ValidationException::withMessages(['question_ids' => 'Every selected question must belong to the selected subject.']);
        }
    }

    private function syncQuestions(Exam $exam, array $ids): void
    {
        $sync = [];
        foreach (array_values(array_unique($ids)) as $index => $id) {
            $question = Question::where('created_by', auth()->id())
                ->where('subject_id', $exam->subject_id)
                ->findOrFail($id);
            $sync[$id] = ['marks' => $question->marks, 'sort_order' => $index + 1];
        }
        $exam->questions()->sync($sync);
    }

    private function own(Exam $exam): void
    {
        abort_unless($exam->created_by === auth()->id(), 403);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'subject_id' => [
                'required',
                'integer',
                Rule::exists('subjects', 'id')->where(fn($query) => $query->where('created_by', auth()->id())),
            ],
            'title' => 'required|string|max:255',
            'instructions' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'duration_minutes' => 'required|integer|min:1|max:480',
            'passing_percentage' => 'required|numeric|min:0|max:100',
            'maximum_attempts' => 'required|integer|min:1|max:5',
            'status' => 'required|in:draft,published',
            'show_result' => 'required|boolean',
            'show_correct_answers' => 'required|boolean',
            'randomize_questions' => 'required|boolean',
            'randomize_options' => 'required|boolean',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => ['integer', 'distinct'],
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => ['integer', 'distinct'],
        ]);
    }
}
