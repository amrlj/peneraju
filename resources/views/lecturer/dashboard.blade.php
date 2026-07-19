@extends('layouts.app')

@section('title', 'Lecturer Dashboard')
@section('heading', 'Lecturer Dashboard')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Statistics --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ([
            'students' => 'Students',
            'classes' => 'Classes',
            'subjects' => 'Subjects',
            'questions' => 'Questions',
            'exams' => 'Exams',
        ] as $key => $label)
                <div
                    class="rounded-xl border border-gray-200 bg-white p-5
                           shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <p class="text-sm font-medium text-gray-500">
                        {{ $label }}
                    </p>

                    <p class="mt-2 text-3xl font-bold text-indigo-700">
                        {{ $stats[$key] ?? 0 }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Dashboard information --}}
        <div class="mt-8 grid gap-6 lg:grid-cols-2">

            {{-- Upcoming and active exams --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-gray-900">
                        Upcoming and active exams
                    </h2>

                    <a href="{{ route('lecturer.exams.index') }}"
                        class="shrink-0 text-sm font-semibold text-indigo-600
                               transition hover:text-indigo-800">
                        View all
                    </a>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($upcoming as $exam)
                        <a href="{{ route('lecturer.exams.show', $exam) }}"
                            class="block rounded-lg border border-gray-200 p-4
                                   transition hover:border-indigo-300 hover:bg-indigo-50">
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
                                </div>
                            </div>

                            <div
                                class="mt-3 flex flex-wrap items-center gap-x-2
                                       gap-y-1 text-xs text-gray-500">
                                <span>
                                    {{ $exam->start_at->format('d M Y, h:i A') }}
                                </span>

                                <span aria-hidden="true">–</span>

                                <span>
                                    {{ $exam->end_at->format('d M Y, h:i A') }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div
                            class="rounded-lg border border-dashed border-gray-300
                                   bg-gray-50 px-4 py-10 text-center">
                            <p class="text-sm font-medium text-gray-600">
                                No upcoming exams.
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Upcoming and active exams will appear here.
                            </p>

                            <a href="{{ route('lecturer.exams.create') }}"
                                class="mt-4 inline-flex items-center justify-center rounded-lg
                                       bg-indigo-600 px-4 py-2 text-sm font-semibold
                                       text-white shadow-sm transition hover:bg-indigo-700">
                                Create Exam
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pending marking --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-gray-900">
                        Awaiting open-text marking
                    </h2>

                    @if ($pendingMarking->isNotEmpty())
                        <span
                            class="inline-flex min-w-7 items-center justify-center
                                   rounded-full bg-amber-100 px-2.5 py-1
                                   text-xs font-semibold text-amber-800">
                            {{ $pendingMarking->count() }}
                        </span>
                    @endif
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($pendingMarking as $attempt)
                        <a href="{{ route('lecturer.attempts.show', $attempt) }}"
                            class="flex items-center justify-between gap-4
                                   rounded-lg border border-gray-200 p-4
                                   transition hover:border-indigo-300
                                   hover:bg-indigo-50">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-gray-900">
                                    {{ $attempt->student->name }}
                                </p>

                                <p class="mt-1 truncate text-sm text-gray-500">
                                    {{ $attempt->exam->title }}
                                </p>

                                @if ($attempt->attempt_number)
                                    <p class="mt-1 text-xs text-gray-400">
                                        Attempt #{{ $attempt->attempt_number }}
                                    </p>
                                @endif
                            </div>

                            <span
                                class="inline-flex shrink-0 items-center rounded-full
                                       bg-amber-100 px-2.5 py-1
                                       text-xs font-semibold text-amber-800">
                                Pending
                            </span>
                        </a>
                    @empty
                        <div
                            class="rounded-lg border border-dashed border-gray-300
                                   bg-gray-50 px-4 py-10 text-center">
                            <p class="text-sm font-medium text-gray-600">
                                No marking is pending.
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Submitted open-text answers will appear here.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
