@extends('layouts.app') @section('title', $class->exists ? 'Edit Class' : 'Create Class') @section('heading', $class->exists ?
'Edit Class' : 'Create Class')
@section('content')<form method="POST"
        action="{{ $class->exists ? route('lecturer.classes.update', $class) : route('lecturer.classes.store') }}"
        class="space-y-6">
        @csrf @if ($class->exists)
            @method('PUT')
        @endif
        <div class="card grid gap-5 md:grid-cols-2">
            <div><label class="label">Class name</label><input class="form-input" name="name"
                    value="{{ old('name', $class->name) }}" required></div>
            <div><label class="label">Class code</label><input class="form-input" name="code"
                    value="{{ old('code', $class->code) }}" required></div>
            <div><label class="label">Academic year</label><input class="form-input" name="academic_year"
                    value="{{ old('academic_year', $class->academic_year) }}" placeholder="2026/2027"></div>
            <div class="flex items-end"><input type="hidden" name="is_active" value="0"><label
                    class="flex items-center gap-2"><input class="rounded" type="checkbox" name="is_active" value="1"
                        @checked(old('is_active', $class->exists ? $class->is_active : true))> Active class</label></div>
            <div class="md:col-span-2"><label class="label">Description</label>
                <textarea class="form-input" name="description" rows="3">{{ old('description', $class->description) }}</textarea>
            </div>
        </div>
        <div class="card">
            <h2 class="font-bold">Assign students</h2>
            <p class="mt-1 text-sm text-slate-500">Students may belong to more than one class.</p>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@php($selected = collect(old('student_ids', $class->exists ? $class->students()->pluck('users.id')->all() : [])))@forelse($students as $student)
                    <label class="flex items-center gap-3 rounded-lg border p-3"><input class="rounded" type="checkbox"
                            name="student_ids[]" value="{{ $student->id }}" @checked($selected->contains($student->id))><span><span
                                class="block text-sm font-semibold">{{ $student->name }}</span><span
                                class="text-xs text-slate-500">{{ $student->email }}</span></span></label>@empty<p
                            class="text-sm text-slate-500">No student accounts exist yet.</p>
                    @endforelse
                </div>
            </div>
            <div class="flex gap-3"><button class="btn-primary">Save Class</button><a class="btn-secondary"
                    href="{{ route('lecturer.classes.index') }}">Cancel</a></div>
    </form>@endsection
