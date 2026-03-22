<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Asignación de Jurados
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 overflow-hidden shadow-xl sm:rounded-lg">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('expediente.asignar-jurados') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Expediente</label>
                        <select name="expediente_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Seleccione un expediente</option>
                            @foreach($expedientes as $expediente)
                                <option value="{{ $expediente->id }}">{{ $expediente->numero_radicacion }} - {{ $expediente->titulo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Jurados (Seleccione 3)</label>
                        @for($i = 0; $i < 3; $i++)
                            <select name="jurados[]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mb-2">
                                <option value="">Seleccione jurado {{ $i + 1 }}</option>
                                @foreach($personas as $persona)
                                    <option value="{{ $persona->id }}">{{ $persona->nombre }} {{ $persona->apellido }} ({{ $persona->dni }})</option>
                                @endforeach
                            </select>
                        @endfor
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-4">
                            Asignar Jurados
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
