@extends('layouts.app')

@section('title', $exam->title)
@section('heading', $exam->title)

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Exam information --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">

                <span
                    class="inline-flex items-center rounded-full bg-indigo-100
                           px-3 py-1 text-xs font-semibold text-indigo-700">
                    {{ $exam->subject->name }}
                </span>

                <h2 class="mt-5 text-lg font-bold text-gray-900">
                    Instructions
                </h2>

                <p class="mt-2 whitespace-pre-line leading-7 text-gray-600">
                    {{ $exam->instructions ?: 'Answer every question and submit before the timer reaches zero.' }}
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">

                    {{-- Availability --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Availability
                        </p>

                        <p class="mt-2 text-sm font-semibold leading-6 text-gray-900">
                            {{ $exam->start_at->format('d M Y, h:i A') }}

                            <span class="block font-normal text-gray-500">
                                to
                            </span>

                            {{ $exam->end_at->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    {{-- Time limit --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Time limit
                        </p>

                        <p class="mt-2 text-sm font-semibold text-gray-900">
                            {{ $exam->duration_minutes }} minutes
                        </p>
                    </div>

                    {{-- Maximum attempts --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Maximum attempts
                        </p>

                        <p class="mt-2 text-sm font-semibold text-gray-900">
                            {{ $exam->maximum_attempts }}
                        </p>
                    </div>

                    {{-- Exam status --}}
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                            Exam status
                        </p>

                        <div class="mt-2">
                            @if (now()->between($exam->start_at, $exam->end_at))
                                <span
                                    class="inline-flex items-center rounded-full bg-green-100
                                           px-2.5 py-1 text-xs font-semibold text-green-700">
                                    Open
                                </span>
                            @elseif (now()->lt($exam->start_at))
                                <span
                                    class="inline-flex items-center rounded-full bg-blue-100
                                           px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    Upcoming
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center rounded-full bg-gray-100
                                           px-2.5 py-1 text-xs font-semibold text-gray-600">
                                    Closed
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attempts panel --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                <h2 class="text-lg font-bold text-gray-900">
                    Attempts
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Used {{ $attempts->count() }} of {{ $exam->maximum_attempts }}
                </p>

                {{-- Attempts progress --}}
                @php
                    $attemptPercentage =
                        $exam->maximum_attempts > 0
                            ? min(($attempts->count() / $exam->maximum_attempts) * 100, 100)
                            : 0;
                @endphp

                <div class="mt-4 h-2 overflow-hidden rounded-full bg-gray-200">
                    <div class="h-full rounded-full bg-indigo-600" style="width: {{ $attemptPercentage }}%"></div>
                </div>

                {{-- Attempt history --}}
                <div class="mt-5 space-y-3">
                    @forelse ($attempts as $attempt)
                        @php
                            $isInProgress = $attempt->status === 'in_progress';

                            $attemptUrl = $isInProgress
                                ? route('student.attempts.show', $attempt)
                                : route('student.attempts.result', $attempt);

                            if ($isInProgress) {
                                $attemptStatus = 'Continue';
                                $attemptStatusClass = 'bg-blue-100 text-blue-700';
                            } elseif ($attempt->result_status === 'passed') {
                                $attemptStatus = 'Passed';
                                $attemptStatusClass = 'bg-green-100 text-green-700';
                            } elseif ($attempt->result_status === 'failed') {
                                $attemptStatus = 'Failed';
                                $attemptStatusClass = 'bg-red-100 text-red-700';
                            } else {
                                $attemptStatus = ucfirst(str_replace('_', ' ', $attempt->status));
                                $attemptStatusClass = 'bg-amber-100 text-amber-800';
                            }
                        @endphp

                        <a href="{{ $attemptUrl }}"
                            class="block rounded-lg border border-gray-200 p-4
                                   transition hover:border-indigo-300 hover:bg-indigo-50">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-semibold text-gray-900">
                                    Attempt #{{ $attempt->attempt_number }}
                                </span>

                                <span
                                    class="inline-flex shrink-0 items-center rounded-full
                                           px-2.5 py-1 text-xs font-semibold
                                           {{ $attemptStatusClass }}">
                                    {{ $attemptStatus }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-5 text-center">
                            <p class="text-sm text-gray-500">
                                You have not started this exam yet.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Exam action --}}
                @if (now()->between($exam->start_at, $exam->end_at) && $attempts->count() < $exam->maximum_attempts)
                    @php
                        $inProgressAttempt = $attempts->firstWhere('status', 'in_progress');
                    @endphp

                    @if ($inProgressAttempt)
                        <a href="{{ route('student.attempts.show', $inProgressAttempt) }}"
                            class="mt-5 inline-flex w-full items-center justify-center rounded-lg
                                   bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white
                                   shadow-sm transition hover:bg-blue-700
                                   focus:outline-none focus:ring-2 focus:ring-blue-500
                                   focus:ring-offset-2">
                            Continue Exam
                        </a>
                    @else
                        <form method="POST" action="{{ route('student.exams.start', $exam) }}" class="mt-5"
                            onsubmit="return confirm('Start the exam now? The timer begins immediately.')">
                            @csrf

                            <button type="submit"
                                class="inline-flex w-full items-center justify-center rounded-lg
                                       bg-indigo-600 px-4 py-2.5 text-sm font-semibold
                                       text-white shadow-sm transition hover:bg-indigo-700
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                                       focus:ring-offset-2">
                                Start Exam
                            </button>
                        </form>
                    @endif
                @elseif (now()->lt($exam->start_at))
                    <div class="mt-5 rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <p class="text-sm font-medium text-blue-700">
                            This exam has not opened yet.
                        </p>

                        <p class="mt-1 text-xs text-blue-600">
                            It opens on {{ $exam->start_at->format('d M Y, h:i A') }}.
                        </p>
                    </div>
                @elseif (now()->gt($exam->end_at))
                    <div class="mt-5 rounded-lg border border-gray-200 bg-gray-100 p-4">
                        <p class="text-sm font-medium text-gray-600">
                            This exam is closed.
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            It closed on {{ $exam->end_at->format('d M Y, h:i A') }}.
                        </p>
                    </div>
                @elseif ($attempts->count() >= $exam->maximum_attempts)
                    <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-medium text-amber-800">
                            You have used all available attempts.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Back button --}}
        <div class="mt-6">
            <a href="{{ route('student.exams.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300
                       bg-white px-4 py-2 text-sm font-semibold text-gray-700
                       shadow-sm transition hover:bg-gray-50">
                Back to My Exams
            </a>
        </div>
    </div>
@endsection
