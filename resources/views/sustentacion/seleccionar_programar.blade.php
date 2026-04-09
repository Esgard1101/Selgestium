<x-app-layout>
    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div>
                <h2 class="text-2xl font-bold text-gray-800">Programar Sustentación</h2>
                <p class="mt-1 text-sm text-gray-500">Selecciona el expediente que deseas programar (Fase 10).</p>
            </div>

            @if ($expedientes->isEmpty())
                <div class="bg-yellow-50 border border-yellow-300 text-yellow-700 px-4 py-4 rounded-lg text-sm">
                    No hay expedientes en Fase 10 pendientes de programar.
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Radicación</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estudiante</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($expedientes as $exp)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">
                                        {{ $exp->numero_radicacion }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ Str::limit($exp->titulo, 60) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {{ $exp->estudiante }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('sustentacion.programar.show', $exp->id) }}"
                                           class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition">
                                            <i class="fas fa-calendar-check"></i> Programar
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
