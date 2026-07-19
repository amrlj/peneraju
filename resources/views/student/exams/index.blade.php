@extends('layouts.app')

@section('title', 'My Exams')
@section('heading', 'My Exams')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($exams as $exam)
                <div
                    class="flex flex-col rounded-xl border border-gray-200
                           bg-white p-6 shadow-sm transition
                           hover:-translate-y-0.5 hover:shadow-md">
                    {{-- Subject and exam status --}}
                    <div class="flex items-start justify-between gap-3">
                        <span
                            class="inline-flex items-center rounded-full bg-indigo-100
                                   px-2.5 py-1 text-xs font-semibold text-indigo-700">
                            {{ $exam->subject->code }}
                        </span>

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

                    {{-- Exam title --}}
                    <h2 class="mt-4 text-lg font-bold text-gray-900">
                        {{ $exam->title }}
                    </h2>

                    {{-- Assigned classes --}}
                    <p class="mt-2 text-sm text-gray-500">
                        @if ($exam->classes->isNotEmpty())
                            {{ $exam->classes->pluck('name')->join(', ') }}
                        @else
                            No class assigned
                        @endif
                    </p>

                    {{-- Exam details --}}
                    <dl class="mt-5 space-y-3 border-t border-gray-100 pt-4 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-gray-500">
                                Starts
                            </dt>

                            <dd class="text-right font-semibold text-gray-900">
                                {{ $exam->start_at->format('d M Y, h:i A') }}
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-gray-500">
                                Duration
                            </dt>

                            <dd class="font-semibold text-gray-900">
                                {{ $exam->duration_minutes }} minutes
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-gray-500">
                                Maximum attempts
                            </dt>

                            <dd class="font-semibold text-gray-900">
                                {{ $exam->maximum_attempts }}
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-gray-500">
                                Ends
                            </dt>

                            <dd class="text-right font-semibold text-gray-900">
                                {{ $exam->end_at->format('d M Y, h:i A') }}
                            </dd>
                        </div>
                    </dl>

                    {{-- View button --}}
                    <div class="mt-auto pt-6">
                        <a href="{{ route('student.exams.show', $exam) }}"
                            class="inline-flex w-full items-center justify-center rounded-lg
                                   bg-indigo-600 px-4 py-2.5 text-sm font-semibold
                                   text-white shadow-sm transition hover:bg-indigo-700
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500
                                   focus:ring-offset-2">
                            View Exam
                        </a>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-xl border border-dashed border-gray-300 bg-white
                           px-6 py-12 text-center md:col-span-2 lg:col-span-3">
                    <div class="text-sm font-semibold text-gray-700">
                        No exams are assigned to your classes.
                    </div>

                    <p class="mt-1 text-sm text-gray-500">
                        Exams assigned by your lecturer will appear here.
                    </p>
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
