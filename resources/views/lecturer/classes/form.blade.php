@extends('layouts.app')

@section('title', $class->exists ? 'Edit Class' : 'Create Class')
@section('heading', $class->exists ? 'Edit Class' : 'Create Class')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        <form method="POST"
            action="{{ $class->exists ? route('lecturer.classes.update', $class) : route('lecturer.classes.store') }}"
            class="space-y-6">
            @csrf

            @if ($class->exists)
                @method('PUT')
            @endif

            {{-- Class information --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="grid gap-6 md:grid-cols-2">

                    {{-- Class name --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                            Class name
                        </label>

                        <input id="name" type="text" name="name" value="{{ old('name', $class->name) }}" required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm focus:border-indigo-500
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200">

                        @error('name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Class code --}}
                    <div>
                        <label for="code" class="mb-2 block text-sm font-medium text-gray-700">
                            Class code
                        </label>

                        <input id="code" type="text" name="code" value="{{ old('code', $class->code) }}" required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm focus:border-indigo-500
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200">

                        @error('code')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Academic year --}}
                    <div>
                        <label for="academic_year" class="mb-2 block text-sm font-medium text-gray-700">
                            Academic year
                        </label>

                        <input id="academic_year" type="text" name="academic_year"
                            value="{{ old('academic_year', $class->academic_year) }}" placeholder="2026/2027"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm placeholder:text-gray-400
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-200">

                        @error('academic_year')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Active status --}}
                    <div class="flex items-end">
                        <div>
                            <input type="hidden" name="is_active" value="0">

                            <label class="inline-flex cursor-pointer items-center gap-3">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $class->exists ? $class->is_active : true))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600
                                           focus:ring-indigo-500">

                                <span class="text-sm font-medium text-gray-700">
                                    Active class
                                </span>
                            </label>

                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
                            Description
                        </label>

                        <textarea id="description" name="description" rows="4"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2
                                   text-gray-900 shadow-sm focus:border-indigo-500
                                   focus:outline-none focus:ring-2 focus:ring-indigo-200">{{ old('description', $class->description) }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Assign students --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        Assign students
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Students may belong to more than one class.
                    </p>
                </div>

                @php
                    $selectedStudents = collect(
                        old('student_ids', $class->exists ? $class->students()->pluck('users.id')->all() : []),
                    )->map(fn($id) => (int) $id);
                @endphp

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($students as $student)
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-lg
                                   border border-gray-200 p-4 transition
                                   hover:border-indigo-400 hover:bg-indigo-50">
                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                @checked($selectedStudents->contains((int) $student->id))
                                class="h-4 w-4 shrink-0 rounded border-gray-300
                                       text-indigo-600 focus:ring-indigo-500">

                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-gray-900">
                                    {{ $student->name }}
                                </span>

                                <span class="mt-1 block truncate text-xs text-gray-500">
                                    {{ $student->email }}
                                </span>
                            </span>
                        </label>
                    @empty
                        <div
                            class="rounded-lg border border-dashed border-gray-300
                                   bg-gray-50 px-4 py-8 text-center
                                   sm:col-span-2 lg:col-span-3">
                            <p class="text-sm font-medium text-gray-600">
                                No student accounts exist yet.
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                Student accounts will appear here after they are created.
                            </p>
                        </div>
                    @endforelse
                </div>

                @error('student_ids')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                @error('student_ids.*')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Form buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600
                           px-5 py-2.5 text-sm font-semibold text-white shadow-sm
                           transition hover:bg-indigo-700 focus:outline-none
                           focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ $class->exists ? 'Update Class' : 'Save Class' }}
                </button>

                <a href="{{ route('lecturer.classes.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border
                           border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold
                           text-gray-700 shadow-sm transition hover:bg-gray-50
                           focus:outline-none focus:ring-2 focus:ring-indigo-500
                           focus:ring-offset-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
