<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center gap-3 mb-4">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl" style="background-color: var(--color-accent-light); color: var(--color-primary);">
                    <i class="fas fa-file-signature text-lg"></i>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Cierre de Expediente (Actas)</h2>
                    <p class="text-xs text-gray-500">Selecciona el expediente con sustentación programada para ingresar calificaciones.</p>
                </div>
            </div>

            @if ($expedientes->isEmpty())
                <div class="p-4 rounded-xl text-sm font-bold" style="background-color: #FFF9DB; color: #F59F00; border: 1px solid #FFE066;">
                    <i class="fas fa-circle-info"></i> No hay sustentaciones programadas listas para calificar en este momento.
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-200">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Radicación</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estudiante</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($expedientes as $exp)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
                                        {{ $exp->numero_radicacion }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 text-xs font-medium">
                                        {{ Str::limit($exp->titulo, 60) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600 text-xs">
                                        {{ $exp->estudiante }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('sustentacion.cerrar.show', $exp->id) }}"
                                           class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-4 rounded-xl transition shadow-sm">
                                            <i class="fas fa-pen-to-square"></i> Evaluar
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
