<x-app-layout>

<div class="space-y-6">

    {{-- Header --}}
    <section class="rounded-2xl p-6 shadow-sm"
             style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest mb-1"
                   style="color: var(--color-primary);">
                    {{ __('Asignación de Jurados') }}
                </p>
                <h1 class="text-2xl font-bold" style="color: var(--color-text-primary);">
                    {{ __('Designar Miembros & Registrar Resolución') }}
                </h1>
            </div>
            <a href="{{ route('jurado.asignar') }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold"
               style="background-color: var(--color-accent-light); color: var(--color-primary);">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </section>

    {{-- Workspace --}}
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Formulario --}}
        <div class="lg:col-span-2 space-y-6">
            
            <section class="rounded-2xl p-6 shadow-sm"
                     style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                
                {{-- Expediente Band --}}
                <div class="p-5 rounded-xl mb-6" style="background-color: var(--color-neutral-bg); border: 1px solid var(--color-border);">
                    <p class="font-bold text-lg" style="color: var(--color-text-primary);">
                        {{ $expediente->numero_radicacion }}
                    </p>
                    <p class="text-sm mt-1" style="color: var(--color-text-secondary);">
                        {{ $expediente->titulo }}
                    </p>
                    <div class="grid grid-cols-2 gap-y-2 gap-x-4 mt-4 text-xs" style="color: var(--color-text-muted);">
                        <span><i class="fas fa-user-graduate mr-1"></i> Estudiante: <strong style="color: var(--color-text-primary);">{{ $expediente->estudiante_nombre ?: 'No asignado' }}</strong></span>
                        <span><i class="fas fa-user-tie mr-1"></i> Asesor: <strong style="color: var(--color-text-primary);">{{ $expediente->asesor_nombre ?: 'Sin asesor' }}</strong></span>
                        <span><i class="fas fa-graduation-cap mr-1"></i> Programa: <strong style="color: var(--color-text-primary);">{{ $expediente->sucursal_nombre ?: 'General' }}</strong></span>
                        <span><i class="fas fa-calendar-day mr-1"></i> Ingreso: <strong style="color: var(--color-text-primary);">{{ \Carbon\Carbon::parse($expediente->created_at)->format('d/m/Y') }}</strong></span>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="mb-6 px-4 py-3 rounded-xl text-sm" style="background-color: var(--color-danger-bg); color: var(--color-danger);">
                        <p class="font-bold"><i class="fas fa-circle-exclamation mr-1"></i> Por favor corrige los errores:</p>
                        <ul class="list-disc list-inside text-xs mt-1 pl-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('jurado.asignar.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="expediente_id" value="{{ $expediente->id }}">

                    {{-- Datos de la Resolución --}}
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-widest mb-4" style="color: var(--color-text-muted);">
                            1. Datos de la Resolución
                        </h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="numero_resolucion" class="block text-xs font-semibold mb-1" style="color: var(--color-text-secondary);">
                                    Número de Resolución <span style="color: var(--color-danger);">*</span>
                                </label>
                                <input type="text" id="numero_resolucion" name="numero_resolucion" required
                                       value="{{ old('numero_resolucion') }}"
                                       placeholder="RES-XXXX-2026"
                                       class="w-full rounded-xl text-sm border p-2.5 focus:outline-none"
                                       style="background-color: var(--color-neutral-bg); border-color: var(--color-border); color: var(--color-text-primary);">
                            </div>

                            <div>
                                <label for="fecha_emision" class="block text-xs font-semibold mb-1" style="color: var(--color-text-secondary);">
                                    Fecha de Emisión <span style="color: var(--color-danger);">*</span>
                                </label>
                                <input type="date" id="fecha_emision" name="fecha_emision" required
                                       value="{{ old('fecha_emision', date('Y-m-d')) }}"
                                       class="w-full rounded-xl text-sm border p-2.5 focus:outline-none"
                                       style="background-color: var(--color-neutral-bg); border-color: var(--color-border); color: var(--color-text-primary);">
                            </div>
                        </div>
                    </div>

                    {{-- Designación de Jurados --}}
                    <div class="pt-4" style="border-top: 1px solid var(--color-border);">
                        <h3 class="text-sm font-bold uppercase tracking-widest mb-4" style="color: var(--color-text-muted);">
                            2. Designación del Jurado
                        </h3>

                        <div class="space-y-4">
                            <div class="p-4 rounded-xl" style="background-color: var(--color-neutral-bg); border: 1px solid var(--color-border);">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" style="background-color: var(--color-accent-light); color: var(--color-primary);">
                                        <i class="fas fa-gavel mr-1"></i> Presidente
                                    </span>
                                </div>
                                <x-autocomplete 
                                    name="presidente_id" 
                                    label="" 
                                    endpoint="/ajax/personas/search" 
                                    placeholder="Escriba nombre o DNI del Presidente..."
                                />
                            </div>

                            <div class="p-4 rounded-xl" style="background-color: var(--color-neutral-bg); border: 1px solid var(--color-border);">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" style="background-color: #E6FFFA; color: #00A3C4;">
                                        <i class="fas fa-pen mr-1"></i> Secretario
                                    </span>
                                </div>
                                <x-autocomplete 
                                    name="secretario_id" 
                                    label="" 
                                    endpoint="/ajax/personas/search" 
                                    placeholder="Escriba nombre o DNI del Secretario..."
                                />
                            </div>

                            <div class="p-4 rounded-xl" style="background-color: var(--color-neutral-bg); border: 1px solid var(--color-border);">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" style="background-color: #FAF5FF; color: #9F7AEA;">
                                        <i class="fas fa-bullhorn mr-1"></i> Vocal
                                    </span>
                                </div>
                                <x-autocomplete 
                                    name="vocal_id" 
                                    label="" 
                                    endpoint="/ajax/personas/search" 
                                    placeholder="Escriba nombre o DNI del Vocal..."
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4" style="border-top: 1px solid var(--color-border);">
                        <a href="{{ route('jurado.asignar') }}" 
                           class="px-4 py-2.5 rounded-xl text-sm font-bold transition hover:bg-gray-100"
                           style="color: var(--color-text-secondary); border: 1px solid var(--color-border);">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm transition hover:opacity-90"
                                style="background-color: var(--color-primary); color: white;">
                            <i class="fas fa-circle-check mr-1"></i> Registrar Designación
                        </button>
                    </div>

                </form>

            </section>

        </div>

        {{-- Sidebar / Validaciones --}}
        <aside class="rounded-2xl p-6 shadow-sm self-start"
               style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            
            <h2 class="text-base font-bold mb-4 flex items-center gap-2" style="color: var(--color-text-primary);">
                <i class="fas fa-shield-halved" style="color: var(--color-primary);"></i>
                Validaciones de Regla
            </h2>

            <ul class="space-y-3 text-xs" style="color: var(--color-text-secondary);">
                <li class="flex items-start gap-2 p-3 rounded-xl" style="background-color: var(--color-success-bg);">
                    <i class="fas fa-check-circle text-sm mt-0.5" style="color: var(--color-success);"></i>
                    <div>
                        <p class="font-bold" style="color: var(--color-text-primary);">Roles Únicos</p>
                        <p class="mt-0.5">El sistema verifica que los 3 jurados sean personas diferentes.</p>
                    </div>
                </li>

                <li class="flex items-start gap-2 p-3 rounded-xl" style="background-color: var(--color-success-bg);">
                    <i class="fas fa-check-circle text-sm mt-0.5" style="color: var(--color-success);"></i>
                    <div>
                        <p class="font-bold" style="color: var(--color-text-primary);">Restricción de Asesor</p>
                        <p class="mt-0.5">Los docentes asesores del expediente están inhabilitados de ser jurados.</p>
                    </div>
                </li>

                <li class="flex items-start gap-2 p-3 rounded-xl" style="background-color: var(--color-accent-light);">
                    <i class="fas fa-circle-exclamation text-sm mt-0.5" style="color: var(--color-primary);"></i>
                    <div>
                        <p class="font-bold" style="color: var(--color-text-primary);">Créditos y FAI</p>
                        <p class="mt-0.5">El expediente cuenta con todos los filtros académicos.</p>
                    </div>
                </li>
            </ul>

        </aside>

    </div>

</div>

</x-app-layout>
