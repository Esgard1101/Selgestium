<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Programar Sustentación: {{ $expediente->numero_radicacion }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-xl sm:rounded-lg">
                <div class="mb-6">
                    <h3 class="text-lg font-bold">Título:</h3>
                    <p class="text-gray-600">{{ $expediente->titulo }}</p>
                </div>

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('sustentacion.programar.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="expediente_id" value="{{ $expediente->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-label for="fecha" value="Fecha Sugerida (Mínimo 7 días hábiles)" />
                            <x-input id="fecha" class="block mt-1 w-full" type="date" name="fecha" value="{{ old('fecha') }}" required />
                        </div>

                        <div>
                            <x-label for="hora" value="Hora" />
                            <x-input id="hora" class="block mt-1 w-full" type="time" name="hora" value="{{ old('hora') }}" required />
                        </div>
                    </div>

                    <div class="mb-6">
                        <x-label for="lugar" value="Lugar / Aula / Enlace Virtual" />
                        <x-input id="lugar" class="block mt-1 w-full" type="text" name="lugar" value="{{ old('lugar') }}" placeholder="Ej: Aula Magna o Google Meet Link" required />
                    </div>

                    <div class="flex items-center justify-end">
                        <x-button>
                            Confirmar Programación
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
