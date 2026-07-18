@extends('layouts.app') @section('title', 'Exams') @section('heading', 'Exam Management')
@section('content')<div class="mb-5 flex justify-end">
        <a class="btn-primary" href="{{ route('lecturer.exams.create') }}">Create Exam</a>
    </div>
    <div class="space-y-4">
        @forelse($exams as $exam)
            <div class="card">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap gap-2"><span
                                class="badge bg-indigo-100 text-indigo-700">{{ $exam->subject->code }}</span><span
                                class="badge {{ $exam->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($exam->status) }}</span>
                        </div>
                        <h2 class="mt-2 text-lg font-bold">{{ $exam->title }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $exam->classes->pluck('name')->join(', ') }}</p>
                        <p class="mt-2 text-sm">{{ $exam->start_at->format('d M Y h:i A') }} –
                            {{ $exam->end_at->format('d M Y h:i A') }} · {{ $exam->duration_minutes }} minutes ·
                            {{ $exam->attempts_count }} attempts</p>
                    </div>
                    <div class="flex flex-wrap gap-2"><a class="btn-secondary"
                            href="{{ route('lecturer.exams.show', $exam) }}">View</a><a class="btn-secondary"
                            href="{{ route('lecturer.exams.results', $exam) }}">Results</a>
                        @if (!$exam->attempts_count)
                            <a class="btn-secondary" href="{{ route('lecturer.exams.edit', $exam) }}">Edit</a>
                            @endif @if ($exam->status === 'draft')
                                <form method="POST" action="{{ route('lecturer.exams.publish', $exam) }}">@csrf<button
                                        class="btn-primary">Publish</button></form>
                                @endif @if (!$exam->attempts_count)
                                    <form method="POST" action="{{ route('lecturer.exams.destroy', $exam) }}"
                                        onsubmit="return confirm('Delete this exam?')">@csrf @method('DELETE')<button
                                            class="btn-danger">Delete</button></form>
                                @endif
                    </div>
                </div>
        </div>@empty<div class="card text-center text-slate-500">No exams created.</div>
        @endforelse
    </div>
<div class="mt-5">{{ $exams->links() }}</div>@endsection
