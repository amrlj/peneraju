@extends('layouts.app') @section('title', 'Student Dashboard') @section('heading', 'Student Dashboard')
@section('content')<div class="grid gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold">Available and upcoming exams</h2><a class="text-sm text-indigo-600"
                    href="{{ route('student.exams.index') }}">View all</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($available as $exam)
                    <a class="block rounded-lg border p-4 hover:bg-slate-50" href="{{ route('student.exams.show', $exam) }}">
                        <div class="flex justify-between gap-3">
                            <div>
                                <p class="font-semibold">{{ $exam->title }}</p>
                                <p class="text-sm text-slate-500">{{ $exam->subject->name }}</p>
                            </div>
                            @if (now()->between($exam->start_at, $exam->end_at))
                                <span class="badge bg-green-100 text-green-700">Open</span>
                            @elseif(now()->lt($exam->start_at))
                                <span class="badge bg-blue-100 text-blue-700">Upcoming</span>
                            @endif
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $exam->start_at->format('d M Y h:i A') }} ·
                            {{ $exam->duration_minutes }} minutes</p>
                </a>@empty<p class="text-sm text-slate-500">No exams assigned to your classes.</p>
                @endforelse
            </div>
        </div>
        <div class="card">
            <h2 class="text-lg font-bold">Recent attempts</h2>
            <div class="mt-4 space-y-3">
                @forelse($attempts as $attempt)
                    <a class="flex items-center justify-between rounded-lg border p-4 hover:bg-slate-50"
                        href="{{ $attempt->status === 'in_progress' ? route('student.attempts.show', $attempt) : route('student.attempts.result', $attempt) }}">
                        <div>
                            <p class="font-semibold">{{ $attempt->exam->title }}</p>
                            <p class="text-sm text-slate-500">{{ $attempt->exam->subject->name }} · Attempt
                                #{{ $attempt->attempt_number }}</p>
                        </div><span
                            class="badge {{ $attempt->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : ($attempt->result_status === 'passed' ? 'bg-green-100 text-green-700' : ($attempt->result_status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800')) }}">{{ $attempt->status === 'in_progress' ? 'Continue' : ucfirst($attempt->result_status) }}</span>
                </a>@empty<p class="text-sm text-slate-500">You have not attempted an exam yet.</p>
                @endforelse
            </div>
        </div>
</div>@endsection
