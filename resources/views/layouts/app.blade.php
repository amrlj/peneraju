<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Online Exam Portal'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">

    @auth
        <nav class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">

                <a href="{{ route('dashboard') }}" class="text-lg font-bold text-indigo-700">
                    Online Exam Portal
                </a>

                <div class="hidden items-center gap-5 md:flex">
                    @if (auth()->user()->isLecturer())
                        <a href="{{ route('lecturer.dashboard') }}" class="text-sm hover:text-indigo-700">
                            Dashboard
                        </a>

                        <a href="{{ route('lecturer.classes.index') }}" class="text-sm hover:text-indigo-700">
                            Classes
                        </a>

                        <a href="{{ route('lecturer.subjects.index') }}" class="text-sm hover:text-indigo-700">
                            Subjects
                        </a>

                        <a href="{{ route('lecturer.questions.index') }}" class="text-sm hover:text-indigo-700">
                            Questions
                        </a>

                        <a href="{{ route('lecturer.exams.index') }}" class="text-sm hover:text-indigo-700">
                            Exams
                        </a>
                    @else
                        <a href="{{ route('student.dashboard') }}" class="text-sm hover:text-indigo-700">
                            Dashboard
                        </a>

                        <a href="{{ route('student.exams.index') }}" class="text-sm hover:text-indigo-700">
                            Exams
                        </a>
                    @endif


                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    @hasSection('heading')
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold">
                    @yield('heading')
                </h1>
            </div>
        </header>
    @endif

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('status'))
            <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 text-blue-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                <p class="font-semibold">
                    Please correct the following:
                </p>

                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-10 border-t bg-white py-6 text-center text-sm text-slate-500">
        Laravel 11 Online Examination Portal
    </footer>

    @stack('scripts')
</body>

</html>
