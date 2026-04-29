<x-app-layout>
    <div class="p-6 max-w-7xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fa-solid fa-folder-open mr-2 text-indigo-600"></i>
                    Expediente: {{ $expediente->numero_radicacion }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Radicado el {{ $expediente->created_at->format('d/m/Y') }}
                </p>
            </div>
            <a href="{{ route('pur.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">

            <div class="border-b border-gray-200 bg-gray-50">
                <nav class="flex -mb-px" id="tabs-nav">
                    <button onclick="changeTab('info')" id="tab-info" class="tab-btn w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm text-indigo-600 border-indigo-600 bg-white">
                        <i class="fa-solid fa-circle-info mr-2"></i> Información
                    </button>
                    <button onclick="changeTab('docs')" id="tab-docs" class="tab-btn w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300">
                        <i class="fa-solid fa-file-pdf mr-2"></i> Documentos
                    </button>
                    <button onclick="changeTab('fai')" id="tab-fai" class="tab-btn w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300">
                        <i class="fa-solid fa-clipboard-check mr-2"></i> FAI
                    </button>
                    <button onclick="changeTab('timeline')" id="tab-timeline" class="tab-btn w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300">
                        <i class="fa-solid fa-clock-rotate-left mr-2"></i> Timeline
                    </button>
                </nav>
            </div>

            <div class="p-6">

                <div id="content-info" class="tab-content block">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Detalles del Proyecto</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Título del Proyecto</p>
                            <p class="text-gray-900">{{ $expediente->titulo }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Fase Actual</p>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Fase {{ $expediente->fase_actual }}</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-semibold">Estado</p>
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ ucfirst($expediente->estado) }}</span>
                        </div>
                    </div>
                </div>

                <div id="content-docs" class="tab-content hidden">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Documentos Adjuntos</h3>
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex items-center">
                            <i class="fa-solid fa-file-pdf text-red-500 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Proyecto_Radicado.pdf</p>
                                <p class="text-xs text-gray-500">Subido el {{ $expediente->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>

                        {{-- ✅ Botón dinámico conectado a la ruta de descarga --}}
                        <a href="{{ route('pur.descargar', $expediente->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">
                            Descargar <i class="fa-solid fa-download ml-1"></i>
                        </a>
                    </div>
                </div>

                <div id="content-fai" class="tab-content hidden">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Ficha de Aprobación Inicial (FAI)</h3>
                    <div class="p-8 text-center text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                        <i class="fa-solid fa-clipboard-question text-4xl mb-3 text-gray-400 block"></i>
                        <p>La FAI aún no ha sido generada o evaluada para este expediente.</p>
                    </div>
                </div>

                <div id="content-timeline" class="tab-content hidden">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Historial del Expediente</h3>

                    <div class="relative border-l border-gray-200 ml-3 mt-4">

                        @forelse($expediente->historialFases as $nodo)
                        <div class="mb-8 ml-6 relative">

                            {{-- Icono dinámico --}}
                            @php
                            $color = match($nodo->estadoexpediente_id ?? null) {
                            1 => 'bg-green-100 text-green-500', // aprobado
                            2 => 'bg-red-100 text-red-500', // rechazado
                            default => 'bg-blue-100 text-blue-500' // pendiente
                            };

                            $icon = match($nodo->estadoexpediente_id ?? null) {
                            1 => 'fa-check',
                            2 => 'fa-xmark',
                            default => 'fa-clock'
                            };
                            @endphp

                            <span class="absolute flex items-center justify-center w-6 h-6 {{ $color }} rounded-full -left-9 ring-8 ring-white">
                                <i class="fa-solid {{ $icon }} text-xs"></i>
                            </span>

                            {{-- Fase --}}
                            <h4 class="font-bold text-gray-900">
                                Fase {{ $nodo->fase_id }}
                            </h4>

                            {{-- Fecha --}}
                            <p class="text-sm text-gray-500">
                                Movimiento registrado el {{ $nodo->created_at->format('d/m/Y H:i A') }}
                            </p>

                            {{-- Actor + IP --}}
                            <div class="mt-1 flex items-center gap-3 text-xs text-gray-400 font-mono">
                                <span title="Actor ID">
                                    <i class="fa-solid fa-user-tag"></i> ID: {{ $nodo->actor_id }}
                                </span>

                                @if($nodo->ip_actor)
                                <span title="Dirección IP">
                                    <i class="fa-solid fa-network-wired"></i> IP: {{ $nodo->ip_actor }}
                                </span>
                                @endif
                            </div>

                            {{-- Observación --}}
                            @if($nodo->observacion)
                            <p class="text-sm text-gray-700 mt-2 bg-gray-50 border border-gray-100 p-3 rounded-md italic">
                                "{{ $nodo->observacion }}"
                            </p>
                            @endif

                        </div>
                        @empty
                        <div class="text-gray-500 text-sm ml-6 italic">
                            No hay historial registrado aún.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function changeTab(tabId) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('block');
            });
            // Mostrar el contenido seleccionado
            document.getElementById('content-' + tabId).classList.remove('hidden');
            document.getElementById('content-' + tabId).classList.add('block');

            // Resetear estilos de todos los botones
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = "tab-btn w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300";
            });

            // Aplicar estilo activo al botón seleccionado
            const activeBtn = document.getElementById('tab-' + tabId);
            activeBtn.className = "tab-btn w-1/4 py-4 px-1 text-center border-b-2 font-medium text-sm text-indigo-600 border-indigo-600 bg-white";
        }
    </script>
</x-app-layout>