@extends('layouts.app')

@section('title', $subject->exists ? 'Edit Subject' : 'Create Subject')
@section('heading', $subject->exists ? 'Edit Subject' : 'Create Subject')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        <form method="POST"
            action="{{ $subject->exists ? route('lecturer.subjects.update', $subject) : route('lecturer.subjects.store') }}"
            class="space-y-6">
            @csrf

            @if ($subject->exists)
                @method('PUT')
            @endif

            {{-- Subject information --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="grid gap-6 md:grid-cols-2">

                    {{-- Subject name --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                            Subject name
                        </label>

                        <input id="name" type="text" name="name" value="{{ old('name', $subject->name) }}" required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 shadow-sm
                                   focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">

                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Subject code --}}
                    <div>
                        <label for="code" class="mb-2 block text-sm font-medium text-gray-700">
                            Subject code
                        </label>

                        <input id="code" type="text" name="code" value="{{ old('code', $subject->code) }}"
                            required
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 shadow-sm
                                   focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">

                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="mb-2 block text-sm font-medium text-gray-700">
                            Description
                        </label>

                        <textarea id="description" name="description" rows="4"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 shadow-sm
                                   focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200">{{ old('description', $subject->description) }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active status --}}
                    <div class="md:col-span-2">
                        <input type="hidden" name="is_active" value="0">

                        <label class="inline-flex cursor-pointer items-center gap-3">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $subject->exists ? $subject->is_active : true))
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600
                                       focus:ring-indigo-500">

                            <span class="text-sm font-medium text-gray-700">
                                Active subject
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Assign classes --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">
                    Assign to classes
                </h2>

                @php
                    $selected = collect(
                        old('class_ids', $subject->exists ? $subject->classes()->pluck('classes.id')->all() : []),
                    );
                @endphp

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($classes as $class)
                        <label
                            class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200
                                   p-4 transition hover:border-indigo-400 hover:bg-indigo-50">
                            <input type="checkbox" name="class_ids[]" value="{{ $class->id }}"
                                @checked($selected->contains($class->id))
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600
                                       focus:ring-indigo-500">

                            <span>
                                <span class="block text-sm font-semibold text-gray-900">
                                    {{ $class->name }}
                                </span>

                                <span class="mt-1 block text-xs text-gray-500">
                                    {{ $class->code }}
                                </span>
                            </span>
                        </label>
                    @empty
                        <div class="rounded-lg bg-gray-50 p-4 sm:col-span-2 lg:col-span-3">
                            <p class="text-sm text-gray-500">
                                No classes are available. Please create a class first.
                            </p>
                        </div>
                    @endforelse
                </div>

                @error('class_ids')
                    <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit"
                    class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white
                           shadow-sm transition hover:bg-indigo-700 focus:outline-none
                           focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ $subject->exists ? 'Update Subject' : 'Save Subject' }}
                </button>

                <a href="{{ route('lecturer.subjects.index') }}"
                    class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold
                           text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
