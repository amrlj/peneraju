@extends('layouts.app') @section('title', $exam->title) @section('heading', $exam->title)
@section('content')<div class="grid gap-6 lg:grid-cols-3">
        <div class="card lg:col-span-2">
            <div class="flex flex-wrap gap-2"><span
                    class="badge bg-indigo-100 text-indigo-700">{{ $exam->subject->name }}</span><span
                    class="badge {{ $exam->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700' }}">{{ ucfirst($exam->status) }}</span>
            </div>
            <p class="mt-4 whitespace-pre-line text-slate-600">{{ $exam->instructions ?: 'No special instructions.' }}</p>
            <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs uppercase text-slate-500">Availability</dt>
                    <dd class="font-semibold">{{ $exam->start_at->format('d M Y h:i A') }}<br>to
                        {{ $exam->end_at->format('d M Y h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-slate-500">Duration</dt>
                    <dd class="font-semibold">{{ $exam->duration_minutes }} minutes</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-slate-500">Passing mark</dt>
                    <dd class="font-semibold">{{ $exam->passing_percentage }}%</dd>
                </div>
                <div>
                    <dt class="text-xs uppercase text-slate-500">Total marks</dt>
                    <dd class="font-semibold">{{ number_format($exam->totalMarks(), 2) }}</dd>
                </div>
            </dl>
        </div>
        <div class="card">
            <h2 class="font-bold">Assigned classes</h2>
            <ul class="mt-3 space-y-2">
                @foreach ($exam->classes as $class)
                    <li class="rounded bg-slate-50 p-3 text-sm">{{ $class->name }} <span
                            class="text-slate-500">({{ $class->code }})</span></li>
                @endforeach
            </ul>
            <a class="btn-primary mt-5 w-full justify-center" href="{{ route('lecturer.exams.results', $exam) }}">View
                Results</a>
        </div>
    </div>
    <div class="card mt-6">
        <h2 class="text-lg font-bold">Questions</h2>
        <div class="mt-4 space-y-4">
            @foreach ($exam->questions as $i => $question)
                <div class="rounded-lg border p-4">
                    <div class="flex justify-between gap-4">
                        <p class="font-semibold">{{ $i + 1 }}. {{ $question->question_text }}</p><span
                            class="badge bg-emerald-100 text-emerald-700">{{ number_format($question->pivot->marks, 2) }}</span>
                    </div>
                    <p class="mt-2 text-xs uppercase text-slate-500">
                        {{ $question->question_type === 'multiple_choice' ? 'Multiple choice' : 'Open text' }}</p>
                    @if ($question->isMultipleChoice())
                        <ul class="mt-3 space-y-1 text-sm">
                            @foreach ($question->options as $option)
                                <li class="{{ $option->is_correct ? 'font-bold text-green-700' : '' }}">
                                    {{ $loop->iteration }}. {{ $option->option_text }} @if ($option->is_correct)
                                        ✓
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
</div>@endsection
