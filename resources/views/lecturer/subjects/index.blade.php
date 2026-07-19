@extends('layouts.app')

@section('title', 'Subjects')
@section('heading', 'Subject Management')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Success message --}}
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header action --}}
        <div class="mb-6 flex items-center justify-end">
            <a href="{{ route('lecturer.subjects.create') }}"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5
                       text-sm font-semibold text-white shadow-sm transition
                       hover:bg-indigo-700 focus:outline-none focus:ring-2
                       focus:ring-indigo-500 focus:ring-offset-2">
                Create Subject
            </a>
        </div>

        {{-- Subjects table --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-5 py-3 text-left text-xs font-semibold uppercase
                                       tracking-wider text-gray-600">
                                Subject
                            </th>

                            <th scope="col"
                                class="px-5 py-3 text-left text-xs font-semibold uppercase
                                       tracking-wider text-gray-600">
                                Classes
                            </th>

                            <th scope="col"
                                class="px-5 py-3 text-left text-xs font-semibold uppercase
                                       tracking-wider text-gray-600">
                                Questions
                            </th>

                            <th scope="col"
                                class="px-5 py-3 text-left text-xs font-semibold uppercase
                                       tracking-wider text-gray-600">
                                Status
                            </th>

                            <th scope="col"
                                class="px-5 py-3 text-right text-xs font-semibold uppercase
                                       tracking-wider text-gray-600">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($subjects as $subject)
                            <tr class="transition hover:bg-gray-50">

                                {{-- Subject --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $subject->name }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $subject->code }}
                                    </div>
                                </td>

                                {{-- Classes count --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                    {{ $subject->classes_count }}
                                </td>

                                {{-- Questions count --}}
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-700">
                                    {{ $subject->questions_count }}
                                </td>

                                {{-- Status --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($subject->is_active)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100
                                                   px-2.5 py-1 text-xs font-semibold text-green-700">
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-100
                                                   px-2.5 py-1 text-xs font-semibold text-gray-600">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">

                                        <a href="{{ route('lecturer.subjects.edit', $subject) }}"
                                            class="inline-flex items-center rounded-lg border border-gray-300
                                                   bg-white px-3 py-2 text-sm font-semibold text-gray-700
                                                   shadow-sm transition hover:bg-gray-50
                                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('lecturer.subjects.destroy', $subject) }}"
                                            onsubmit="return confirm('Are you sure you want to delete this subject?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="inline-flex items-center rounded-lg bg-red-600
                                                       px-3 py-2 text-sm font-semibold text-white
                                                       shadow-sm transition hover:bg-red-700
                                                       focus:outline-none focus:ring-2
                                                       focus:ring-red-500 focus:ring-offset-2">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center">
                                    <div class="text-sm font-medium text-gray-600">
                                        No subjects created.
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        Create your first subject to get started.
                                    </div>

                                    <a href="{{ route('lecturer.subjects.create') }}"
                                        class="mt-4 inline-flex items-center rounded-lg bg-indigo-600
                                               px-4 py-2 text-sm font-semibold text-white shadow-sm
                                               transition hover:bg-indigo-700">
                                        Create Subject
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($subjects->hasPages())
            <div class="mt-6">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>
@endsection
