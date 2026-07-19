@extends('layouts.app')
@section('title', 'Lecturer Dashboard') @section('heading', 'Lecturer Dashboard')
@section('content')
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach (['students' => 'Students', 'classes' => 'Classes', 'subjects' => 'Subjects', 'questions' => 'Questions', 'exams' => 'Exams'] as $key => $label)
            <div class="card">
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-black text-indigo-700">{{ $stats[$key] }}</p>
            </div>
        @endforeach
    </div>
    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold">Upcoming and active exams</h2><a class="text-sm text-indigo-600"
                    href="{{ route('lecturer.exams.index') }}">View all</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($upcoming as $exam)
                    <a href="{{ route('lecturer.exams.show', $exam) }}"
                        class="block rounded-lg border p-4 hover:bg-slate-50">
                        <div class="flex justify-between">
                            <div>
                                <p class="font-semibold">{{ $exam->title }}</p>
                                <p class="text-sm text-slate-500">{{ $exam->subject->name }}</p>
                            </div><span
                                class="badge {{ $exam->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($exam->status) }}</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $exam->start_at->format('d M Y, h:i A') }} –
                            {{ $exam->end_at->format('d M Y, h:i A') }}</p>
                </a>@empty<p class="text-sm text-slate-500">No upcoming exams.</p>
                @endforelse
            </div>
        </div>
        <div class="card">
            <h2 class="text-lg font-bold">Awaiting open-text marking</h2>
            <div class="mt-4 space-y-3">
                @forelse($pendingMarking as $attempt)
                    <a href="{{ route('lecturer.attempts.show', $attempt) }}"
                        class="flex items-center justify-between rounded-lg border p-4 hover:bg-slate-50">
                        <div>
                            <p class="font-semibold">{{ $attempt->student->name }}</p>
                            <p class="text-sm text-slate-500">{{ $attempt->exam->title }}</p>
                        </div><span class="badge bg-amber-100 text-amber-800">Pending</span>
                </a>@empty<p class="text-sm text-slate-500">No marking is pending.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
