<x-app-layout>

<div class="space-y-6">

    {{-- Header --}}
    <section class="rounded-2xl p-6 shadow-sm"
             style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-1"
                   style="color: var(--color-primary);">
                    {{ __('Bandeja del Jurado') }}
                </p>
                <h1 class="text-2xl font-bold" style="color: var(--color-text-primary);">
                    {{ __('Mis Revisiones Asignadas') }}
                </h1>
            </div>
        </div>
    </section>

    {{-- Main content: Tabla Premium --}}
    <section class="rounded-2xl shadow-sm p-6" 
             style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
        
        <div class="flex items-center gap-3 mb-6">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl shadow-sm"
                  style="background-color: var(--color-accent-light); color: var(--color-primary);">
                <i class="fas fa-folder-open text-lg"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold" style="color: var(--color-text-primary);">
                    Expedientes para Evaluación
                </h2>
                <p class="text-xs" style="color: var(--color-text-muted);">
                    Consulte y evalúe los expedientes de investigación asignados
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 px-4 py-3 rounded-xl text-sm flex items-center gap-2" 
                 style="background-color: var(--color-success-bg); color: var(--color-success);">
                <i class="fas fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border" style="border-color: var(--color-border);">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr style="background-color: var(--color-neutral-bg); border-bottom: 1px solid var(--color-border);">
                            <th class="px-5 py-3.5 text-left font-bold uppercase tracking-wider text-xs" style="color: var(--color-text-secondary);">
                                Número Radicación
                            </th>
                            <th class="px-5 py-3.5 text-left font-bold uppercase tracking-wider text-xs" style="color: var(--color-text-secondary);">
                                Título del Proyecto
                            </th>
                            <th class="px-5 py-3.5 text-center font-bold uppercase tracking-wider text-xs" style="color: var(--color-text-secondary);">
                                Rol
                            </th>
                            <th class="px-5 py-3.5 text-center font-bold uppercase tracking-wider text-xs" style="color: var(--color-text-secondary);">
                                Evaluación
                            </th>
                            <th class="px-5 py-3.5 text-right font-bold uppercase tracking-wider text-xs" style="color: var(--color-text-secondary);">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color: var(--color-border); background-color: var(--color-surface);">
                        @forelse($expedientes as $expediente)
                            <tr class="transition-colors duration-150 hover:bg-neutral-50" 
                                style="border-bottom: 1px solid var(--color-border);">
                                <td class="px-5 py-4 font-bold" style="color: var(--color-primary);">
                                    {{ $expediente->numero_radicacion }}
                                </td>
                                <td class="px-5 py-4" style="color: var(--color-text-primary);">
                                    <div class="max-w-xl truncate" title="{{ $expediente->titulo }}">
                                        {{ \Illuminate\Support\Str::limit($expediente->titulo, 90) }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm" 
                                          style="background-color: var(--color-accent-light); color: var(--color-primary);">
                                        {{ ucfirst($expediente->rol_jurado) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if(is_null($expediente->aprobado))
                                        <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm flex items-center justify-center gap-1.5 mx-auto w-max" 
                                              style="background-color: #FFFBEB; color: #D97706;">
                                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                            Pendiente
                                        </span>
                                    @elseif($expediente->aprobado)
                                        <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm flex items-center justify-center gap-1.5 mx-auto w-max" 
                                              style="background-color: #ECFDF5; color: #059669;">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            Aprobado
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-bold shadow-sm flex items-center justify-center gap-1.5 mx-auto w-max" 
                                              style="background-color: #FEF2F2; color: #DC2626;">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                            Observado
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('jurado.observaciones.show', $expediente->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition hover:opacity-90"
                                       style="background-color: var(--color-primary); color: white;">
                                        <i class="fas fa-magnifying-glass"></i> Revisar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-sm" style="color: var(--color-text-muted);">
                                    <i class="fas fa-clipboard-check text-3xl mb-3 block" style="color: var(--color-border);"></i>
                                    No tiene expedientes asignados para revisión.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </section>

</div>

</x-app-layout>
