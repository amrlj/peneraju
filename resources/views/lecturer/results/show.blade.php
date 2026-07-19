@extends('layouts.app')

@section('title', 'Review Attempt')
@section('heading', 'Review Attempt: ' . $attempt->student->name)

@section('content')
    @php
        if ($attempt->result_status === 'passed') {
            $resultText = 'Passed';
            $resultClass = 'bg-green-100 text-green-700';
        } elseif ($attempt->result_status === 'failed') {
            $resultText = 'Failed';
            $resultClass = 'bg-red-100 text-red-700';
        } elseif ($attempt->status === 'in_progress') {
            $resultText = 'In Progress';
            $resultClass = 'bg-blue-100 text-blue-700';
        } else {
            $resultText = $attempt->result_status ? ucfirst(str_replace('_', ' ', $attempt->result_status)) : 'Pending';

            $resultClass = 'bg-amber-100 text-amber-800';
        }
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Attempt summary --}}
        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Exam --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Exam
                </p>

                <p class="mt-2 font-bold text-gray-900">
                    {{ $attempt->exam->title }}
                </p>

                <p class="mt-1 text-xs text-gray-500">
                    Attempt #{{ $attempt->attempt_number }}
                </p>
            </div>

            {{-- Current score --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Current score
                </p>

                <p class="mt-2 text-2xl font-black text-indigo-700">
                    {{ number_format($attempt->total_score ?? 0, 2) }}
                </p>

                <p class="mt-1 text-xs text-gray-500">
                    out of {{ number_format($attempt->exam->totalMarks(), 2) }}
                </p>
            </div>

            {{-- Percentage --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Percentage
                </p>

                <p class="mt-2 text-2xl font-black text-gray-900">
                    {{ number_format($attempt->percentage ?? 0, 2) }}%
                </p>

                <p class="mt-1 text-xs text-gray-500">
                    Passing mark:
                    {{ number_format($attempt->exam->passing_percentage, 2) }}%
                </p>
            </div>

            {{-- Result --}}
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Result
                </p>

                <div class="mt-2">
                    <span
                        class="inline-flex items-center rounded-full px-3 py-1
                               text-sm font-semibold {{ $resultClass }}">
                        {{ $resultText }}
                    </span>
                </div>

                @if ($attempt->submitted_at)
                    <p class="mt-2 text-xs text-gray-500">
                        Submitted {{ $attempt->submitted_at->format('d M Y, h:i A') }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Student information --}}
        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-gray-900">
                        {{ $attempt->student->name }}
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $attempt->student->email }}
                    </p>
                </div>

                <span
                    class="inline-flex items-center rounded-full bg-indigo-100
                           px-3 py-1 text-xs font-semibold text-indigo-700">
                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('lecturer.attempts.mark', $attempt) }}" class="space-y-5">
            @csrf
            @method('PUT')

            @forelse ($attempt->exam->questions as $question)
                @php
                    $answer = $attempt->answers->firstWhere('question_id', $question->id);

                    $maximumMarks = $question->pivot->marks ?? ($question->marks ?? 0);
                @endphp

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                    {{-- Question heading --}}
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-gray-100 px-2.5 py-1
                                           text-xs font-semibold text-gray-700">
                                    Question {{ $loop->iteration }}
                                </span>

                                @if ($question->isMultipleChoice())
                                    <span
                                        class="inline-flex items-center rounded-full
                                               bg-blue-100 px-2.5 py-1
                                               text-xs font-semibold text-blue-700">
                                        Multiple Choice
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full
                                               bg-purple-100 px-2.5 py-1
                                               text-xs font-semibold text-purple-700">
                                        Open Text
                                    </span>
                                @endif
                            </div>

                            <h2 class="mt-3 font-bold leading-7 text-gray-900">
                                {{ $question->question_text }}
                            </h2>
                        </div>

                        <span
                            class="inline-flex shrink-0 items-center rounded-full
                                   bg-emerald-100 px-2.5 py-1
                                   text-xs font-semibold text-emerald-700">
                            Max {{ number_format($maximumMarks, 2) }}
                        </span>
                    </div>

                    @if ($question->isMultipleChoice())
                        {{-- Multiple-choice answer --}}
                        <div class="mt-5 rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <dl class="space-y-4">
                                <div>
                                    <dt
                                        class="text-xs font-semibold uppercase
                                               tracking-wide text-gray-500">
                                        Selected answer
                                    </dt>

                                    <dd class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $answer?->selectedOption?->option_text ?: 'No answer selected.' }}
                                    </dd>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    <div>
                                        <dt
                                            class="text-xs font-semibold uppercase
                                                   tracking-wide text-gray-500">
                                            Marks awarded
                                        </dt>

                                        <dd class="mt-1 text-sm font-bold text-gray-900">
                                            {{ number_format($answer?->marks_awarded ?? 0, 2) }}
                                            / {{ number_format($maximumMarks, 2) }}
                                        </dd>
                                    </div>

                                    <div class="ml-auto">
                                        @if ($answer?->is_correct)
                                            <span
                                                class="inline-flex items-center rounded-full
                                                       bg-green-100 px-2.5 py-1
                                                       text-xs font-semibold text-green-700">
                                                Correct
                                            </span>
                                        @elseif ($answer)
                                            <span
                                                class="inline-flex items-center rounded-full
                                                       bg-red-100 px-2.5 py-1
                                                       text-xs font-semibold text-red-700">
                                                Incorrect
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full
                                                       bg-gray-100 px-2.5 py-1
                                                       text-xs font-semibold text-gray-600">
                                                Not answered
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </dl>
                        </div>
                    @else
                        {{-- Open-text answer --}}
                        <div class="mt-5">
                            <p
                                class="mb-2 text-xs font-semibold uppercase
                                       tracking-wide text-gray-500">
                                Student answer
                            </p>

                            <div
                                class="min-h-24 whitespace-pre-line rounded-lg
                                       border border-gray-200 bg-gray-50 p-4
                                       text-sm leading-7 text-gray-700">
                                {{ $answer?->answer_text ?: 'No answer submitted.' }}
                            </div>
                        </div>

                        {{-- Manual marking --}}
                        <div class="mt-5 grid gap-5 md:grid-cols-3">
                            <div>
                                <label for="marks_{{ $question->id }}"
                                    class="mb-2 block text-sm font-medium text-gray-700">
                                    Marks awarded
                                </label>

                                <input id="marks_{{ $question->id }}" type="number"
                                    name="answers[{{ $question->id }}][marks_awarded]" min="0"
                                    max="{{ $maximumMarks }}" step="0.01"
                                    value="{{ old('answers.' . $question->id . '.marks_awarded', $answer?->marks_awarded ?? 0) }}"
                                    required
                                    class="block w-full rounded-lg border border-gray-300
                                           px-3 py-2 text-gray-900 shadow-sm
                                           focus:border-indigo-500 focus:outline-none
                                           focus:ring-2 focus:ring-indigo-200">

                                <p class="mt-1 text-xs text-gray-500">
                                    Maximum {{ number_format($maximumMarks, 2) }} marks.
                                </p>

                                @error('answers.' . $question->id . '.marks_awarded')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="feedback_{{ $question->id }}"
                                    class="mb-2 block text-sm font-medium text-gray-700">
                                    Feedback
                                </label>

                                <textarea id="feedback_{{ $question->id }}" name="answers[{{ $question->id }}][lecturer_feedback]" rows="4"
                                    placeholder="Add feedback for the student..."
                                    class="block w-full rounded-lg border border-gray-300
                                           px-3 py-2 text-gray-900 shadow-sm
                                           placeholder:text-gray-400
                                           focus:border-indigo-500 focus:outline-none
                                           focus:ring-2 focus:ring-indigo-200">{{ old('answers.' . $question->id . '.lecturer_feedback', $answer?->lecturer_feedback) }}</textarea>

                                @error('answers.' . $question->id . '.lecturer_feedback')
                                    <p class="mt-1 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div
                    class="rounded-xl border border-dashed border-gray-300
                           bg-white px-6 py-12 text-center">
                    <p class="text-sm font-semibold text-gray-700">
                        No questions are attached to this exam.
                    </p>
                </div>
            @endforelse

            {{-- Form actions --}}
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg
                           bg-indigo-600 px-5 py-2.5 text-sm font-semibold
                           text-white shadow-sm transition hover:bg-indigo-700
                           focus:outline-none focus:ring-2 focus:ring-indigo-500
                           focus:ring-offset-2">
                    Save Marks and Recalculate
                </button>

                <a href="{{ route('lecturer.exams.results', $attempt->exam) }}"
                    class="inline-flex items-center justify-center rounded-lg
                           border border-gray-300 bg-white px-5 py-2.5
                           text-sm font-semibold text-gray-700 shadow-sm
                           transition hover:bg-gray-50 focus:outline-none
                           focus:ring-2 focus:ring-indigo-500
                           focus:ring-offset-2">
                    Back to Results
                </a>
            </div>
        </form>
    </div>
@endsection
