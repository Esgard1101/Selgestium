<x-app-layout>

<div class="space-y-6">

    {{-- Header --}}
    <section class="rounded-2xl p-6 shadow-sm"
             style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-1"
                   style="color: var(--color-primary);">
                    {{ __('Gestión de Jurados') }}
                </p>
                <h1 class="text-2xl font-bold" style="color: var(--color-text-primary);">
                    {{ __('Selector de Expedientes') }}
                </h1>
            </div>

        </div>
    </section>

    {{-- Stats Grid --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl p-5 shadow-sm" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-text-muted);">Pendientes</p>
            <p class="text-3xl font-extrabold mt-2" style="color: var(--color-text-primary);">{{ $pendientesCount }}</p>
        </div>
        <div class="rounded-2xl p-5 shadow-sm" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-text-muted);">Jurados Activos</p>
            <p class="text-3xl font-extrabold mt-2" style="color: var(--color-text-primary);">{{ $juradosActivosCount }}</p>
        </div>
        <div class="rounded-2xl p-5 shadow-sm" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-text-muted);">Resoluciones</p>
            <p class="text-3xl font-extrabold mt-2" style="color: var(--color-text-primary);">{{ $resolucionesCount }}</p>
        </div>
        <div class="rounded-2xl p-5 shadow-sm" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <p class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-text-muted);">Conflictos</p>
            <p class="text-3xl font-extrabold mt-2" style="color: var(--color-text-primary);">0</p>
        </div>
    </div>

    {{-- Listado de Expedientes --}}
    <section class="rounded-2xl p-6 shadow-sm"
             style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
        
        <div class="flex items-center gap-2 mb-6">
            <i class="fas fa-folder-open text-lg" style="color: var(--color-primary);"></i>
            <h2 class="text-lg font-bold" style="color: var(--color-text-primary);">
                Expedientes en espera de asignación
            </h2>
        </div>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background-color: var(--color-success-bg); color: var(--color-success);">
                <i class="fas fa-circle-check mr-1"></i> {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse($expedientes as $expediente)
                <div class="flex flex-col md:flex-row md:items-center justify-between p-5 rounded-xl transition-all"
                     style="background-color: var(--color-neutral-bg); border: 1px solid var(--color-border);">
                    
                    <div class="mb-4 md:mb-0">
                        <p class="font-bold text-base" style="color: var(--color-text-primary);">
                            {{ $expediente->numero_radicacion }}
                        </p>
                        <p class="text-sm mt-1" style="color: var(--color-text-secondary);">
                            {{ $expediente->titulo }}
                        </p>
                        <div class="flex items-center gap-4 mt-3 text-xs" style="color: var(--color-text-muted);">
                            <span><i class="fas fa-user-graduate mr-1"></i> {{ $expediente->estudiante_nombre ?: 'No asignado' }}</span>
                            <span><i class="fas fa-clock mr-1"></i> Fase {{ $expediente->fase_actual }}</span>
                            <span><i class="fas fa-calendar-day mr-1"></i> {{ \Carbon\Carbon::parse($expediente->created_at)->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-shrink-0">
                        <a href="{{ route('jurado.asignar.show', $expediente->id) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold shadow-sm transition hover:opacity-90"
                           style="background-color: var(--color-primary); color: white;">
                            <i class="fas fa-user-check"></i>
                            Asignar Jurados
                        </a>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-12 rounded-xl"
                     style="background-color: var(--color-neutral-bg); border: 1px dashed var(--color-border);">
                    <i class="fas fa-folder-open text-3xl mb-3" style="color: var(--color-neutral);"></i>
                    <p class="text-sm" style="color: var(--color-text-muted);">No hay expedientes disponibles para asignar jurado.</p>
                </div>
            @endforelse
        </div>

    </section>

</div>

</x-app-layout>
