<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <script>
        (function () {
            var t = localStorage.getItem('theme');
            if (t) document.documentElement.setAttribute('data-theme', t);
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Drawer for mobile sidebar --}}
    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle">

        <div class="drawer-content flex flex-col min-h-screen">
            {{-- Navbar --}}
            <nav class="navbar bg-base-100 border-b border-base-200 sticky top-0 z-30 gap-2 lg:gap-0">
                <div class="flex-none lg:hidden">
                    <label for="main-drawer" class="btn btn-ghost btn-square">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </label>
                </div>
                <div class="flex-1">
                    <span class="text-lg font-semibold">{{ $pageTitle ?? 'Dashboard' }}</span>
                </div>
                <div class="flex-none">
                    {{-- Dark mode toggle --}}
                    <div
                        x-data="{
                            theme: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'business' : 'corporate'),
                            toggle() {
                                this.theme = this.theme === 'business' ? 'corporate' : 'business';
                                document.documentElement.setAttribute('data-theme', this.theme);
                                localStorage.setItem('theme', this.theme);
                            }
                        }"
                    >
                        <button @click="toggle()" class="btn btn-ghost btn-square" :aria-label="theme === 'business' ? 'Switch to light mode' : 'Switch to dark mode'">
                            {{-- Sun icon (shown in dark mode) --}}
                            <svg x-show="theme === 'business'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 100 10A5 5 0 0012 7z" />
                            </svg>
                            {{-- Moon icon (shown in light mode) --}}
                            <svg x-show="theme === 'corporate'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex-none">
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        </div>
                        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-40 w-52 p-2 shadow-lg border border-base-200">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left">Sign Out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            {{-- Flash messages --}}
            <div class="px-6 pt-4">
                @if (session('success'))
                    <div role="alert" class="alert alert-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div role="alert" class="alert alert-error mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            {{-- Main content --}}
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>

        {{-- Sidebar --}}
        <div class="drawer-side z-40">
            <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <aside class="bg-base-100 border-r border-base-200 w-64 min-h-full flex flex-col">
                <div class="p-4 border-b border-base-200">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="/logo.svg" alt="Open Docket" class="h-8 w-8">
                        <span class="text-xl font-bold text-primary">Open Docket</span>
                    </a>
                </div>

                <nav class="flex-1 p-4">
                    <ul class="menu menu-md p-0 gap-1">
                        <li>
                            <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tickets.index') }}" @class(['active' => request()->routeIs('tickets.*')])>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Tickets
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('contacts.index') }}" @class(['active' => request()->routeIs('contacts.*')])>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Contacts
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('ticket-types.index') }}" @class(['active' => request()->routeIs('ticket-types.*')])>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Ticket Types
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tags.index') }}" @class(['active' => request()->routeIs('tags.*')])>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg>
                                Tags
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('activity.index') }}" @class(['active' => request()->routeIs('activity.*')])>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Activity
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>
        </div>
    </div>
</body>
</html>
