<?php
namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    public function index()
    {
        return view('lecturer.classes.index', [
            'classes' => SchoolClass::withCount('students')
                ->where('lecturer_id', auth()->id())
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create()
    {
        return view('lecturer.classes.form', [
            'class' => new SchoolClass,
            'students' => User::where('role', 'student')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $studentIds = $data['student_ids'] ?? [];
        unset($data['student_ids']);
        $data['lecturer_id'] = auth()->id();

        $class = DB::transaction(function () use ($data, $studentIds) {
            $class = SchoolClass::create($data);
            $class->students()->sync($studentIds);
            return $class;
        });

        return redirect()->route('lecturer.classes.index')->with('success', 'Class created successfully.');
    }

    public function edit(SchoolClass $class)
    {
        $this->own($class);
        return view('lecturer.classes.form', [
            'class' => $class,
            'students' => User::where('role', 'student')->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SchoolClass $class)
    {
        $this->own($class);
        $data = $this->validated($request, $class);
        $studentIds = $data['student_ids'] ?? [];
        unset($data['student_ids']);

        DB::transaction(function () use ($class, $data, $studentIds) {
            $class->update($data);
            $class->students()->sync($studentIds);
        });

        return redirect()->route('lecturer.classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        $this->own($class);
        $class->delete();
        return back()->with('success', 'Class deleted.');
    }

    private function own(SchoolClass $class): void
    {
        abort_unless($class->lecturer_id === auth()->id(), 403);
    }

    private function validated(Request $request, ?SchoolClass $class = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'code' => ['required', 'string', 'max:50', Rule::unique('classes', 'code')->ignore($class?->id)],
            'description' => 'nullable|string',
            'academic_year' => 'nullable|string|max:30',
            'is_active' => 'required|boolean',
            'student_ids' => 'array',
            'student_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'student')),
            ],
        ]);
    }
}
