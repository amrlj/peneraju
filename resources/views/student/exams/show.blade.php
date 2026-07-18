@extends('layouts.app')
@section('title', $exam->title)
@section('heading', $exam->title)
@section('content')<div class="grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-2"><span class="badge bg-indigo-100 text-indigo-700">{{ $exam->subject->name }}</span>
            <h2 class="mt-4 text-lg font-bold">Instructions</h2>
            <p class="mt-2 whitespace-pre-line text-slate-600">
                {{ $exam->instructions ?: 'Answer every question and submit before the timer reaches zero.' }}</p>
            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs uppercase text-slate-500">Availability</p>
                    <p class="mt-1 font-semibold">{{ $exam->start_at->format('d M Y h:i A') }}<br>to
                        {{ $exam->end_at->format('d M Y h:i A') }}</p>
                </div>
                <div class="rounded-lg bg-slate-50 p-4">
                    <p class="text-xs uppercase text-slate-500">Time limit</p>
                    <p class="mt-1 font-semibold">{{ $exam->duration_minutes }} minutes</p>
                </div>
            </div>
        </div>
        <div class="card">
            <h2 class="font-bold">Attempts</h2>
            <p class="mt-1 text-sm text-slate-500">Used {{ $attempts->count() }} of {{ $exam->maximum_attempts }}</p>
            <div class="mt-4 space-y-2">
                @foreach ($attempts as $attempt)
                    <a class="block rounded-lg border p-3 text-sm"
                        href="{{ $attempt->status === 'in_progress' ? route('student.attempts.show', $attempt) : route('student.attempts.result', $attempt) }}">Attempt
                        #{{ $attempt->attempt_number }} — {{ str_replace('_', ' ', ucfirst($attempt->status)) }}</a>
                @endforeach
            </div>
            @if (now()->between($exam->start_at, $exam->end_at) && $attempts->count() < $exam->maximum_attempts)
                <form class="mt-5" method="POST" action="{{ route('student.exams.start', $exam) }}"
                    onsubmit="return confirm('Start the exam now? The timer begins immediately.')">@csrf<button
                        class="btn-primary w-full justify-center">Start Exam</button></form>
            @elseif(now()->lt($exam->start_at))
                <p class="mt-5 rounded bg-blue-50 p-3 text-sm text-blue-700">This exam has not opened yet.</p>
            @elseif(now()->gt($exam->end_at))
                <p class="mt-5 rounded bg-slate-100 p-3 text-sm text-slate-600">This exam is closed.</p>
            @endif
        </div>
    </div>
@endsection
