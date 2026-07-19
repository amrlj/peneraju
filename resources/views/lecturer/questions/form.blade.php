@extends('layouts.app')

@section('title', $question->exists ? 'Edit Question' : 'Create Question')
@section('heading', $question->exists ? 'Edit Question' : 'Create Question')

@section('content')
    @php
        $initialOptions = old(
            'options',
            $question->exists
                ? $question->options
                    ->map(
                        fn($option) => [
                            'option_text' => $option->option_text,
                            'is_correct' => (bool) $option->is_correct,
                        ],
                    )
                    ->values()
                    ->all()
                : [
                    ['option_text' => '', 'is_correct' => true],
                    ['option_text' => '', 'is_correct' => false],
                    ['option_text' => '', 'is_correct' => false],
                    ['option_text' => '', 'is_correct' => false],
                ],
        );
    @endphp

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST"
            action="{{ $question->exists ? route('lecturer.questions.update', $question) : route('lecturer.questions.store') }}"
            class="space-y-6"
            x-data='{
                type: @json(old('question_type', $question->question_type ?: 'multiple_choice')),

                options: @json($initialOptions),

                init() {
                    this.options = this.options.map((option) => ({
                        option_text: option.option_text ?? "",
                        is_correct:
                            option.is_correct === true ||
                            Number(option.is_correct) === 1
                    }));

                    const firstCorrect = this.options.findIndex(
                        (option) => option.is_correct
                    );

                    this.options.forEach((option, index) => {
                        option.is_correct = firstCorrect === -1
                            ? index === 0
                            : index === firstCorrect;
                    });
                },

                addOption() {
                    this.options.push({
                        option_text: "",
                        is_correct: false
                    });
                },

                setCorrect(selectedIndex) {
                    this.options.forEach((option, index) => {
                        option.is_correct = index === selectedIndex;
                    });
                },

                removeOption(index) {
                    const removedOptionWasCorrect =
                        this.options[index].is_correct;

                    this.options.splice(index, 1);

                    if (
                        removedOptionWasCorrect &&
                        this.options.length > 0
                    ) {
                        this.options[0].is_correct = true;
                    }
                }
            }'>
            @csrf

            @if ($question->exists)
                @method('PUT')
            @endif

            {{-- Question information --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">
                        Question information
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Select the subject, question type, and default marks.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-3">

                    {{-- Subject --}}
                    <div>
                        <label for="subject_id" class="mb-2 block text-sm font-medium text-gray-700">
                            Subject
                        </label>

                        <select id="subject_id" name="subject_id" required
                            class="block w-full rounded-lg border border-gray-300
                                   bg-white px-3 py-2 text-gray-900 shadow-sm
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-200">
                            <option value="">
                                Select subject
                            </option>

                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(old('subject_id', $question->subject_id) == $subject->id)>
                                    {{ $subject->code }} — {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('subject_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Question type --}}
                    <div>
                        <label for="question_type" class="mb-2 block text-sm font-medium text-gray-700">
                            Question type
                        </label>

                        <select id="question_type" name="question_type" x-model="type" required
                            class="block w-full rounded-lg border border-gray-300
                                   bg-white px-3 py-2 text-gray-900 shadow-sm
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-200">
                            <option value="multiple_choice">
                                Multiple choice
                            </option>

                            <option value="open_text">
                                Open text
                            </option>
                        </select>

                        @error('question_type')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Default marks --}}
                    <div>
                        <label for="marks" class="mb-2 block text-sm font-medium text-gray-700">
                            Default marks
                        </label>

                        <input id="marks" type="number" name="marks" min="0.5" step="0.5"
                            value="{{ old('marks', $question->marks ?: 1) }}" required
                            class="block w-full rounded-lg border border-gray-300
                                   px-3 py-2 text-gray-900 shadow-sm
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-200">

                        @error('marks')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Question text --}}
                    <div class="md:col-span-3">
                        <label for="question_text" class="mb-2 block text-sm font-medium text-gray-700">
                            Question
                        </label>

                        <textarea id="question_text" name="question_text" rows="5" required placeholder="Enter the question here..."
                            class="block w-full rounded-lg border border-gray-300
                                   px-3 py-2 text-gray-900 shadow-sm
                                   placeholder:text-gray-400
                                   focus:border-indigo-500 focus:outline-none
                                   focus:ring-2 focus:ring-indigo-200">{{ old('question_text', $question->question_text) }}</textarea>

                        @error('question_text')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Active status --}}
                    <div class="md:col-span-3">
                        <input type="hidden" name="is_active" value="0">

                        <label class="inline-flex cursor-pointer items-center gap-3">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $question->exists ? $question->is_active : true))
                                class="h-4 w-4 rounded border-gray-300
                                       text-indigo-600 focus:ring-indigo-500">

                            <span class="text-sm font-medium text-gray-700">
                                Active question
                            </span>
                        </label>

                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Multiple-choice answer options --}}
            <div x-show="type === 'multiple_choice'" x-cloak
                class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            Answer options
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Select exactly one correct answer.
                        </p>
                    </div>

                    <button type="button" @click="addOption()"
                        class="inline-flex items-center justify-center rounded-lg
                               border border-gray-300 bg-white px-4 py-2
                               text-sm font-semibold text-gray-700 shadow-sm
                               transition hover:bg-gray-50 focus:outline-none
                               focus:ring-2 focus:ring-indigo-500
                               focus:ring-offset-2">
                        Add Option
                    </button>
                </div>

                <div class="mt-5 space-y-3">
                    <template x-for="(option, index) in options" :key="index">
                        <div class="rounded-lg border p-4 transition"
                            :class="option.is_correct ?
                                'border-green-300 bg-green-50' :
                                'border-gray-200 bg-white'">
                            <div class="flex items-start gap-3">

                                {{-- Correct answer selector --}}
                                <div class="pt-2">
                                    <input type="radio" name="_correct_option" :value="index"
                                        :checked="option.is_correct" @change="setCorrect(index)"
                                        :disabled="type !== 'multiple_choice'" :required="type === 'multiple_choice'"
                                        class="h-4 w-4 border-gray-300
                                               text-green-600 focus:ring-green-500">
                                </div>

                                {{-- Submitted correct value --}}
                                <input type="hidden" :name="`options[${index}][is_correct]`"
                                    :value="option.is_correct ? 1 : 0" :disabled="type !== 'multiple_choice'">

                                {{-- Option input --}}
                                <div class="min-w-0 flex-1">
                                    <label
                                        class="mb-1 block text-xs font-semibold
                                               uppercase tracking-wide text-gray-500"
                                        x-text="`Option ${index + 1}`"></label>

                                    <input type="text" :name="`options[${index}][option_text]`"
                                        x-model="option.option_text" :disabled="type !== 'multiple_choice'"
                                        :required="type === 'multiple_choice'" placeholder="Enter answer option"
                                        class="block w-full rounded-lg border
                                               border-gray-300 px-3 py-2
                                               text-gray-900 shadow-sm
                                               placeholder:text-gray-400
                                               focus:border-indigo-500
                                               focus:outline-none focus:ring-2
                                               focus:ring-indigo-200">

                                    <p x-show="option.is_correct" class="mt-1 text-xs font-medium text-green-700">
                                        Correct answer
                                    </p>
                                </div>

                                {{-- Remove button --}}
                                <button type="button" x-show="options.length > 2" @click="removeOption(index)"
                                    class="mt-7 shrink-0 text-sm font-semibold
                                           text-red-600 transition
                                           hover:text-red-800">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                @error('options')
                    <p class="mt-3 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

                @if ($errors->has('options.*.option_text'))
                    <p class="mt-3 text-sm text-red-600">
                        Please complete all answer options.
                    </p>
                @endif

                @if ($errors->has('options.*.is_correct'))
                    <p class="mt-3 text-sm text-red-600">
                        Please select one correct answer.
                    </p>
                @endif
            </div>

            {{-- Open-text information --}}
            <div x-show="type === 'open_text'" x-cloak class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <h2 class="text-sm font-bold text-blue-900">
                    Open-text question
                </h2>

                <p class="mt-1 text-sm text-blue-700">
                    Students will type their answers. The lecturer must review and
                    mark submitted answers manually.
                </p>
            </div>

            {{-- Form buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg
                           bg-indigo-600 px-5 py-2.5 text-sm font-semibold
                           text-white shadow-sm transition hover:bg-indigo-700
                           focus:outline-none focus:ring-2
                           focus:ring-indigo-500 focus:ring-offset-2">
                    {{ $question->exists ? 'Update Question' : 'Save Question' }}
                </button>

                <a href="{{ route('lecturer.questions.index') }}"
                    class="inline-flex items-center justify-center rounded-lg
                           border border-gray-300 bg-white px-5 py-2.5
                           text-sm font-semibold text-gray-700 shadow-sm
                           transition hover:bg-gray-50 focus:outline-none
                           focus:ring-2 focus:ring-indigo-500
                           focus:ring-offset-2">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
