@extends('layouts.app') @section('title', $exam->exists ? 'Edit Exam' : 'Create Exam') @section('heading', $exam->exists ?
'Edit Exam' : 'Create Exam')
@section('content')
    @php($selectedClasses = collect(old('class_ids', $exam->exists ? $exam->classes()->pluck('classes.id')->all() : [])))
    @php($selectedQuestions = collect(old('question_ids', $exam->exists ? $exam->questions()->pluck('questions.id')->all() : [])))
    <form method="POST" action="{{ $exam->exists ? route('lecturer.exams.update', $exam) : route('lecturer.exams.store') }}"
        class="space-y-6">
        @csrf @if ($exam->exists)
            @method('PUT')
        @endif
        <div class="card space-y-5">
            <div class="grid gap-5 md:grid-cols-2">
                <div><label class="label">Exam title</label><input class="form-input" name="title"
                        value="{{ old('title', $exam->title) }}" required></div>
                <div><label class="label">Subject</label><select class="form-input" name="subject_id" required>
                        <option value="">Select subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id', $exam->subject_id) == $subject->id)>{{ $subject->code }} —
                                {{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="label">Starts at</label><input class="form-input" type="datetime-local" name="start_at"
                        value="{{ old('start_at', $exam->start_at?->format('Y-m-d\TH:i')) }}" required></div>
                <div><label class="label">Ends at</label><input class="form-input" type="datetime-local" name="end_at"
                        value="{{ old('end_at', $exam->end_at?->format('Y-m-d\TH:i')) }}" required></div>
                <div><label class="label">Duration (minutes)</label><input class="form-input" type="number" min="1"
                        max="480" name="duration_minutes"
                        value="{{ old('duration_minutes', $exam->duration_minutes ?: 15) }}" required></div>
                <div><label class="label">Passing percentage</label><input class="form-input" type="number" min="0"
                        max="100" step="0.01" name="passing_percentage"
                        value="{{ old('passing_percentage', $exam->passing_percentage ?: 50) }}" required></div>
                <div><label class="label">Maximum attempts</label><input class="form-input" type="number" min="1"
                        max="5" name="maximum_attempts"
                        value="{{ old('maximum_attempts', $exam->maximum_attempts ?: 1) }}" required></div>
                <div><label class="label">Status</label><select class="form-input" name="status">
                        <option value="draft" @selected(old('status', $exam->status ?: 'draft') === 'draft')>Draft</option>
                        <option value="published" @selected(old('status', $exam->status) === 'published')>Published</option>
                    </select></div>
                <div class="md:col-span-2"><label class="label">Instructions</label>
                    <textarea class="form-input" name="instructions" rows="4">{{ old('instructions', $exam->instructions) }}</textarea>
                </div>
            </div>
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
                @foreach (['show_result' => 'Show result to student', 'show_correct_answers' => 'Show correct answers', 'randomize_questions' => 'Randomize questions', 'randomize_options' => 'Randomize MCQ options'] as $field => $label)
                    <label class="flex items-center gap-2 rounded-lg border p-3"><input type="hidden"
                            name="{{ $field }}" value="0"><input class="rounded" type="checkbox"
                            name="{{ $field }}" value="1" @checked(old($field, $exam->exists ? $exam->{$field} : $field === 'show_result'))><span
                            class="text-sm">{{ $label }}</span></label>
                @endforeach
            </div>
        </div>
        <div class="card">
            <h2 class="font-bold">Assign classes</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($classes as $class)
                    <label class="flex items-center gap-3 rounded-lg border p-3"><input class="rounded" type="checkbox"
                            name="class_ids[]" value="{{ $class->id }}" @checked($selectedClasses->contains($class->id))><span><span
                                class="block text-sm font-semibold">{{ $class->name }}</span><span
                            class="text-xs text-slate-500">{{ $class->code }}</span></span></label>@empty<p
                        class="text-sm text-slate-500">Create a class first.</p>
                @endforelse
            </div>
        </div>
        <div class="card">
            <h2 class="font-bold">Add questions</h2>
            <p class="mt-1 text-sm text-slate-500">Choose questions belonging to the selected subject. Their default marks
                are copied into the exam.</p>
            <div class="mt-4 space-y-3">
                @forelse($questions as $question)
                    <label class="flex items-start gap-3 rounded-lg border p-4"><input class="mt-1 rounded" type="checkbox"
                            name="question_ids[]" value="{{ $question->id }}" @checked($selectedQuestions->contains($question->id))><span
                            class="flex-1"><span
                                class="block text-xs font-bold uppercase text-indigo-600">{{ $question->subject->code }} ·
                                {{ $question->question_type === 'multiple_choice' ? 'MCQ' : 'Open Text' }} ·
                                {{ number_format($question->marks, 2) }} marks</span><span
                            class="mt-1 block text-sm font-semibold">{{ $question->question_text }}</span></span></label>@empty
                    <p class="text-sm text-slate-500">Create questions first.</p>
                @endforelse
            </div>
        </div>
        <div class="flex gap-3"><button class="btn-primary">Save Exam</button><a class="btn-secondary"
                href="{{ route('lecturer.exams.index') }}">Cancel</a></div>
</form>@endsection
