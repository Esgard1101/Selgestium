<x-app-layout>

    <div class="space-y-6">

        {{-- Bienvenida --}}
        <section class="rounded-2xl p-6 shadow-sm"
            style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <div class="flex items-start gap-5">
                <img src="{{ asset(config('university.logo_url')) }}"
                    alt="{{ config('app.name') }}"
                    class="h-16 w-auto flex-shrink-0"
                    onerror="this.style.display='none'">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest mb-1"
                        style="color: var(--color-primary);">
                        {{ config('university.faculty_name') }} · {{ config('university.university_name') }}
                    </p>
                    <h1 class="text-2xl font-bold mb-1" style="color: var(--color-text-primary);">
                        Bienvenido, {{ session('nombres', Auth::user()->name ?? 'Usuario') }}
                    </h1>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                            style="background-color: var(--color-accent-light); color: var(--color-primary);">
                            <i class="fas fa-id-badge"></i>
                            {{ session('perfil', 'Usuario') }}
                        </span>
                        <span class="text-xs" style="color: var(--color-text-muted);">
                            Periodo académico {{ config('university.academic_period') }}
                        </span>

                        <x-slot name="header">
                            <h2 id="page_title" class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ __('Dashboard') }}
                            </h2>
                        </x-slot>

                        <div class="py-12">
                            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                                <div id="frame" class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                                    {{-- Contenido inicial --}}
                                    <div class="text-center text-gray-500">
                                        <i class="fa-solid fa-arrow-up mb-4 text-4xl"></i>
                                        <p>Bienvenido al sistema. Seleccione <b>Portal PUR</b> en el menú superior para ver los expedientes.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Grid: sustentaciones + estado del sistema --}}
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Próximas sustentaciones --}}
            <section class="lg:col-span-2 rounded-2xl p-6 shadow-sm"
                style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest mb-0.5"
                            style="color: var(--color-text-muted);">Agenda</p>
                        <h2 class="text-lg font-bold" style="color: var(--color-text-primary);">
                            Próximas Sustentaciones
                        </h2>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                        style="background-color: var(--color-accent-light); color: var(--color-primary);">
                        Calendario
                    </span>
                </div>

                @if(isset($sustentaciones) && $sustentaciones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--color-border);">
                                <th class="text-left py-2 px-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color: var(--color-text-muted);">Fecha</th>
                                <th class="text-left py-2 px-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color: var(--color-text-muted);">Hora</th>
                                <th class="text-left py-2 px-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color: var(--color-text-muted);">Expediente</th>
                                <th class="text-left py-2 px-3 text-xs font-semibold uppercase tracking-wider"
                                    style="color: var(--color-text-muted);">Lugar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sustentaciones as $sus)
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td class="py-3 px-3 whitespace-nowrap font-medium"
                                    style="color: var(--color-text-primary);">{{ $sus->fecha }}</td>
                                <td class="py-3 px-3 whitespace-nowrap"
                                    style="color: var(--color-text-secondary);">{{ $sus->hora }}</td>
                                <td class="py-3 px-3" style="color: var(--color-text-primary);">
                                    <p class="font-medium">{{ $sus->numero_radicacion }}</p>
                                    <p class="text-xs" style="color: var(--color-text-muted);">{{ Str::limit($sus->titulo, 60) }}</p>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap"
                                    style="color: var(--color-text-secondary);">{{ $sus->lugar }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 rounded-xl"
                    style="background-color: var(--color-neutral-bg); border: 1px dashed var(--color-border);">
                    <i class="fas fa-calendar-xmark text-3xl mb-3" style="color: var(--color-neutral);"></i>
                    <p class="text-sm" style="color: var(--color-text-muted);">No hay sustentaciones programadas</p>
                </div>
                @endif
            </section>

            {{-- Estado del entorno --}}
            <aside class="rounded-2xl p-6 shadow-sm"
                style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                <p class="text-xs font-semibold uppercase tracking-widest mb-1"
                    style="color: var(--color-text-muted);">Sesión activa</p>
                <h2 class="text-lg font-bold mb-4" style="color: var(--color-text-primary);">Estado</h2>

                <dl class="space-y-3 text-sm">
                    <div class="rounded-xl px-4 py-3" style="background-color: var(--color-neutral-bg);">
                        <dt class="text-xs font-semibold mb-0.5" style="color: var(--color-text-muted);">Facultad</dt>
                        <dd class="font-medium" style="color: var(--color-text-primary);">
                            {{ config('university.faculty_name') }}
                        </dd>
                    </div>

                    <div class="rounded-xl px-4 py-3" style="background-color: var(--color-neutral-bg);">
                        <dt class="text-xs font-semibold mb-0.5" style="color: var(--color-text-muted);">Rol</dt>
                        <dd class="font-medium" style="color: var(--color-text-primary);">
                            {{ session('perfil', 'Sin asignar') }}
                        </dd>
                    </div>

                    <div class="rounded-xl px-4 py-3" style="background-color: var(--color-neutral-bg);">
                        <dt class="text-xs font-semibold mb-0.5" style="color: var(--color-text-muted);">Accesos activos</dt>
                        <dd class="font-medium" style="color: var(--color-text-primary);">
                            {{ count(session('permisos', [])) }} opciones de menú
                        </dd>
                    </div>

                    <div class="rounded-xl px-4 py-3" style="background-color: var(--color-success-bg);">
                        <dt class="text-xs font-semibold mb-0.5" style="color: var(--color-success);">
                            <i class="fas fa-circle-check mr-1"></i>Autenticación
                        </dt>
                        <dd class="font-medium" style="color: var(--color-text-primary);">Sesión verificada</dd>
                    </div>
                </dl>
            </aside>
        </div>
    </div>
</x-app-layout>