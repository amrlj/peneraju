@extends('layouts.app')

@section('title', $exam->title)
@section('heading', $exam->title)

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Exam information --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Main exam details --}}
            <div class="rounded-xl border border-gray-200 bg-white
                       p-6 shadow-sm lg:col-span-2">
                <div class="flex flex-wrap items-center gap-2">

                    {{-- Subject --}}
                    <span
                        class="inline-flex items-center rounded-full
                               bg-indigo-100 px-2.5 py-1
                               text-xs font-semibold text-indigo-700">
                        {{ $exam->subject->name }}
                    </span>

                    {{-- Publication status --}}
                    @if ($exam->status === 'published')
                        <span
                            class="inline-flex items-center rounded-full
                                   bg-green-100 px-2.5 py-1
                                   text-xs font-semibold text-green-700">
                            Published
                        </span>
                    @elseif ($exam->status === 'draft')
                        <span
                            class="inline-flex items-center rounded-full
                                   bg-amber-100 px-2.5 py-1
                                   text-xs font-semibold text-amber-800">
                            Draft
                        </span>
                    @else
                        <span
                            class="inline-flex items-center rounded-full
                                   bg-gray-100 px-2.5 py-1
                                   text-xs font-semibold text-gray-600">
                            {{ ucfirst(str_replace('_', ' ', $exam->status)) }}
                        </span>
                    @endif

                    {{-- Availability status --}}
                    @if (now()->between($exam->start_at, $exam->end_at))
                        <span
                            class="inline-flex items-center rounded-full
                                   bg-blue-100 px-2.5 py-1
                                   text-xs font-semibold text-blue-700">
                            Active now
                        </span>
                    @elseif (now()->lt($exam->start_at))
                        <span
                            class="inline-flex items-center rounded-full
                                   bg-purple-100 px-2.5 py-1
                                   text-xs font-semibold text-purple-700">
                            Upcoming
                        </span>
                    @else
                        <span
                            class="inline-flex items-center rounded-full
                                   bg-gray-100 px-2.5 py-1
                                   text-xs font-semibold text-gray-600">
                            Ended
                        </span>
                    @endif
                </div>

                {{-- Instructions --}}
                <div class="mt-6">
                    <h2 class="text-lg font-bold text-gray-900">
                        Instructions
                    </h2>

                    <p class="mt-2 whitespace-pre-line leading-7 text-gray-600">
                        {{ $exam->instructions ?: 'No special instructions.' }}
                    </p>
                </div>

                {{-- Exam summary --}}
                <dl class="mt-6 grid gap-4 sm:grid-cols-2">

                    {{-- Availability --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-gray-500">
                            Availability
                        </dt>

                        <dd class="mt-2 text-sm font-semibold leading-6 text-gray-900">
                            {{ $exam->start_at->format('d M Y, h:i A') }}

                            <span class="block font-normal text-gray-500">
                                to
                            </span>

                            {{ $exam->end_at->format('d M Y, h:i A') }}
                        </dd>
                    </div>

                    {{-- Duration --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-gray-500">
                            Duration
                        </dt>

                        <dd class="mt-2 text-sm font-semibold text-gray-900">
                            {{ $exam->duration_minutes }} minutes
                        </dd>
                    </div>

                    {{-- Passing mark --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-gray-500">
                            Passing mark
                        </dt>

                        <dd class="mt-2 text-sm font-semibold text-gray-900">
                            {{ number_format($exam->passing_percentage, 2) }}%
                        </dd>
                    </div>

                    {{-- Total marks --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-gray-500">
                            Total marks
                        </dt>

                        <dd class="mt-2 text-sm font-semibold text-gray-900">
                            {{ number_format($exam->totalMarks(), 2) }}
                        </dd>
                    </div>

                    {{-- Maximum attempts --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-gray-500">
                            Maximum attempts
                        </dt>

                        <dd class="mt-2 text-sm font-semibold text-gray-900">
                            {{ $exam->maximum_attempts }}
                        </dd>
                    </div>

                    {{-- Question count --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <dt
                            class="text-xs font-semibold uppercase
                                   tracking-wide text-gray-500">
                            Questions
                        </dt>

                        <dd class="mt-2 text-sm font-semibold text-gray-900">
                            {{ $exam->questions->count() }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Assigned classes --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">
                    Assigned classes
                </h2>

                <div class="mt-4 space-y-3">
                    @forelse ($exam->classes as $class)
                        <div class="rounded-lg border border-gray-100
                                   bg-gray-50 p-4">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $class->name }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $class->code }}

                                @if ($class->academic_year)
                                    <span aria-hidden="true">•</span>
                                    {{ $class->academic_year }}
                                @endif
                            </p>
                        </div>
                    @empty
                        <div
                            class="rounded-lg border border-dashed
                                   border-gray-300 bg-gray-50
                                   px-4 py-8 text-center">
                            <p class="text-sm font-medium text-gray-600">
                                No classes assigned.
                            </p>
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('lecturer.exams.results', $exam) }}"
                    class="mt-5 inline-flex w-full items-center justify-center
                           rounded-lg bg-indigo-600 px-4 py-2.5
                           text-sm font-semibold text-white shadow-sm
                           transition hover:bg-indigo-700
                           focus:outline-none focus:ring-2
                           focus:ring-indigo-500 focus:ring-offset-2">
                    View Results
                </a>
            </div>
        </div>

        {{-- Questions --}}
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        Questions
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $exam->questions->count() }}
                        {{ $exam->questions->count() === 1 ? 'question' : 'questions' }}
                        assigned to this exam.
                    </p>
                </div>

                <span
                    class="inline-flex items-center rounded-full
                           bg-emerald-100 px-3 py-1
                           text-xs font-semibold text-emerald-700">
                    {{ number_format($exam->totalMarks(), 2) }} total marks
                </span>
            </div>

            <div class="mt-5 space-y-4">
                @forelse ($exam->questions as $question)
                    <div class="rounded-lg border border-gray-200 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold leading-6 text-gray-900">
                                    {{ $loop->iteration }}.
                                    {{ $question->question_text }}
                                </p>
                            </div>

                            <span
                                class="inline-flex shrink-0 items-center
                                       rounded-full bg-emerald-100
                                       px-2.5 py-1 text-xs font-semibold
                                       text-emerald-700">
                                {{ number_format($question->pivot->marks ?? $question->marks, 2) }}
                                marks
                            </span>
                        </div>

                        <div class="mt-3">
                            @if ($question->question_type === 'multiple_choice')
                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-blue-100 px-2.5 py-1
                                           text-xs font-semibold text-blue-700">
                                    Multiple choice
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-purple-100 px-2.5 py-1
                                           text-xs font-semibold text-purple-700">
                                    Open text
                                </span>
                            @endif
                        </div>

                        {{-- Multiple-choice options --}}
                        @if ($question->isMultipleChoice())
                            <div class="mt-4 space-y-2">
                                @forelse ($question->options as $option)
                                    <div
                                        class="flex items-start gap-3 rounded-lg
                                               border p-3
                                               {{ $option->is_correct ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                                        <span
                                            class="inline-flex h-6 w-6 shrink-0
                                                   items-center justify-center
                                                   rounded-full text-xs font-bold
                                                   {{ $option->is_correct ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                            {{ $loop->iteration }}
                                        </span>

                                        <p
                                            class="flex-1 text-sm
                                                   {{ $option->is_correct ? 'font-semibold text-green-800' : 'text-gray-700' }}">
                                            {{ $option->option_text }}
                                        </p>

                                        @if ($option->is_correct)
                                            <span
                                                class="shrink-0 text-sm
                                                       font-bold text-green-700">
                                                Correct
                                            </span>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">
                                        No answer options available.
                                    </p>
                                @endforelse
                            </div>
                        @endif
                    </div>
                @empty
                    <div
                        class="rounded-lg border border-dashed border-gray-300
                               bg-gray-50 px-6 py-12 text-center">
                        <p class="text-sm font-semibold text-gray-700">
                            No questions assigned.
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            Edit this exam to add questions.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Bottom actions --}}
        <div class="mt-6 flex flex-wrap items-center gap-3">
            <a href="{{ route('lecturer.exams.index') }}"
                class="inline-flex items-center justify-center rounded-lg
                       border border-gray-300 bg-white px-4 py-2.5
                       text-sm font-semibold text-gray-700 shadow-sm
                       transition hover:bg-gray-50 focus:outline-none
                       focus:ring-2 focus:ring-indigo-500
                       focus:ring-offset-2">
                Back to Exams
            </a>

            @if (!$exam->attempts_count)
                <a href="{{ route('lecturer.exams.edit', $exam) }}"
                    class="inline-flex items-center justify-center rounded-lg
                           bg-indigo-600 px-4 py-2.5 text-sm font-semibold
                           text-white shadow-sm transition hover:bg-indigo-700
                           focus:outline-none focus:ring-2
                           focus:ring-indigo-500 focus:ring-offset-2">
                    Edit Exam
                </a>
            @endif
        </div>
    </div>
@endsection
