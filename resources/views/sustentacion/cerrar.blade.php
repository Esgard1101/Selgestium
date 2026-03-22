<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cerrar Expediente y Registrar Acta: {{ $expediente->numero_radicacion }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-xl sm:rounded-lg border-t-4 border-red-500">
                <div class="mb-6">
                    <h3 class="text-lg font-bold">Título del Proyecto:</h3>
                    <p class="text-gray-600">{{ $expediente->titulo }}</p>
                </div>

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('sustentacion.cerrar.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="expediente_id" value="{{ $expediente->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-label for="numero_acta" value="Número de Acta" />
                            <x-input id="numero_acta" class="block mt-1 w-full" type="text" name="numero_acta" :value="old('numero_acta')" placeholder="Ex: ACTA-2026-001" required />
                        </div>

                        <div>
                            <x-label for="fecha_sustentacion" value="Fecha Real de Sustentación" />
                            <x-input id="fecha_sustentacion" class="block mt-1 w-full" type="datetime-local" name="fecha_sustentacion" :value="old('fecha_sustentacion')" required />
                        </div>
                    </div>

                    <div class="mb-6">
                        <x-label for="resultado" value="Resultado / Veredicto Final" />
                        <select id="resultado" name="resultado" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="Aprobado">Aprobado</option>
                            <option value="Aprobado con Mención">Aprobado con Mención</option>
                            <option value="Desaprobado">Desaprobado</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <x-label for="observaciones" value="Observaciones Finales (Opcional)" />
                        <textarea id="observaciones" name="observaciones" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('observaciones') }}</textarea>
                    </div>

                    <div class="p-4 bg-yellow-50 rounded-lg mb-6 border border-yellow-200">
                        <p class="text-yellow-800 text-sm font-bold">⚠️ ATENCIÓN:</p>
                        <p class="text-yellow-700 text-xs">Al cerrar el expediente, no se podrán realizar más cambios, asignaciones u observaciones. Esta acción es irreversible.</p>
                    </div>

                    <div class="flex items-center justify-end">
                        <x-button class="bg-red-600 hover:bg-red-700">
                            Cerrar Expediente Definitivamente
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
