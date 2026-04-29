<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SELGESTIUM') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">

    <style>
        :root {

            /* prettier-ignore */
            --color-primary: {
                    {
                    config('university.primary_color', '#003087')
                }
            }

            ;

            /* prettier-ignore */
            --color-primary-dark: {
                    {
                    config('university.primary_dark_color', '#001F5C')
                }
            }

            ;

            /* prettier-ignore */
            --color-primary-light: {
                    {
                    config('university.primary_light_color', '#1A4BA8')
                }
            }

            ;

            /* prettier-ignore */
            --color-accent: {
                    {
                    config('university.accent_color', '#00BFFF')
                }
            }

            ;

            /* prettier-ignore */
            --color-accent-light: {
                    {
                    config('university.accent_light_color', '#E0F7FF')
                }
            }

            ;

            /* Fallbacks de diseño para la estructura Upstream */
            --color-bg: #f3f4f6;
            --font-sans: 'Figtree',
            ui-sans-serif,
            system-ui,
            sans-serif;
            --color-surface: #ffffff;
            --color-border: #e5e7eb;
            --color-text-primary: #111827;
            --color-text-secondary: #4b5563;
            --color-text-muted: #6b7280;
            --color-neutral-bg: #f9fafb;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased font-sans" style="background-color: var(--color-bg);">

    <x-banner />

    <div class="flex h-screen overflow-hidden">

        {{-- ── SIDEBAR ──────────────────────────────────────────────────────────── --}}
        <aside id="sidebar"
            class="hidden lg:flex w-64 flex-col flex-shrink-0 overflow-y-auto transition-all duration-300"
            style="background-color: var(--color-primary); scrollbar-width: none;">

            {{-- Logo / branding --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b" style="border-color: rgba(255,255,255,0.12);">
                <img src="{{ asset(config('university.logo_url')) }}"
                    alt="{{ config('app.name') }}"
                    class="h-9 w-auto flex-shrink-0"
                    onerror="this.style.display='none'">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-white leading-tight truncate">{{ config('app.name', 'SELGESTIUM') }}</p>
                    <p class="text-xs leading-tight truncate" style="color: rgba(255,255,255,0.55);">
                        {{ config('university.faculty_name') }} · {{ config('university.academic_period') }}
                    </p>
                </div>
            </div>

            {{-- Perfil del usuario --}}
            <div class="flex items-center gap-3 px-5 py-3 border-b" style="border-color: rgba(255,255,255,0.08);">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold flex-shrink-0"
                    style="background-color: var(--color-accent); color: var(--color-primary);">
                    {{ strtoupper(substr(session('nombres', Auth::user()->name ?? 'U'), 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ session('nombres', Auth::user()->name ?? 'Usuario') }} {{ session('apellidos', '') }}</p>
                    <p class="text-xs truncate" style="color: rgba(255,255,255,0.50);">{{ session('perfil', 'Usuario') }}</p>
                </div>
            </div>

            {{-- Navegación dinámica --}}
            <nav class="flex-1 px-3 py-3 space-y-4">
                @forelse ($menuItems ?? [] as $grupo => $items)
                <div>
                    @if ($grupo)
                    <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wider"
                        style="color: rgba(255,255,255,0.38);">
                        {{ $grupo }}
                    </p>
                    @endif

                    @foreach ($items as $item)
                    @php
                    $activo = $item->ruta_nombre && request()->routeIs($item->ruta_nombre . '*');
                    @endphp

                    <a href="{{ $item->ruta_nombre ? route($item->ruta_nombre) : '#' }}"
                        @class([ 'flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors duration-150 mb-0.5' , 'bg-white/20 text-white font-semibold'=> $activo,
                        'text-white/80 hover:bg-white/10' => !$activo,
                        ])>
                        <i class="{{ $item->icono ?? 'fas fa-circle' }} w-4 text-center flex-shrink-0" style="font-size: 0.875rem;"></i>
                        <span>{{ $item->descripcion }}</span>
                    </a>
                    @endforeach
                </div>
                @empty
                <p class="px-3 text-xs" style="color: rgba(255,255,255,0.38);">Sin accesos configurados</p>
                @endforelse
            </nav>

            {{-- Logout lateral --}}
            <div class="px-3 py-3 border-t" style="border-color: rgba(255,255,255,0.10);">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors duration-150"
                        style="color: rgba(255,255,255,0.65);"
                        onmouseover="this.style.backgroundColor='rgba(255,255,255,0.08)'; this.style.color='#fff'"
                        onmouseout="this.style.backgroundColor=''; this.style.color='rgba(255,255,255,0.65)'">
                        <i class="fas fa-right-from-bracket w-4 text-center flex-shrink-0" style="font-size: 0.875rem;"></i>
                        <span>Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ── ÁREA PRINCIPAL ───────────────────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Navbar superior --}}
            <header class="flex-shrink-0 h-16 flex items-center justify-between px-6 shadow-sm border-b"
                style="background-color: var(--color-surface); border-color: var(--color-border);">

                {{-- Botón hamburguesa en mobile y Título--}}
                <div class="flex items-center gap-4">
                    <button type="button"
                        class="lg:hidden p-2 rounded-lg hover:bg-gray-100"
                        style="color: var(--color-text-secondary);"
                        onclick="document.getElementById('sidebar').classList.toggle('hidden')">
                        <i class="fas fa-bars text-lg"></i>
                    </button>

                    <div class="hidden lg:block">
                        @if (isset($header))
                        <div class="text-sm font-semibold" style="color: var(--color-text-primary);">{{ $header }}</div>
                        @else
                        <div class="text-sm" style="color: var(--color-text-secondary);">
                            {{ config('university.university_name') }} · {{ config('university.faculty_name') }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Acciones y Usuario --}}
                <div class="flex items-center gap-5">

                    {{-- Botón de Portal PUR --}}
                    <nav class="hidden xl:flex items-center text-sm font-medium">
                        <button onclick="setRun('/pur', '', 'frame', 'Portal PUR', 'loading')" class="text-gray-500 hover:text-gray-800 transition cursor-pointer font-semibold">
                            {{ __('app.nav_portal_pur') ?? 'Portal PUR' }}
                        </button>
                    </nav>

                    {{-- Usuario + dropdown --}}
                    <div class="flex items-center gap-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button type="button"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm transition-colors duration-150"
                                    style="color: var(--color-text-primary);"
                                    onmouseover="this.style.backgroundColor='var(--color-neutral-bg)'"
                                    onmouseout="this.style.backgroundColor=''">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold"
                                        style="background-color: var(--color-primary); color: #ffffff;">
                                        {{ strtoupper(substr(session('nombres', Auth::user()->name ?? 'U'), 0, 1)) }}
                                    </span>
                                    <span class="hidden md:block font-medium">{{ session('nombres', Auth::user()->name ?? '') }}</span>
                                    <i class="fas fa-chevron-down text-xs" style="color: var(--color-text-muted);"></i>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2 border-b" style="border-color: var(--color-border);">
                                    <p class="text-xs font-semibold" style="color: var(--color-text-primary);">
                                        {{ session('nombres', Auth::user()->name ?? '') }} {{ session('apellidos', '') }}
                                    </p>
                                    <p class="text-xs" style="color: var(--color-text-muted);">
                                        {{ session('perfil', 'Usuario') }}
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                        <i class="fas fa-right-from-bracket mr-2"></i> Cerrar sesión
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </header>

            {{-- Contenido principal --}}
            <main class="flex-1 overflow-y-auto p-6" id="frame">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function setRun(url, params, targetId, title, loadingClass) {
            const target = document.getElementById(targetId);
            if (loadingClass) target.innerHTML = '<div class="text-center p-10"><i class="fa-solid fa-spinner fa-spin fa-2x"></i> Cargando...</div>';

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const titleEl = document.getElementById('page_title');
                    if (titleEl && data.page_title) titleEl.innerHTML = data.page_title;

                    target.innerHTML = data.content;

                    if (data.content_footer) {
                        const scriptEl = document.createRange().createContextualFragment(data.content_footer);
                        target.appendChild(scriptEl);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    target.innerHTML = '<div class="text-red-500 p-4">Error al cargar los datos.</div>';
                });
        }
    </script>

    @stack('modals')
    @livewireScripts
</body>

</html>