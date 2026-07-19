@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('heading', 'Student Dashboard')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="grid gap-6 lg:grid-cols-2">

            {{-- Available and upcoming exams --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-gray-900">
                        Available and upcoming exams
                    </h2>

                    <a href="{{ route('student.exams.index') }}"
                        class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">
                        View all
                    </a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($available as $exam)
                        <a href="{{ route('student.exams.show', $exam) }}"
                            class="block rounded-lg border border-gray-200 p-4 transition
                                   hover:border-indigo-300 hover:bg-indigo-50">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900">
                                        {{ $exam->title }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $exam->subject->name }}
                                    </p>
                                </div>

                                <div class="shrink-0">
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

                            <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                <span>
                                    {{ $exam->start_at->format('d M Y, h:i A') }}
                                </span>

                                <span aria-hidden="true">•</span>

                                <span>
                                    {{ $exam->duration_minutes }} minutes
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-gray-600">
                                No exams assigned to your classes.
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Available and upcoming exams will appear here.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent attempts --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900">
                    Recent attempts
                </h2>

                <div class="mt-5 space-y-3">
                    @forelse ($attempts as $attempt)
                        @php
                            $isInProgress = $attempt->status === 'in_progress';

                            $attemptUrl = $isInProgress
                                ? route('student.attempts.show', $attempt)
                                : route('student.attempts.result', $attempt);

                            if ($isInProgress) {
                                $statusText = 'Continue';
                                $statusClass = 'bg-blue-100 text-blue-700';
                            } elseif ($attempt->result_status === 'passed') {
                                $statusText = 'Passed';
                                $statusClass = 'bg-green-100 text-green-700';
                            } elseif ($attempt->result_status === 'failed') {
                                $statusText = 'Failed';
                                $statusClass = 'bg-red-100 text-red-700';
                            } else {
                                $statusText = 'Pending';
                                $statusClass = 'bg-amber-100 text-amber-800';
                            }
                        @endphp

                        <a href="{{ $attemptUrl }}"
                            class="flex items-center justify-between gap-4 rounded-lg
                                   border border-gray-200 p-4 transition
                                   hover:border-indigo-300 hover:bg-indigo-50">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-900">
                                    {{ $attempt->exam->title }}
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $attempt->exam->subject->name }}
                                    <span aria-hidden="true">•</span>
                                    Attempt #{{ $attempt->attempt_number }}
                                </p>
                            </div>

                            <span
                                class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1
                                       text-xs font-semibold {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-4 py-8 text-center">
                            <p class="text-sm font-medium text-gray-600">
                                You have not attempted an exam yet.
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Your recent exam attempts will appear here.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection
