@extends('layouts.app')

@section('title', 'Taking ' . $attempt->exam->title)
@section('heading', $attempt->exam->title)

@section('content')
    <div x-data="examRunner({{ $attempt->expires_at->timestamp }})" x-init="start()">
        {{-- Exam timer header --}}
        <div
            class="sticky top-0 z-20 mb-6 flex items-center justify-between rounded-xl bg-slate-900 px-5 py-4 text-white shadow-lg">
            <div>
                <p class="text-xs uppercase text-slate-300">
                    Attempt #{{ $attempt->attempt_number }}
                </p>

                <p class="font-semibold">
                    Answers are saved automatically
                </p>
            </div>

            <div class="text-right">
                <p class="text-xs uppercase text-slate-300">
                    Time remaining
                </p>

                <p class="font-mono text-2xl font-black" x-text="display"></p>
            </div>
        </div>

        {{-- Hidden exam submission form --}}
        <form id="submit-exam-form" method="POST" action="{{ route('student.attempts.submit', $attempt) }}">
            @csrf
        </form>

        {{-- Questions --}}
        <div class="space-y-5">
            @foreach ($questions as $index => $question)
                @php
                    $answer = $attempt->answers->firstWhere('question_id', $question->id);

                    $options = $attempt->exam->randomize_options ? $question->options->shuffle() : $question->options;
                @endphp

                <section id="question-{{ $question->id }}" class="card">
                    <div class="flex items-start justify-between gap-4">
                        <h2 class="font-bold">
                            {{ $index + 1 }}.
                            {{ $question->question_text }}
                        </h2>

                        <span class="badge bg-emerald-100 text-emerald-700">
                            {{ number_format($question->pivot->marks, 2) }}
                            marks
                        </span>
                    </div>

                    @if ($question->isMultipleChoice())
                        <div class="mt-5 space-y-3">
                            @foreach ($options as $option)
                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-lg border p-4 hover:bg-slate-50">
                                    <input type="radio" name="question_{{ $question->id }}" value="{{ $option->id }}"
                                        class="mt-1" @checked($answer?->question_option_id == $option->id)
                                        @change="
                                            saveAnswer(
                                                {{ $question->id }},
                                                {
                                                    question_option_id: $event.target.value
                                                }
                                            )
                                        ">

                                    <span>
                                        {{ $option->option_text }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-5">
                            <textarea class="form-input" rows="7" placeholder="Type your answer here..."
                                @blur="
                                    saveAnswer(
                                        {{ $question->id }},
                                        {
                                            answer_text: $event.target.value
                                        }
                                    )
                                ">{{ $answer?->answer_text }}</textarea>
                        </div>
                    @endif

                    {{-- Save status --}}
                    <div id="saved-{{ $question->id }}" class="mt-3 text-right text-xs text-slate-500"></div>
                </section>
            @endforeach
        </div>

        {{-- Submit button --}}
        <div class="mt-6 flex justify-end">
            <button type="button" class="btn-primary px-8 py-3" :disabled="submitting"
                :class="{ 'cursor-not-allowed opacity-50': submitting }" @click="submitExam()">
                <span x-text="submitting ? 'Submitting...' : 'Submit Exam'"></span>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function examRunner(expiresAt) {
            return {
                display: '--:--',
                timer: null,
                submitting: false,

                start() {
                    const tick = () => {
                        const currentTime = Date.now() / 1000;

                        const seconds = Math.max(
                            0,
                            Math.floor(expiresAt - currentTime)
                        );

                        const minutesDisplay = String(
                            Math.floor(seconds / 60)
                        ).padStart(2, '0');

                        const secondsDisplay = String(
                            seconds % 60
                        ).padStart(2, '0');

                        this.display = `${minutesDisplay}:${secondsDisplay}`;

                        if (seconds <= 0) {
                            clearInterval(this.timer);
                            this.autoSubmit();
                        }
                    };

                    tick();

                    this.timer = setInterval(tick, 1000);
                },

                async saveAnswer(questionId, payload) {
                    const statusElement = document.getElementById(
                        `saved-${questionId}`
                    );

                    statusElement.textContent = 'Saving...';

                    try {
                        const response = await fetch(
                            @js(route('student.attempts.answers.save', $attempt)), {
                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document
                                        .querySelector('meta[name="csrf-token"]')
                                        .content
                                },

                                body: JSON.stringify({
                                    question_id: questionId,
                                    ...payload
                                })
                            }
                        );

                        if (!response.ok) {
                            throw new Error('Unable to save the answer.');
                        }

                        const result = await response.json();

                        statusElement.textContent =
                            `Saved at ${result.saved_at}`;
                    } catch (error) {
                        console.error(error);

                        statusElement.textContent =
                            'Could not save. Please try again.';
                    }
                },

                submitExam() {
                    if (this.submitting) {
                        return;
                    }

                    const confirmed = window.confirm(
                        'Submit your exam? You cannot change your answers afterward.'
                    );

                    if (!confirmed) {
                        return;
                    }

                    this.submitting = true;
                    clearInterval(this.timer);

                    document
                        .getElementById('submit-exam-form')
                        .submit();
                },

                autoSubmit() {
                    if (this.submitting) {
                        return;
                    }

                    this.submitting = true;

                    document
                        .getElementById('submit-exam-form')
                        .submit();
                }
            };
        }
    </script>
@endpush
