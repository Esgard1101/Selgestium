<x-app-layout>
    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Encabezado --}}
            <div>
                <h2 class="text-2xl font-bold text-gray-800">FAI · RF-02.1 — Verificación de Créditos Académicos</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Ingresa los créditos que acredita el estudiante. El sistema evaluará contra el umbral
                    vigente y registrará el resultado en la bitácora FAI.
                </p>
            </div>

            {{-- Mensajes de validación (errores de FormRequest) --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ────────────────────────────────────────────────────
                 TARJETA DE RESULTADO (aparece tras el submit)
            ──────────────────────────────────────────────────── --}}
            @if (session('resultado_fai'))
                @php $r = session('resultado_fai'); @endphp

                @if ($r->estado === 'aprobado')
                    <div class="flex items-start gap-4 bg-green-50 border border-green-400 text-green-800 rounded-2xl px-6 py-5 shadow-sm">
                        <div class="text-4xl leading-none">✅</div>
                        <div>
                            <p class="text-lg font-bold">Verificación aprobada</p>
                            <p class="text-sm mt-1">
                                Créditos registrados: <strong>{{ $r->valor_obtenido }}</strong> —
                                Umbral: <strong>{{ $r->valor_umbral }}</strong>
                            </p>
                            <p class="text-xs text-green-600 mt-2">
                                Registrado el {{ \Carbon\Carbon::parse($r->verificado_at)->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-4 bg-red-50 border border-red-400 text-red-800 rounded-2xl px-6 py-5 shadow-sm">
                        <div class="text-4xl leading-none">❌</div>
                        <div>
                            <p class="text-lg font-bold">Verificación rechazada</p>
                            <p class="text-sm mt-1">
                                Créditos registrados: <strong>{{ $r->valor_obtenido }}</strong> —
                                Umbral mínimo: <strong>{{ $r->valor_umbral }}</strong>
                            </p>
                            <p class="text-xs text-red-600 mt-2">
                                El estudiante no cumple los créditos requeridos. El expediente
                                no puede avanzar hasta que subsane este requisito.
                            </p>
                        </div>
                    </div>
                @endif
            @endif

            {{-- ────────────────────────────────────────────────────
                 FORMULARIO
            ──────────────────────────────────────────────────── --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-6">
                <form action="{{ route('fai.creditos.store') }}" method="POST" id="form-verificacion-creditos">
                    @csrf

                    {{-- Selección de expediente --}}
                    <div class="mb-5">
                        <label for="expediente_id" class="block text-sm font-semibold text-gray-700 mb-1">
                            Expediente (Fase 2 — Verificar Requisitos)
                        </label>
                        @if ($expedientes->isEmpty())
                            <p class="text-sm text-yellow-600 bg-yellow-50 border border-yellow-300 rounded-lg px-4 py-3">
                                No hay expedientes pendientes en Fase 2.
                            </p>
                            <input type="hidden" name="expediente_id" value="">
                        @else
                            <select name="expediente_id" id="expediente_id"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700
                                       focus:outline-none focus:ring-2 focus:ring-blue-500
                                       @error('expediente_id') border-red-400 @enderror">
                                <option value="">— Selecciona un expediente —</option>
                                @foreach ($expedientes as $exp)
                                    <option value="{{ $exp->id }}"
                                        {{ (old('expediente_id', $expedienteIdPreseleccionado) == $exp->id) ? 'selected' : '' }}>
                                        {{ $exp->numero_radicacion }} · {{ Str::limit($exp->titulo, 55) }}
                                        ({{ trim($exp->estudiante) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('expediente_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    {{-- Umbral vigente (informativo) --}}
                    <div class="mb-5 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
                        <span class="font-semibold">Umbral vigente:</span>
                        mínimo <strong id="umbral-display">{{ $umbralGlobal }}</strong> créditos
                        <span class="text-xs text-blue-500">(parámetro CREDITOS_MINIMOS)</span>
                    </div>

                    {{-- Créditos ingresados --}}
                    <div class="mb-6">
                        <label for="creditos" class="block text-sm font-semibold text-gray-700 mb-1">
                            Créditos aprobados del estudiante
                        </label>
                        <input
                            type="number"
                            name="creditos"
                            id="creditos"
                            value="{{ old('creditos') }}"
                            min="0"
                            max="300"
                            placeholder="Ej. 175"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700
                                   focus:outline-none focus:ring-2 focus:ring-blue-500
                                   @error('creditos') border-red-400 @enderror"
                            required
                        >
                        @error('creditos')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">
                            Consulta el número en el expediente académico o portal DSA antes de ingresar.
                        </p>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}"
                           class="text-sm text-gray-500 hover:underline">
                            Cancelar
                        </a>
                        @if (!$expedientes->isEmpty())
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                                       py-2 px-5 rounded-lg transition duration-150 focus:outline-none
                                       focus:ring-2 focus:ring-blue-400">
                                Registrar verificación
                            </button>
                        @endif
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
