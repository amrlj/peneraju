@extends('layouts.app')
@section('title', 'Subjects')
@section('heading', 'Subject Management')
@section('content')<div class="mb-5 flex justify-end">
        <a class="btn-primary" href="{{ route('lecturer.subjects.create') }}">Create Subject</a>
    </div>
    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <table class="min-w-full divide-y">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Subject</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Classes</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Questions</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-bold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($subjects as $subject)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="font-semibold">{{ $subject->name }}</div>
                            <div class="text-xs text-slate-500">{{ $subject->code }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm">{{ $subject->classes_count }}</td>
                        <td class="px-5 py-4 text-sm">{{ $subject->questions_count }}</td>
                        <td class="px-5 py-4"><span
                                class="badge {{ $subject->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">{{ $subject->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2"><a class="btn-secondary"
                                    href="{{ route('lecturer.subjects.edit', $subject) }}">Edit</a>
                                <form method="POST" action="{{ route('lecturer.subjects.destroy', $subject) }}"
                                    onsubmit="return confirm('Delete this subject?')">@csrf @method('DELETE')<button
                                        class="btn-danger">Delete</button></form>
                            </div>
                        </td>
                </tr>@empty<tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-500">No subjects created.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $subjects->links() }}</div>
@endsection
