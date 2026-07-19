@extends('layouts.app')

@section('title', 'Exams')
@section('heading', 'Exam Management')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Success message --}}
        @if (session('success'))
            <div
                class="mb-6 rounded-lg border border-green-200
                       bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error message --}}
        @if (session('error'))
            <div
                class="mb-6 rounded-lg border border-red-200
                       bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Create exam button --}}
        <div class="mb-6 flex justify-end">
            <a href="{{ route('lecturer.exams.create') }}"
                class="inline-flex items-center justify-center rounded-lg
                       bg-indigo-600 px-4 py-2.5 text-sm font-semibold
                       text-white shadow-sm transition hover:bg-indigo-700
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       focus:ring-offset-2">
                Create Exam
            </a>
        </div>

        {{-- Exam list --}}
        <div class="space-y-4">
            @forelse ($exams as $exam)
                <div
                    class="rounded-xl border border-gray-200
                           bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-start">

                        {{-- Exam information --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">

                                {{-- Subject --}}
                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-indigo-100 px-2.5 py-1
                                           text-xs font-semibold text-indigo-700">
                                    {{ $exam->subject->code }}
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

                                {{-- Exam availability --}}
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

                            {{-- Exam title --}}
                            <h2 class="mt-3 text-lg font-bold text-gray-900">
                                {{ $exam->title }}
                            </h2>

                            {{-- Assigned classes --}}
                            <div class="mt-2">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Assigned classes
                                </p>

                                <p class="mt-1 text-sm text-gray-600">
                                    @if ($exam->classes->isNotEmpty())
                                        {{ $exam->classes->pluck('name')->join(', ') }}
                                    @else
                                        No classes assigned
                                    @endif
                                </p>
                            </div>

                            {{-- Exam details --}}
                            <dl class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <dt class="text-xs font-semibold uppercase text-gray-500">
                                        Starts
                                    </dt>

                                    <dd class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $exam->start_at->format('d M Y, h:i A') }}
                                    </dd>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-3">
                                    <dt class="text-xs font-semibold uppercase text-gray-500">
                                        Ends
                                    </dt>

                                    <dd class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $exam->end_at->format('d M Y, h:i A') }}
                                    </dd>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-3">
                                    <dt class="text-xs font-semibold uppercase text-gray-500">
                                        Duration
                                    </dt>

                                    <dd class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $exam->duration_minutes }} minutes
                                    </dd>
                                </div>

                                <div class="rounded-lg bg-gray-50 p-3">
                                    <dt class="text-xs font-semibold uppercase text-gray-500">
                                        Attempts
                                    </dt>

                                    <dd class="mt-1 text-sm font-medium text-gray-900">
                                        {{ $exam->attempts_count }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Actions --}}
                        <div class="flex shrink-0 flex-wrap items-center gap-2 lg:justify-end">

                            {{-- View --}}
                            <a href="{{ route('lecturer.exams.show', $exam) }}"
                                class="inline-flex items-center justify-center rounded-lg
                                       border border-gray-300 bg-white px-3 py-2
                                       text-sm font-semibold text-gray-700 shadow-sm
                                       transition hover:bg-gray-50 focus:outline-none
                                       focus:ring-2 focus:ring-indigo-500">
                                View
                            </a>

                            {{-- Results --}}
                            <a href="{{ route('lecturer.exams.results', $exam) }}"
                                class="inline-flex items-center justify-center rounded-lg
                                       border border-gray-300 bg-white px-3 py-2
                                       text-sm font-semibold text-gray-700 shadow-sm
                                       transition hover:bg-gray-50 focus:outline-none
                                       focus:ring-2 focus:ring-indigo-500">
                                Results
                            </a>

                            {{-- Edit --}}
                            @if ($exam->attempts_count === 0)
                                <a href="{{ route('lecturer.exams.edit', $exam) }}"
                                    class="inline-flex items-center justify-center rounded-lg
                                           border border-gray-300 bg-white px-3 py-2
                                           text-sm font-semibold text-gray-700 shadow-sm
                                           transition hover:bg-gray-50 focus:outline-none
                                           focus:ring-2 focus:ring-indigo-500">
                                    Edit
                                </a>
                            @endif

                            {{-- Publish --}}
                            @if ($exam->status === 'draft')
                                <form method="POST" action="{{ route('lecturer.exams.publish', $exam) }}"
                                    onsubmit="return confirm('Publish this exam now?')">
                                    @csrf

                                    <button type="submit"
                                        class="inline-flex items-center justify-center rounded-lg
                                               bg-indigo-600 px-3 py-2 text-sm font-semibold
                                               text-white shadow-sm transition hover:bg-indigo-700
                                               focus:outline-none focus:ring-2
                                               focus:ring-indigo-500 focus:ring-offset-2">
                                        Publish
                                    </button>
                                </form>
                            @endif

                            {{-- Delete --}}
                            @if ($exam->attempts_count === 0)
                                <form method="POST" action="{{ route('lecturer.exams.destroy', $exam) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this exam?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="inline-flex items-center justify-center rounded-lg
                                               bg-red-600 px-3 py-2 text-sm font-semibold
                                               text-white shadow-sm transition hover:bg-red-700
                                               focus:outline-none focus:ring-2
                                               focus:ring-red-500 focus:ring-offset-2">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    {{-- Notice when editing is disabled --}}
                    @if ($exam->attempts_count > 0)
                        <div
                            class="mt-5 rounded-lg border border-amber-200
                                   bg-amber-50 px-4 py-3">
                            <p class="text-sm text-amber-800">
                                This exam cannot be edited or deleted because students
                                have already attempted it.
                            </p>
                        </div>
                    @endif
                </div>
            @empty
                <div
                    class="rounded-xl border border-dashed border-gray-300
                           bg-white px-6 py-14 text-center">
                    <p class="text-sm font-semibold text-gray-700">
                        No exams created.
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        Create an exam and assign it to your classes.
                    </p>

                    <a href="{{ route('lecturer.exams.create') }}"
                        class="mt-4 inline-flex items-center justify-center
                               rounded-lg bg-indigo-600 px-4 py-2.5
                               text-sm font-semibold text-white shadow-sm
                               transition hover:bg-indigo-700">
                        Create Exam
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($exams->hasPages())
            <div class="mt-6">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
@endsection
