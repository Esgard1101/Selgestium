<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <style>
            :root {
                --color-primary: {{ config('university.primary_color', '#003087') }};
                --color-primary-dark: {{ config('university.primary_dark_color', '#001F5C') }};
                --color-primary-light: {{ config('university.primary_light_color', '#1A4BA8') }};
                --color-accent: {{ config('university.accent_color', '#00BFFF') }};
                --color-accent-light: {{ config('university.accent_light_color', '#E0F7FF') }};
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        @php
            $rolSimulado = 9;
            $displayName = Auth::user()->name ?? 'Salazar Flores Jesus';
            $displayRole = __('app.role_advisor_faculty');
            $initials = collect(explode(' ', trim($displayName)))
                ->filter()
                ->take(2)
                ->map(fn ($segment) => strtoupper(substr($segment, 0, 1)))
                ->implode('');
        @endphp

        <div class="flex h-screen bg-bg overflow-hidden">
            <aside class="hidden lg:flex lg:w-64 lg:flex-col bg-primary text-white overflow-y-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
                    <!-- University logo (adjust size here if needed) -->
                    <img src="{{ asset(config('university.logo_url')) }}" alt="{{ config('app.name', 'SELGESTIUN') }}" class="h-10 w-auto">
                    <div>
                        <p class="text-sm font-semibold tracking-wide">{{ config('app.name', 'SELGESTIUN') }}</p>
                        <p class="text-xs text-white/70">{{ config('university.faculty_name') }}</p>
                    </div>
                </div>

                <nav class="flex-1 px-3 py-4 space-y-1">
                    {{-- Sidebar menu is simulated for MVP. In next phase it will be resolved from permiso_usuario using session context. --}}

                    {{-- Student Module --}}
                    @if ($rolSimulado === 6)
                        <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('student.expediente.*') ? 'bg-warning text-primary font-bold' : 'text-white hover:bg-primary-light' }}">
                            <x-icons.document-text class="size-5" />
                            <span>{{ __('app.menu_my_record') }}</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('student.radicacion.*') ? 'bg-warning text-primary font-bold' : 'text-white hover:bg-primary-light' }}">
                            <x-icons.plus-circle class="size-5" />
                            <span>{{ __('app.menu_new_filing') }}</span>
                        </a>
                    @endif

                    {{-- Reviewer Module --}}
                    @if ($rolSimulado === 8)
                        <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('review.expedientes.*') ? 'bg-warning text-primary font-bold' : 'text-white hover:bg-primary-light' }}">
                            <x-icons.folder-open class="size-5" />
                            <span>{{ __('app.menu_files_to_review') }}</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('review.calendar.*') ? 'bg-warning text-primary font-bold' : 'text-white hover:bg-primary-light' }}">
                            <x-icons.calendar class="size-5" />
                            <span>{{ __('app.menu_calendar') }}</span>
                        </a>
                    @endif

                    {{-- Admin Module --}}
                    @if ($rolSimulado === 9)
                        <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('admin.expedientes.*') ? 'bg-warning text-primary font-bold' : 'text-white hover:bg-primary-light' }}">
                            <x-icons.home class="size-5" />
                            <span>{{ __('app.menu_all_files') }}</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition {{ request()->routeIs('admin.fai.*') ? 'bg-warning text-primary font-bold' : 'text-white hover:bg-primary-light' }}">
                            <x-icons.cog class="size-5" />
                            <span>{{ __('app.menu_fai_settings') }}</span>
                        </a>
                    @endif
                </nav>
            </aside>

            <div class="flex-1 flex flex-col overflow-hidden">
                <header class="h-20 bg-primary shadow-sm border-b border-white/10">
                    <div class="h-full px-6 flex items-center justify-between gap-6 text-white">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- University logo (adjust size here if needed) -->
                            <img src="{{ asset(config('university.logo_url')) }}" alt="{{ config('app.name', 'SELGESTIUN') }}" class="h-10 w-auto lg:hidden">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold leading-tight truncate">{{ config('app.name', 'SELGESTIUN') }}</p>
                                <p class="text-xs text-white/75 truncate">
                                    {{ config('university.faculty_name') }} - {{ config('university.university_name') }} - {{ config('university.academic_period') }}
                                </p>
                            </div>
                        </div>

                        <nav class="hidden xl:flex items-center gap-8 text-sm font-medium">
                            <a href="#" class="text-white/85 hover:text-white transition">{{ __('app.nav_portal_pur') }}</a>
                            <a href="#" class="text-white/85 hover:text-white transition">{{ __('app.nav_filters_fai') }}</a>
                            <a href="#" class="text-white/85 hover:text-white transition">{{ __('app.nav_security') }}</a>
                        </nav>

                        <div class="flex items-center">
                            <x-dropdown align="right" width="56">
                                <x-slot name="trigger">
                                    <button type="button" class="inline-flex items-center gap-3 rounded-lg px-2 py-1.5 text-sm hover:bg-white/10 transition focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 focus:ring-offset-primary">
                                        <span class="inline-flex size-9 items-center justify-center rounded-full bg-warning text-primary font-bold">
                                            {{ $initials !== '' ? $initials : 'SF' }}
                                        </span>
                                        <span class="hidden md:block text-left">
                                            <span class="block text-sm font-semibold leading-tight">{{ $displayName }}</span>
                                            <span class="block text-xs text-white/75 leading-tight">{{ $displayRole }}</span>
                                        </span>
                                        <svg class="size-4 text-white/80" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('profile.show') }}">
                                        {{ __('app.profile') }}
                                    </x-dropdown-link>

                                    <div class="border-t border-gray-200"></div>

                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf

                                        <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                            {{ __('app.logout') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6">
                    @if (isset($header))
                        <div class="mb-6 rounded-xl bg-surface p-4 shadow-sm">
                            {{ $header }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
