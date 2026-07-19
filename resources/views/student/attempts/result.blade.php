@extends('layouts.app')

@section('title', 'Exam Result')
@section('heading', 'Exam Result')

@section('content')
    @php
        $statusClass = match ($attempt->result_status) {
            'passed' => 'bg-green-100 text-green-700',
            'failed' => 'bg-red-100 text-red-700',
            default => 'bg-amber-100 text-amber-800',
        };

        $statusLabel =
            $attempt->result_status === 'pending' ? 'Awaiting Manual Marking' : ucfirst($attempt->result_status);
    @endphp

    <div class="mx-auto max-w-4xl">
        <div class="card text-center">
            <span class="badge {{ $statusClass }}">
                {{ $statusLabel }}
            </span>

            <h2 class="mt-4 text-2xl font-black">
                {{ $attempt->exam->title }}
            </h2>

            <p class="mt-1 text-slate-500">
                {{ $attempt->exam->subject->name }}
                · Attempt #{{ $attempt->attempt_number }}
            </p>

            @if ($attempt->exam->show_result)
                <div class="mt-7 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl bg-slate-50 p-5">
                        <p class="text-xs uppercase text-slate-500">
                            Score
                        </p>

                        <p class="mt-1 text-3xl font-black text-indigo-700">
                            {{ number_format($attempt->total_score, 2) }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-5">
                        <p class="text-xs uppercase text-slate-500">
                            Percentage
                        </p>

                        <p class="mt-1 text-3xl font-black">
                            {{ number_format($attempt->percentage, 2) }}%
                        </p>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-5">
                        <p class="text-xs uppercase text-slate-500">
                            Submitted
                        </p>

                        <p class="mt-2 font-bold">
                            {{ $attempt->submitted_at?->format('d M Y h:i A') }}
                        </p>
                    </div>
                </div>
            @else
                <p class="mt-6 rounded bg-slate-50 p-4 text-slate-600">
                    The lecturer has not enabled score viewing for this exam.
                </p>
            @endif
        </div>

        @if ($attempt->exam->show_correct_answers)
            <div class="mt-6 space-y-4">
                @foreach ($attempt->exam->questions as $index => $question)
                    @php
                        $answer = $attempt->answers->firstWhere('question_id', $question->id);
                    @endphp

                    <div class="card">
                        <div class="flex justify-between gap-4">
                            <h3 class="font-bold">
                                {{ $index + 1 }}.
                                {{ $question->question_text }}
                            </h3>

                            <span class="badge bg-slate-100 text-slate-700">
                                {{ number_format($answer?->marks_awarded ?? 0, 2) }}
                                /
                                {{ number_format($question->pivot->marks, 2) }}
                            </span>
                        </div>

                        @if ($question->isMultipleChoice())
                            <p class="mt-3 text-sm">
                                <strong>Your answer:</strong>

                                {{ $answer?->selectedOption?->option_text ?: 'No answer' }}
                            </p>

                            <p class="mt-1 text-sm">
                                <strong>Correct answer:</strong>

                                {{ $question->options->firstWhere('is_correct', true)?->option_text ?? 'Not available' }}
                            </p>
                        @else
                            <div class="mt-3 whitespace-pre-line rounded bg-slate-50 p-4">
                                {{ $answer?->answer_text ?: 'No answer' }}
                            </div>

                            @if ($answer?->lecturer_feedback)
                                <p class="mt-3 text-sm">
                                    <strong>Lecturer feedback:</strong>

                                    {{ $answer->lecturer_feedback }}
                                </p>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('student.exams.index') }}" class="btn-primary">
                Back to Exams
            </a>
        </div>
    </div>
@endsection
