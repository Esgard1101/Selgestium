<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="fa-solid fa-folder-open mr-2 text-[var(--color-primary)]"></i> Mis Expedientes
            </h2>
            <a href="{{ route('pur.create') }}" class="inline-flex items-center px-4 py-2 bg-[var(--color-primary)] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 shadow-md cursor-pointer transition-colors">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Expediente
            </a>
        </div>

        {{-- Mostrar mensajes de Éxito o Error desde la sesión (Flash Messages) --}}
        @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Éxito!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Uy!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif
        @if (isset($errorMessage))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Error del Sistema!</strong>
            <span class="block sm:inline">{{ $errorMessage }}</span>
        </div>
        @endif

        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            N° Radicación
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Título
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Fase Actual
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Fecha
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expedientes as $expediente)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            <p class="text-gray-900 font-semibold">{{ $expediente->numero_radicacion }}</p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            <p class="text-gray-900 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs" title="{{ $expediente->titulo }}">
                                {{ $expediente->titulo }}
                            </p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                Fase {{ $expediente->fase_actual }}
                            </span>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            @php
                            $colorEstado = match($expediente->estado) {
                            'aprobado' => 'bg-green-100 text-green-800',
                            'rechazado' => 'bg-red-100 text-red-800',
                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                            default => 'bg-gray-100 text-gray-800'
                            };
                            @endphp
                            <span class="{{ $colorEstado }} text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ ucfirst($expediente->estado) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm">
                            <p class="text-gray-900 whitespace-nowrap">
                                {{ $expediente->created_at->format('d/m/Y') }}
                            </p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-200 text-sm text-center">
                            <a href="{{ route('pur.show', $expediente->id) }}" class="text-blue-600 hover:text-blue-900 mx-1" title="Ver detalle">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300 block"></i>
                            No tienes expedientes radicados aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginación nativa de Laravel --}}
            @if($expedientes->hasPages())
            <div class="mt-4">
                {{ $expedientes->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>