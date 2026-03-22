<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Registro de Observaciones
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

                <div id="deadline-alert" class="hidden bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                    <strong>⚠️ ¡Atención!</strong> El plazo de 15 días para este expediente ha vencido.
                </div>

                <div id="vencidos-data" data-vencidos="{{ json_encode($vencidos ?? []) }}" class="hidden"></div>

                <form action="{{ route('expediente.observaciones.registrar') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Expediente</label>
                            <select id="expediente_id" name="expediente_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Seleccione un expediente</option>
                                @foreach($expedientes as $expediente)
                                <option value="{{ $expediente->id }}">{{ $expediente->numero_radicacion }} - {{ $expediente->titulo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jurado</label>
                            <select name="jurado_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Seleccione el jurado</option>
                                @foreach($jurados as $jurado)
                                <option value="{{ $jurado->id }}">{{ $jurado->nombre }} {{ $jurado->apellido }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Ronda</label>
                            <input type="number" name="ronda" value="1" min="1" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Veredicto</label>
                            <select name="tipo_veredicto" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="observado">Observado (Requiere subsanación)</option>
                                <option value="aprobado">Aprobado (Conforme)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Descripción de la Observación</label>
                        <textarea name="descripcion" rows="4" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Escriba aquí sus observaciones detalladas..."></textarea>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-4">
                            Registrar Observación
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataElement = document.getElementById('vencidos-data');
            const vencidos = JSON.parse(dataElement.dataset.vencidos || '[]');
            const alertBox = document.getElementById('deadline-alert');
            const expedienteSelect = document.getElementById('expediente_id');

            if (expedienteSelect && alertBox) {
                expedienteSelect.addEventListener('change', function() {
                    const id = parseInt(this.value);
                    if (vencidos.includes(id)) {
                        alertBox.classList.remove('hidden');
                    } else {
                        alertBox.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-app-layout>