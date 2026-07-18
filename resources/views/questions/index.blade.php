@extends('layouts.app')
@section('title', 'Question Bank')
@section('heading', 'Question Bank')
@section('content')<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex gap-2"><select class="rounded-lg border-slate-300" name="subject_id">
                <option value="">All subjects</option>
                @foreach ($subjects as $s)
                    <option value="{{ $s->id }}" @selected(request('subject_id') == $s->id)>{{ $s->code }} —
                        {{ $s->name }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Filter</button>
        </form><a class="btn-primary" href="{{ route('lecturer.questions.create') }}">Create Question</a>
    </div>
    <div class="space-y-4">
        @forelse($questions as $question)
            <div class="card">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="max-w-3xl">
                        <div class="mb-2 flex gap-2"><span
                                class="badge bg-indigo-100 text-indigo-700">{{ $question->subject->code }}</span><span
                                class="badge bg-slate-100 text-slate-700">{{ $question->question_type === 'multiple_choice' ? 'Multiple Choice' : 'Open Text' }}</span><span
                                class="badge bg-emerald-100 text-emerald-700">{{ number_format($question->marks, 2) }}
                                marks</span></div>
                        <p class="font-semibold">{{ $question->question_text }}</p>
                    </div>
                    <div class="flex gap-2"><a class="btn-secondary"
                            href="{{ route('lecturer.questions.edit', $question) }}">Edit</a>
                        <form method="POST" action="{{ route('lecturer.questions.destroy', $question) }}"
                            onsubmit="return confirm('Delete this question?')">@csrf @method('DELETE')<button
                                class="btn-danger">Delete</button></form>
                    </div>
                </div>
        </div>@empty<div class="card text-center text-slate-500">No questions found.</div>
        @endforelse
    </div>
    <div class="mt-5">{{ $questions->links() }}</div>
@endsection
