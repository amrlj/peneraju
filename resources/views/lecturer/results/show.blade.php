@extends('layouts.app')
@section('title', 'Review Attempt')
@section('heading', 'Review Attempt: ' . $attempt->student->name)
@section('content')<div class="mb-6 grid gap-4 sm:grid-cols-4">
        <div class="card">
            <p class="text-xs uppercase text-slate-500">Exam</p>
            <p class="mt-1 font-bold">{{ $attempt->exam->title }}</p>
        </div>
        <div class="card">
            <p class="text-xs uppercase text-slate-500">Current score</p>
            <p class="mt-1 text-2xl font-black text-indigo-700">{{ number_format($attempt->total_score, 2) }}</p>
        </div>
        <div class="card">
            <p class="text-xs uppercase text-slate-500">Percentage</p>
            <p class="mt-1 text-2xl font-black">{{ number_format($attempt->percentage, 2) }}%</p>
        </div>
        <div class="card">
            <p class="text-xs uppercase text-slate-500">Result</p>
            <p class="mt-1 font-bold">{{ ucfirst($attempt->result_status) }}</p>
        </div>
    </div>
    <form method="POST" action="{{ route('lecturer.attempts.mark', $attempt) }}" class="space-y-5">@csrf @method('PUT')
        @foreach ($attempt->exam->questions as $i => $question)
            @php($answer = $attempt->answers->firstWhere('question_id', $question->id))<div class="card">
                <div class="flex justify-between gap-4">
                    <h2 class="font-bold">{{ $i + 1 }}. {{ $question->question_text }}</h2><span
                        class="badge bg-emerald-100 text-emerald-700">Max
                        {{ number_format($question->pivot->marks, 2) }}</span>
                </div>
                @if ($question->isMultipleChoice())
                    <div class="mt-4 rounded-lg bg-slate-50 p-4 text-sm">
                        <p><strong>Selected:</strong> {{ $answer?->selectedOption?->option_text ?: 'No answer' }}</p>
                        <p class="mt-1"><strong>Mark:</strong> {{ number_format($answer?->marks_awarded ?? 0, 2) }} ·
                            {{ $answer?->is_correct ? 'Correct' : 'Incorrect' }}</p>
                </div>@else<div class="mt-4 rounded-lg bg-slate-50 p-4 whitespace-pre-line">
                        {{ $answer?->answer_text ?: 'No answer submitted.' }}</div>
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <div><label class="label">Marks awarded</label><input class="form-input" type="number"
                                min="0" max="{{ $question->pivot->marks }}" step="0.01"
                                name="answers[{{ $question->id }}][marks_awarded]"
                                value="{{ old('answers.' . $question->id . '.marks_awarded', $answer?->marks_awarded ?? 0) }}"
                                required></div>
                        <div class="md:col-span-2"><label class="label">Feedback</label>
                            <textarea class="form-input" name="answers[{{ $question->id }}][lecturer_feedback]" rows="3">{{ old('answers.' . $question->id . '.lecturer_feedback', $answer?->lecturer_feedback) }}</textarea>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
        <div class="flex gap-3"><button class="btn-primary">Save Marks and Recalculate</button><a class="btn-secondary"
                href="{{ route('lecturer.exams.results', $attempt->exam) }}">Back</a></div>
    </form>
@endsection
