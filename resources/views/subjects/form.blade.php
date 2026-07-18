@extends('layouts.app')
@section('title', $subject->exists ? 'Edit Subject' : 'Create Subject')
@section('heading', $subject->exists ? 'Edit Subject' : 'Create Subject')
@section('content')<form method="POST"
        action="{{ $subject->exists ? route('lecturer.subjects.update', $subject) : route('lecturer.subjects.store') }}"
        class="space-y-6">
        @csrf @if ($subject->exists)
            @method('PUT')
        @endif
        <div class="card grid gap-5 md:grid-cols-2">
            <div><label class="label">Subject name</label><input class="form-input" name="name"
                    value="{{ old('name', $subject->name) }}" required></div>
            <div><label class="label">Subject code</label><input class="form-input" name="code"
                    value="{{ old('code', $subject->code) }}" required></div>
            <div class="md:col-span-2"><label class="label">Description</label>
                <textarea class="form-input" name="description" rows="3">{{ old('description', $subject->description) }}</textarea>
            </div>
            <div><input type="hidden" name="is_active" value="0"><label class="flex items-center gap-2"><input
                        class="rounded" type="checkbox" name="is_active" value="1" @checked(old('is_active', $subject->exists ? $subject->is_active : true))> Active
                    subject</label></div>
        </div>
        <div class="card">
            <h2 class="font-bold">Assign to classes</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@php($selected = collect(old('class_ids', $subject->exists ? $subject->classes()->pluck('classes.id')->all() : [])))@forelse($classes as $class)
                    <label class="flex items-center gap-3 rounded-lg border p-3"><input class="rounded" type="checkbox"
                            name="class_ids[]" value="{{ $class->id }}" @checked($selected->contains($class->id))><span><span
                                class="block text-sm font-semibold">{{ $class->name }}</span><span
                                class="text-xs text-slate-500">{{ $class->code }}</span></span></label>@empty<p
                            class="text-sm text-slate-500">Create a class first.</p>
                    @endforelse
                </div>
            </div>
            <div class="flex gap-3"><button class="btn-primary">Save Subject</button><a class="btn-secondary"
                    href="{{ route('lecturer.subjects.index') }}">Cancel</a></div>
        </form>
    @endsection
