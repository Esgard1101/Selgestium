<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Resolución de Designación: {{ $numero_resolucion }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-12 shadow-xl sm:rounded-lg font-serif leading-relaxed text-gray-900 border border-gray-200">
                <!-- Encabezado -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold uppercase">{{ $universidad }}</h1>
                    <h2 class="text-xl font-semibold">{{ $facultad }}</h2>
                    <div class="mt-4 border-b-2 border-black w-24 mx-auto"></div>
                </div>

                <!-- Título Resolution -->
                <div class="text-center mb-10">
                    <h3 class="text-lg font-bold">RESOLUCIÓN DE DECANATO N° {{ $numero_resolucion }}</h3>
                    <p class="text-sm italic">Lambayeque, {{ $fecha_emision }}</p>
                </div>

                <!-- Cuerpo -->
                <div class="mb-6">
                    <p class="font-bold mb-2">VISTO:</p>
                    <p class="text-justify mb-4">
                        El expediente N° <strong>{{ $expediente->numero_radicacion }}</strong> presentado por el graduando 
                        <strong>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</strong>, quien solicita la designación de jurados 
                        para su trabajo de investigación titulado: <em>"{{ $expediente->titulo }}"</em>.
                    </p>

                    <p class="font-bold mb-2">CONSIDERANDO:</p>
                    <p class="text-justify mb-4">
                        Que, de acuerdo al reglamento de grados y títulos de la {{ $facultad }}, es necesario designar a los miembros del jurado 
                        calificador que se encargarán de la evaluación del proyecto de tesis.
                    </p>

                    <p class="font-bold mb-2">SE RESUELVE:</p>
                    <p class="mb-4">
                        <strong>ARTÍCULO PRIMERO.-</strong> DESIGNAR a los miembros del Jurado Calificador para el expediente de referencia, 
                        el mismo que queda conformado de la siguiente manera:
                    </p>

                    <div class="ml-8 mb-6">
                        <ul class="list-decimal">
                            @foreach($jurados as $jurado)
                                <li class="mb-1"><strong>{{ $jurado->nombre }} {{ $jurado->apellido }}</strong> (DNI: {{ $jurado->dni }})</li>
                            @endforeach
                        </ul>
                    </div>

                    <p class="text-justify mb-6">
                        <strong>ARTÍCULO SEGUNDO.-</strong> DISPONER que el jurado designado proceda con la evaluación conforme a los plazos 
                        establecidos en la normativa institucional vigente.
                    </p>
                </div>

                <!-- Firmas -->
                <div class="mt-20 flex justify-between px-10">
                    <div class="text-center">
                        <div class="border-t border-black w-48 mb-2"></div>
                        <p class="text-xs font-bold uppercase">Secretaría Académica</p>
                    </div>
                    <div class="text-center">
                        <div class="border-t border-black w-48 mb-2"></div>
                        <p class="text-xs font-bold uppercase">Decanato - {{ $facultad }}</p>
                    </div>
                </div>

                <!-- Botón de impresión (oculto al imprimir) -->
                <div class="mt-12 text-center print:hidden">
                    <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-black transition">
                        Imprimir Resolución
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
