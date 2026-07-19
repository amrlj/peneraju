@extends('layouts.app')

@section('title', 'Results')
@section('heading', 'Results: ' . $exam->title)

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Page actions --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('lecturer.exams.show', $exam) }}"
                class="inline-flex items-center justify-center rounded-lg border
                       border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold
                       text-gray-700 shadow-sm transition hover:bg-gray-50
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       focus:ring-offset-2">
                Back to Exam
            </a>

            <a href="{{ route('lecturer.exams.results.export', $exam) }}"
                class="inline-flex items-center justify-center rounded-lg
                       bg-indigo-600 px-4 py-2.5 text-sm font-semibold
                       text-white shadow-sm transition hover:bg-indigo-700
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       focus:ring-offset-2">
                Export CSV
            </a>
        </div>

        {{-- Exam summary --}}
        <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Subject
                </p>

                <p class="mt-2 font-semibold text-gray-900">
                    {{ $exam->subject->name }}
                </p>

                <p class="mt-1 text-xs text-gray-500">
                    {{ $exam->subject->code }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Total attempts
                </p>

                <p class="mt-2 text-2xl font-bold text-indigo-700">
                    {{ $attempts->total() }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Passing percentage
                </p>

                <p class="mt-2 text-2xl font-bold text-gray-900">
                    {{ number_format($exam->passing_percentage, 2) }}%
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                    Exam status
                </p>

                <div class="mt-2">
                    @if (now()->between($exam->start_at, $exam->end_at))
                        <span
                            class="inline-flex items-center rounded-full bg-blue-100
                                   px-2.5 py-1 text-xs font-semibold text-blue-700">
                            Active
                        </span>
                    @elseif (now()->lt($exam->start_at))
                        <span
                            class="inline-flex items-center rounded-full bg-purple-100
                                   px-2.5 py-1 text-xs font-semibold text-purple-700">
                            Upcoming
                        </span>
                    @else
                        <span
                            class="inline-flex items-center rounded-full bg-gray-100
                                   px-2.5 py-1 text-xs font-semibold text-gray-600">
                            Ended
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Results table --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-600">
                                Student
                            </th>

                            <th scope="col"
                                class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-600">
                                Attempt
                            </th>

                            <th scope="col"
                                class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-600">
                                Submitted
                            </th>

                            <th scope="col"
                                class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-600">
                                Score
                            </th>

                            <th scope="col"
                                class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wider text-gray-600">
                                Result
                            </th>

                            <th scope="col"
                                class="px-5 py-3 text-right text-xs font-semibold
                                       uppercase tracking-wider text-gray-600">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($attempts as $attempt)
                            @php
                                $attemptStatus = ucfirst(str_replace('_', ' ', $attempt->status));

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
                                    $resultText = $attempt->result_status
                                        ? ucfirst(str_replace('_', ' ', $attempt->result_status))
                                        : 'Pending';

                                    $resultClass = 'bg-amber-100 text-amber-800';
                                }
                            @endphp

                            <tr class="transition hover:bg-gray-50">
                                {{-- Student --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $attempt->student->name }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $attempt->student->email }}
                                    </div>
                                </td>

                                {{-- Attempt --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        Attempt #{{ $attempt->attempt_number }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $attemptStatus }}
                                    </div>
                                </td>

                                {{-- Submitted --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                    @if ($attempt->submitted_at)
                                        <div>
                                            {{ $attempt->submitted_at->format('d M Y') }}
                                        </div>

                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ $attempt->submitted_at->format('h:i A') }}
                                        </div>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-100
                                                   px-2.5 py-1 text-xs font-semibold text-blue-700">
                                            In progress
                                        </span>
                                    @endif
                                </td>

                                {{-- Score --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($attempt->status === 'in_progress')
                                        <span class="text-sm text-gray-500">
                                            Not available
                                        </span>
                                    @else
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ number_format($attempt->total_score ?? 0, 2) }}
                                        </div>

                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ number_format($attempt->percentage ?? 0, 2) }}%
                                        </div>
                                    @endif
                                </td>

                                {{-- Result --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1
                                               text-xs font-semibold {{ $resultClass }}">
                                        {{ $resultText }}
                                    </span>
                                </td>

                                {{-- Action --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a href="{{ route('lecturer.attempts.show', $attempt) }}"
                                        class="inline-flex items-center justify-center rounded-lg
                                               border border-gray-300 bg-white px-3 py-2
                                               text-sm font-semibold text-gray-700 shadow-sm
                                               transition hover:bg-gray-50 focus:outline-none
                                               focus:ring-2 focus:ring-indigo-500
                                               focus:ring-offset-2">
                                        {{ $attempt->status === 'in_progress' ? 'View Attempt' : 'Review / Mark' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <p class="text-sm font-semibold text-gray-700">
                                        No attempts submitted.
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Student attempts and results will appear here.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($attempts->hasPages())
            <div class="mt-6">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>
@endsection
