@extends('layouts.app')
@section('title', 'My Exams')
@section('heading', 'My Exams')
@section('content')<div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse($exams as $exam)
            <div class="card flex flex-col">
                <div class="flex justify-between gap-3"><span
                        class="badge bg-indigo-100 text-indigo-700">{{ $exam->subject->code }}</span>
                    @if (now()->between($exam->start_at, $exam->end_at))
                        <span class="badge bg-green-100 text-green-700">Open</span>
                    @elseif(now()->lt($exam->start_at))
                    <span class="badge bg-blue-100 text-blue-700">Upcoming</span>@else<span
                            class="badge bg-slate-100 text-slate-600">Closed</span>
                    @endif
                </div>
                <h2 class="mt-4 text-lg font-bold">{{ $exam->title }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $exam->classes->pluck('name')->join(', ') }}</p>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt>Starts</dt>
                        <dd class="font-semibold">{{ $exam->start_at->format('d M, h:i A') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Duration</dt>
                        <dd class="font-semibold">{{ $exam->duration_minutes }} min</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>Attempts</dt>
                        <dd class="font-semibold">{{ $exam->maximum_attempts }}</dd>
                    </div>
                </dl><a class="btn-primary mt-5 justify-center" href="{{ route('student.exams.show', $exam) }}">View
                    Exam</a>
        </div>@empty<div class="card text-center text-slate-500 md:col-span-2 lg:col-span-3">No exams are assigned to
                your classes.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $exams->links() }}</div>
@endsection
