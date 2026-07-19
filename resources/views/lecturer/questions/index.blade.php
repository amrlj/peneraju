@extends('layouts.app')

@section('title', 'Question Bank')
@section('heading', 'Question Bank')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Success message --}}
        @if (session('success'))
            <div
                class="mb-6 rounded-lg border border-green-200 bg-green-50
                       px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error message --}}
        @if (session('error'))
            <div
                class="mb-6 rounded-lg border border-red-200 bg-red-50
                       px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter and create button --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <form method="GET" action="{{ route('lecturer.questions.index') }}"
                class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div>
                    <label for="subject_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Filter by subject
                    </label>

                    <select id="subject_id" name="subject_id"
                        class="block w-full min-w-64 rounded-lg border border-gray-300
                               bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm
                               focus:border-indigo-500 focus:outline-none
                               focus:ring-2 focus:ring-indigo-200">
                        <option value="">
                            All subjects
                        </option>

                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>
                                {{ $subject->code }} — {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg
                               border border-gray-300 bg-white px-4 py-2.5
                               text-sm font-semibold text-gray-700 shadow-sm
                               transition hover:bg-gray-50 focus:outline-none
                               focus:ring-2 focus:ring-indigo-500
                               focus:ring-offset-2">
                        Filter
                    </button>

                    @if (request()->filled('subject_id'))
                        <a href="{{ route('lecturer.questions.index') }}"
                            class="inline-flex items-center justify-center rounded-lg
                                   border border-gray-300 bg-white px-4 py-2.5
                                   text-sm font-semibold text-gray-700 shadow-sm
                                   transition hover:bg-gray-50">
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            <a href="{{ route('lecturer.questions.create') }}"
                class="inline-flex items-center justify-center rounded-lg
                       bg-indigo-600 px-4 py-2.5 text-sm font-semibold
                       text-white shadow-sm transition hover:bg-indigo-700
                       focus:outline-none focus:ring-2 focus:ring-indigo-500
                       focus:ring-offset-2">
                Create Question
            </a>
        </div>

        {{-- Question list --}}
        <div class="space-y-4">
            @forelse ($questions as $question)
                <div
                    class="rounded-xl border border-gray-200 bg-white
                           p-6 shadow-sm transition hover:shadow-md">
                    <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-start">

                        {{-- Question information --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">

                                {{-- Subject --}}
                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-indigo-100 px-2.5 py-1
                                           text-xs font-semibold text-indigo-700">
                                    {{ $question->subject->code }}
                                </span>

                                {{-- Question type --}}
                                @if ($question->question_type === 'multiple_choice')
                                    <span
                                        class="inline-flex items-center rounded-full
                                               bg-blue-100 px-2.5 py-1
                                               text-xs font-semibold text-blue-700">
                                        Multiple Choice
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full
                                               bg-purple-100 px-2.5 py-1
                                               text-xs font-semibold text-purple-700">
                                        Open Text
                                    </span>
                                @endif

                                {{-- Marks --}}
                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-emerald-100 px-2.5 py-1
                                           text-xs font-semibold text-emerald-700">
                                    {{ number_format($question->marks, 2) }}
                                    {{ $question->marks == 1 ? 'mark' : 'marks' }}
                                </span>

                                {{-- Status --}}
                                @if ($question->is_active)
                                    <span
                                        class="inline-flex items-center rounded-full
                                               bg-green-100 px-2.5 py-1
                                               text-xs font-semibold text-green-700">
                                        Active
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full
                                               bg-gray-100 px-2.5 py-1
                                               text-xs font-semibold text-gray-600">
                                        Inactive
                                    </span>
                                @endif
                            </div>

                            <p class="mt-4 whitespace-pre-line font-semibold leading-7 text-gray-900">
                                {{ $question->question_text }}
                            </p>

                            @if ($question->question_type === 'multiple_choice' && $question->relationLoaded('options'))
                                <p class="mt-3 text-xs text-gray-500">
                                    {{ $question->options->count() }}
                                    {{ $question->options->count() === 1 ? 'option' : 'options' }}
                                </p>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex shrink-0 flex-wrap items-center gap-2">
                            <a href="{{ route('lecturer.questions.edit', $question) }}"
                                class="inline-flex items-center justify-center rounded-lg
                                       border border-gray-300 bg-white px-3 py-2
                                       text-sm font-semibold text-gray-700 shadow-sm
                                       transition hover:bg-gray-50 focus:outline-none
                                       focus:ring-2 focus:ring-indigo-500
                                       focus:ring-offset-2">
                                Edit
                            </a>

                            <form method="POST" action="{{ route('lecturer.questions.destroy', $question) }}"
                                onsubmit="return confirm('Are you sure you want to delete this question?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg
                                           bg-red-600 px-3 py-2 text-sm font-semibold
                                           text-white shadow-sm transition hover:bg-red-700
                                           focus:outline-none focus:ring-2
                                           focus:ring-red-500 focus:ring-offset-2">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-xl border border-dashed border-gray-300
                           bg-white px-6 py-14 text-center">
                    <p class="text-sm font-semibold text-gray-700">
                        No questions found.
                    </p>

                    @if (request()->filled('subject_id'))
                        <p class="mt-1 text-sm text-gray-500">
                            There are no questions for the selected subject.
                        </p>

                        <a href="{{ route('lecturer.questions.index') }}"
                            class="mt-4 inline-flex items-center justify-center
                                   rounded-lg border border-gray-300 bg-white
                                   px-4 py-2.5 text-sm font-semibold text-gray-700
                                   shadow-sm transition hover:bg-gray-50">
                            View All Questions
                        </a>
                    @else
                        <p class="mt-1 text-sm text-gray-500">
                            Create your first multiple-choice or open-text question.
                        </p>

                        <a href="{{ route('lecturer.questions.create') }}"
                            class="mt-4 inline-flex items-center justify-center
                                   rounded-lg bg-indigo-600 px-4 py-2.5
                                   text-sm font-semibold text-white shadow-sm
                                   transition hover:bg-indigo-700">
                            Create Question
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($questions->hasPages())
            <div class="mt-6">
                {{ $questions->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
