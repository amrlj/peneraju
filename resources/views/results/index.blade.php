@extends('layouts.app')
@section('title', 'Results')
@section('heading', 'Results: ' . $exam->title)
@section('content')<div class="mb-5 flex justify-end">
        <a class="btn-secondary" href="{{ route('lecturer.exams.results.export', $exam) }}">Export CSV</a>
    </div>
    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <table class="min-w-full divide-y">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Student</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Attempt</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Submitted</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Score</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Result</th>
                    <th class="px-5 py-3 text-right text-xs font-bold uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($attempts as $attempt)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="font-semibold">{{ $attempt->student->name }}</div>
                            <div class="text-xs text-slate-500">{{ $attempt->student->email }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm">#{{ $attempt->attempt_number }} ·
                            {{ str_replace('_', ' ', ucfirst($attempt->status)) }}</td>
                        <td class="px-5 py-4 text-sm">{{ $attempt->submitted_at?->format('d M Y h:i A') ?: 'In progress' }}
                        </td>
                        <td class="px-5 py-4 text-sm">{{ number_format($attempt->total_score, 2) }}
                            ({{ number_format($attempt->percentage, 2) }}%)
                        </td>
                        <td class="px-5 py-4"><span
                                class="badge {{ $attempt->result_status === 'passed' ? 'bg-green-100 text-green-700' : ($attempt->result_status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800') }}">{{ ucfirst($attempt->result_status) }}</span>
                        </td>
                        <td class="px-5 py-4 text-right"><a class="btn-secondary"
                                href="{{ route('lecturer.attempts.show', $attempt) }}">Review / Mark</a></td>
                </tr>@empty<tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-500">No attempts submitted.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $attempts->links() }}</div>
@endsection
