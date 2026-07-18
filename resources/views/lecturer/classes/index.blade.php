@extends('layouts.app')
@section('title', 'Classes')
@section('heading', 'Class Management')
@section('content')<div class="mb-5 flex justify-end">
        <a class="btn-primary" href="{{ route('lecturer.classes.create') }}">Create Class</a>
    </div>
    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <table class="min-w-full divide-y">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Class</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Academic Year</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Students</th>
                    <th class="px-5 py-3 text-left text-xs font-bold uppercase">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-bold uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($classes as $class)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="font-semibold">{{ $class->name }}</div>
                            <div class="text-xs text-slate-500">{{ $class->code }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm">{{ $class->academic_year ?: '—' }}</td>
                        <td class="px-5 py-4 text-sm">{{ $class->students_count }}</td>
                        <td class="px-5 py-4"><span
                                class="badge {{ $class->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">{{ $class->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2"><a class="btn-secondary"
                                    href="{{ route('lecturer.classes.edit', $class) }}">Edit</a>
                                <form method="POST" action="{{ route('lecturer.classes.destroy', $class) }}"
                                    onsubmit="return confirm('Delete this class?')">@csrf @method('DELETE')<button
                                        class="btn-danger">Delete</button></form>
                            </div>
                        </td>
                </tr>@empty<tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-500">No classes created.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $classes->links() }}</div>
@endsection
