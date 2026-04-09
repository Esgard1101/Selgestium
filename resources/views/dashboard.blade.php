<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <section class="rounded-3xl bg-white p-8 shadow-xl shadow-slate-200/70">
            <div class="flex items-start gap-4">
                <img
                    src="{{ asset(config('university.logo_url')) }}"
                    alt="{{ config('app.name', 'SELGESTIUM') }}"
                    class="h-20 w-auto shrink-0"
                >

                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-primary/70">{{ config('university.faculty_name') }}</p>
                        <h3 class="text-3xl font-bold text-slate-800">{{ __('Panel principal SELGESTIUM') }}</h3>
                    </div>

                    <p class="max-w-3xl text-base leading-7 text-slate-600">
                        {{ __('Bienvenido al entorno interno del sistema. Desde aqui se gestionan expedientes, fases, sustentaciones y los flujos operativos del modulo PUR.') }}
                    </p>
                </div>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-3">
            <article class="rounded-3xl bg-white p-6 shadow-lg shadow-slate-200/60 lg:col-span-2">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Agenda</p>
                        <h3 class="text-2xl font-bold text-slate-800">{{ __('Proximas Sustentaciones (Art. 123)') }}</h3>
                    </div>
                    <span class="rounded-full bg-accent/10 px-3 py-1 text-xs font-semibold text-primary">
                        {{ __('Calendario academico') }}
                    </span>
                </div>

                @if(isset($sustentaciones) && $sustentaciones->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Fecha') }}</th>
                                    <th class="px-4 py-3">{{ __('Hora') }}</th>
                                    <th class="px-4 py-3">{{ __('Expediente') }}</th>
                                    <th class="px-4 py-3">{{ __('Lugar') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                                @foreach($sustentaciones as $sus)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $sus->fecha }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $sus->hora }}</td>
                                        <td class="px-4 py-3">{{ $sus->numero_radicacion }} - {{ $sus->titulo }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $sus->lugar }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-5 py-8 text-slate-500">
                        {{ __('No hay sustentaciones programadas.') }}
                    </div>
                @endif
            </article>

            <aside class="rounded-3xl bg-white p-6 shadow-lg shadow-slate-200/60">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Acceso rapido') }}</p>
                <h3 class="mt-2 text-xl font-bold text-slate-800">{{ __('Estado del entorno') }}</h3>

                <div class="mt-6 space-y-4 text-sm text-slate-600">
                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-semibold text-slate-800">{{ __('Autenticacion hibrida activa') }}</p>
                        <p class="mt-1">{{ __('Jetstream y Fortify quedan reservados para login, logout y recuperacion de credenciales.') }}</p>
                    </div>

                    <div class="rounded-2xl bg-slate-50 px-4 py-4">
                        <p class="font-semibold text-slate-800">{{ __('ERP en Blade') }}</p>
                        <p class="mt-1">{{ __('Los modulos internos deben renderizar HTML completo y usar AJAX solo en endpoints explicitos del negocio.') }}</p>
                    </div>
                </div>
            </aside>
        </section>
    </div>
</x-app-layout>
