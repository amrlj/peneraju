@extends('layouts.app')

@section('title', $exam->exists ? 'Edit Exam' : 'Create Exam')
@section('heading', $exam->exists ? 'Edit Exam' : 'Create Exam')

@section('content')
    @php
        $selectedClasses = collect(
            old('class_ids', $exam->exists ? $exam->classes()->pluck('classes.id')->all() : []),
        )->map(fn($id) => (int) $id);

        $selectedQuestions = collect(
            old('question_ids', $exam->exists ? $exam->questions()->pluck('questions.id')->all() : []),
        )->map(fn($id) => (int) $id);
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST"
            action="{{ $exam->exists ? route('lecturer.exams.update', $exam) : route('lecturer.exams.store') }}"
            class="space-y-6">
            @csrf

            @if ($exam->exists)
                @method('PUT')
            @endif

            {{-- Exam information --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">
                        Exam information
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Configure the exam schedule, duration, passing mark, and visibility.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    {{-- Exam title --}}
                    <div>
                        <label for="title" class="mb-2 block text-sm font-medium text-gray-700">
                            Exam title
                        </label>

                        <input id="title" type="text" name="title" value="{{ old('title', $exam->title) }}"
                            required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm focus:border-indigo-500
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200">

                        @error('title')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Subject --}}
                    <div>
                        <label for="subject_id" class="mb-2 block text-sm font-medium text-gray-700">
                            Subject
                        </label>

                        <select id="subject_id" name="subject_id" required
                            class="block w-full rounded-lg border border-gray-300 bg-white
                                   px-3 py-2 text-gray-900 shadow-sm
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-200">
                            <option value="">
                                Select subject
                            </option>

                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id', $exam->subject_id) == $subject->id)>
                                    {{ $subject->code }} — {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('subject_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Start date --}}
                    <div>
                        <label for="start_at" class="mb-2 block text-sm font-medium text-gray-700">
                            Starts at
                        </label>

                        <input id="start_at" type="datetime-local" name="start_at"
                            value="{{ old('start_at', $exam->start_at?->format('Y-m-d\TH:i')) }}"
                            required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm focus:border-indigo-500
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200">

                        @error('start_at')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- End date --}}
                    <div>
                        <label for="end_at" class="mb-2 block text-sm font-medium text-gray-700">
                            Ends at
                        </label>

                        <input id="end_at" type="datetime-local" name="end_at"
                            value="{{ old('end_at', $exam->end_at?->format('Y-m-d\TH:i')) }}"
                            required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm focus:border-indigo-500
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200">

                        @error('end_at')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Duration --}}
                    <div>
                        <label for="duration_minutes" class="mb-2 block text-sm font-medium text-gray-700">
                            Duration (minutes)
                        </label>

                        <input id="duration_minutes" type="number" name="duration_minutes" min="1" max="480"
                            value="{{ old('duration_minutes', $exam->duration_minutes ?: 15) }}"
                            required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm focus:border-indigo-500
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200">

                        @error('duration_minutes')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Passing percentage --}}
                    <div>
                        <label for="passing_percentage" class="mb-2 block text-sm font-medium text-gray-700">
                            Passing percentage
                        </label>

                        <div class="relative">
                            <input id="passing_percentage" type="number" name="passing_percentage" min="0"
                                max="100" step="0.01"
                                value="{{ old('passing_percentage', $exam->passing_percentage ?? 50) }}"
                                required
                                class="block w-full rounded-lg border border-gray-300
                                       px-3 py-2 pr-10 text-gray-900 shadow-sm
                                       focus:border-indigo-500 focus:outline-none
                                       focus:ring-2 focus:ring-indigo-200">

                            <span
                                class="pointer-events-none absolute inset-y-0 right-3
                                       flex items-center text-sm text-gray-500">
                                %
                            </span>
                        </div>

                        @error('passing_percentage')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Maximum attempts --}}
                    <div>
                        <label for="maximum_attempts" class="mb-2 block text-sm font-medium text-gray-700">
                            Maximum attempts
                        </label>

                        <input id="maximum_attempts" type="number" name="maximum_attempts" min="1" max="5"
                            value="{{ old('maximum_attempts', $exam->maximum_attempts ?: 1) }}"
                            required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm focus:border-indigo-500
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200">

                        @error('maximum_attempts')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-gray-700">
                            Status
                        </label>

                        <select id="status" name="status"
                            class="block w-full rounded-lg border border-gray-300 bg-white
                                   px-3 py-2 text-gray-900 shadow-sm
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-200">
                            <option value="draft" @selected(old('status', $exam->status ?: 'draft') === 'draft')>
                                Draft
                            </option>

                            <option value="published" @selected(old('status', $exam->status) === 'published')>
                                Published
                            </option>
                        </select>

                        @error('status')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Instructions --}}
                    <div class="md:col-span-2">
                        <label for="instructions" class="mb-2 block text-sm font-medium text-gray-700">
                            Instructions
                        </label>

                        <textarea id="instructions" name="instructions" rows="4"
                            placeholder="Enter instructions that students should read before starting the exam."
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm placeholder:text-gray-400
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-200">{{ old('instructions', $exam->instructions) }}</textarea>

                        @error('instructions')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Exam settings --}}
                <div class="mt-6 border-t border-gray-200 pt-6">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-gray-700">
                        Exam settings
                    </h3>

                    <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
            'show_result' => 'Show result to student',
            'show_correct_answers' => 'Show correct answers',
            'randomize_questions' => 'Randomize questions',
            'randomize_options' => 'Randomize MCQ options',
        ] as $field => $label)
                            <label
                                class="flex cursor-pointer items-start gap-3 rounded-lg
                                       border border-gray-200 p-4 transition
                                       hover:border-indigo-400 hover:bg-indigo-50">
                                <input type="hidden" name="{{ $field }}" value="0">

                                <input type="checkbox" name="{{ $field }}" value="1"
                                    @checked(old($field, $exam->exists ? $exam->{$field} : $field === 'show_result'))
                                    class="mt-0.5 h-4 w-4 shrink-0 rounded
                                           border-gray-300 text-indigo-600
                                           focus:ring-indigo-500">

                                <span class="text-sm font-medium text-gray-700">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Assign classes --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        Assign classes
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Only students from the selected classes can access this exam.
                    </p>
                </div>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($classes as $class)
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-lg
                                   border border-gray-200 p-4 transition
                                   hover:border-indigo-400 hover:bg-indigo-50">
                            <input type="checkbox" name="class_ids[]" value="{{ $class->id }}"
                                @checked($selectedClasses->contains((int) $class->id))
                                class="h-4 w-4 shrink-0 rounded border-gray-300
                                       text-indigo-600 focus:ring-indigo-500">

                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-gray-900">
                                    {{ $class->name }}
                                </span>

                                <span class="mt-1 block text-xs text-gray-500">
                                    {{ $class->code }}
                                </span>
                            </span>
                        </label>
                    @empty
                        <div
                            class="rounded-lg border border-dashed border-gray-300
                                   bg-gray-50 px-4 py-8 text-center
                                   sm:col-span-2 lg:col-span-3">
                            <p class="text-sm font-medium text-gray-600">
                                No classes are available.
                            </p>

                            <a href="{{ route('lecturer.classes.create') }}"
                                class="mt-3 inline-flex text-sm font-semibold text-indigo-600
                                       hover:text-indigo-800">
                                Create a class first
                            </a>
                        </div>
                    @endforelse
                </div>

                @error('class_ids')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                @error('class_ids.*')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Add questions --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        Add questions
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Choose questions belonging to the selected subject. Their default
                        marks will be copied into the exam.
                    </p>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($questions as $question)
                        <label
                            class="flex cursor-pointer items-start gap-4 rounded-lg
                                   border border-gray-200 p-4 transition
                                   hover:border-indigo-400 hover:bg-indigo-50">
                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}"
                                @checked($selectedQuestions->contains((int) $question->id))
                                class="mt-1 h-4 w-4 shrink-0 rounded border-gray-300
                                       text-indigo-600 focus:ring-indigo-500">

                            <span class="min-w-0 flex-1">
                                <span
                                    class="flex flex-wrap items-center gap-2 text-xs
                                           font-bold uppercase tracking-wide text-indigo-600">
                                    <span>
                                        {{ $question->subject->code }}
                                    </span>

                                    <span class="text-gray-300">•</span>

                                    <span>
                                        {{ $question->question_type === 'multiple_choice' ? 'MCQ' : 'Open Text' }}
                                    </span>

                                    <span class="text-gray-300">•</span>

                                    <span>
                                        {{ number_format($question->marks, 2) }}
                                        {{ $question->marks == 1 ? 'mark' : 'marks' }}
                                    </span>
                                </span>

                                <span class="mt-2 block text-sm font-semibold text-gray-900">
                                    {{ $question->question_text }}
                                </span>
                            </span>
                        </label>
                    @empty
                        <div
                            class="rounded-lg border border-dashed border-gray-300
                                   bg-gray-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-gray-600">
                                No questions are available.
                            </p>

                            <a href="{{ route('lecturer.questions.create') }}"
                                class="mt-3 inline-flex text-sm font-semibold text-indigo-600
                                       hover:text-indigo-800">
                                Create questions first
                            </a>
                        </div>
                    @endforelse
                </div>

                @error('question_ids')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                @error('question_ids.*')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Form buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg
                           bg-indigo-600 px-5 py-2.5 text-sm font-semibold
                           text-white shadow-sm transition hover:bg-indigo-700
                           focus:outline-none focus:ring-2 focus:ring-indigo-500
                           focus:ring-offset-2">
                    {{ $exam->exists ? 'Update Exam' : 'Save Exam' }}
                </button>

                <a href="{{ route('lecturer.exams.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border
                           border-gray-300 bg-white px-5 py-2.5
                           text-sm font-semibold text-gray-700 shadow-sm
                           transition hover:bg-gray-50 focus:outline-none
                           focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
