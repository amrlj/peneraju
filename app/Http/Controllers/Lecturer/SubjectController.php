<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        return view('lecturer.subjects.index', [
            'subjects' => Subject::withCount(['classes', 'questions'])
                ->where('created_by', auth()->id())
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create()
    {
        return view('lecturer.subjects.form', [
            'subject' => new Subject,
            'classes' => SchoolClass::where('lecturer_id', auth()->id())->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $classIds = $data['class_ids'] ?? [];
        unset($data['class_ids']);
        $data['created_by'] = auth()->id();

        $subject = DB::transaction(function () use ($data, $classIds) {
            $subject = Subject::create($data);
            $this->syncClasses($subject, $classIds);
            return $subject;
        });

        return redirect()->route('lecturer.subjects.index')->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject)
    {
        $this->own($subject);
        return view('lecturer.subjects.form', [
            'subject' => $subject,
            'classes' => SchoolClass::where('lecturer_id', auth()->id())->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Subject $subject)
    {
        $this->own($subject);
        $data = $this->validated($request, $subject);
        $classIds = $data['class_ids'] ?? [];
        unset($data['class_ids']);

        DB::transaction(function () use ($subject, $data, $classIds) {
            $subject->update($data);
            $this->syncClasses($subject, $classIds);
        });

        return redirect()->route('lecturer.subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $this->own($subject);
        abort_if($subject->exams()->exists(), 422, 'Cannot delete a subject that has exams.');
        $subject->delete();
        return back()->with('success', 'Subject deleted.');
    }

    private function syncClasses(Subject $subject, array $ids): void
    {
        $sync = [];
        foreach ($ids as $id) {
            $sync[$id] = ['lecturer_id' => auth()->id()];
        }
        $subject->classes()->sync($sync);
    }

    private function own(Subject $subject): void
    {
        abort_unless($subject->created_by === auth()->id(), 403);
    }

    private function validated(Request $request, ?Subject $subject = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('subjects', 'code')->ignore($subject?->id)],
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
            'class_ids' => 'array',
            'class_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('classes', 'id')->where(fn($query) => $query->where('lecturer_id', auth()->id())),
            ],
        ]);
    }
}
