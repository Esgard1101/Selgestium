<x-app-layout>
    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Encabezado --}}
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-filter mr-2" style="color: var(--color-primary);"></i>
                    Panel FAI — Verificaciones Pendientes
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Expedientes en fase 2 u 8 con al menos un filtro administrativo sin aprobar.
                    Haz clic en "Ver Panel" para revisar el estado de cada FAI.
                </p>
            </div>

            {{-- Listado --}}
            @if ($expedientes->isEmpty())
                <div class="flex items-center gap-4 bg-green-50 border border-green-300 text-green-800
                            rounded-2xl px-6 py-5 shadow-sm">
                    <i class="fas fa-circle-check text-3xl" style="color: var(--color-success);"></i>
                    <div>
                        <p class="font-semibold">Sin verificaciones pendientes</p>
                        <p class="text-sm mt-0.5">
                            Todos los expedientes activos tienen sus filtros FAI al día.
                        </p>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr style="background-color: var(--color-primary);">
                                <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    N.° Radicación
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    Estudiante
                                </th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">
                                    Fase
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">
                                    FAIs pendientes
                                </th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">
                                    Acción
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($expedientes as $exp)
                                <tr class="hover:bg-gray-50 transition-colors duration-100">
                                    <td class="px-5 py-4 whitespace-nowrap font-mono text-xs text-gray-700">
                                        {{ $exp->numero_radicacion }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-gray-800 font-medium">
                                        {{ $exp->estudiante }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: var(--color-accent); color: var(--color-primary);">
                                            Fase {{ $exp->fase_actual }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach ($exp->faltantes as $codigo)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold"
                                                      style="background-color: var(--color-warning-bg);
                                                             color: var(--color-warning);
                                                             border: 1px solid var(--color-warning);">
                                                    {{ $codigo }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ route('fai.panel.show', $exp->id) }}"
                                           class="inline-flex items-center gap-1.5 text-xs font-semibold px-4 py-1.5
                                                  rounded-lg transition-colors duration-150 text-white"
                                           style="background-color: var(--color-primary);"
                                           onmouseover="this.style.backgroundColor='var(--color-primary-dark)'"
                                           onmouseout="this.style.backgroundColor='var(--color-primary)'">
                                            <i class="fas fa-table-cells-large"></i>
                                            Ver Panel
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="text-xs text-gray-400 text-right">
                    {{ $expedientes->count() }} expediente(s) con verificaciones pendientes
                </p>
            @endif

        </div>
    </div>
</x-app-layout>
