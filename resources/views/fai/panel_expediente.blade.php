<x-app-layout>
    @php
        /**
         * Etiquetas descriptivas por código FAI.
         */
        $labels = [
            'RF02.1' => ['nombre' => 'Créditos Académicos',         'icono' => 'fas fa-graduation-cap', 'fase' => 2],
            'RF02.2' => ['nombre' => 'Voucher Pago Proyecto',        'icono' => 'fas fa-receipt',         'fase' => 2],
            'RF02.3' => ['nombre' => 'Grado de Bachiller',           'icono' => 'fas fa-certificate',     'fase' => 8],
            'RF02.4' => ['nombre' => 'Voucher Pago Sustentación',    'icono' => 'fas fa-receipt',         'fase' => 8],
            'RF02.5' => ['nombre' => 'Identidad RENIEC',             'icono' => 'fas fa-id-card',         'fase' => 2],
            'RF02.6' => ['nombre' => 'Similitud Turnitin',           'icono' => 'fas fa-shield-halved',   'fase' => 8],
        ];

        /**
         * Rutas de verificación por código FAI.
         * null = ruta aún no implementada (botón deshabilitado).
         */
        $verificarRoutes = [
            'RF02.1' => 'fai.creditos.show',   // F-06 ✅
            'RF02.2' => null,                  // F-11 pendiente (Jesus)
            'RF02.3' => null,                  // F-14 pendiente (César)
            'RF02.4' => null,                  // F-14 pendiente (César)
            'RF02.5' => null,                  // F-16 pendiente (Esgardo)
            'RF02.6' => null,                  // F-16 pendiente (Esgardo)
        ];

        /**
         * Estilos semafóricos por estado.
         */
        $estilos = [
            'aprobado'        => ['border' => '#00C48C', 'bg' => '#E6FAF5', 'text' => '#00C48C', 'badge_bg' => '#E6FAF5', 'badge_text' => '#006B4F', 'icono' => 'fas fa-circle-check'],
            'rechazado'       => ['border' => '#FF4C4C', 'bg' => '#FFF0F0', 'text' => '#FF4C4C', 'badge_bg' => '#FFF0F0', 'badge_text' => '#991B1B', 'icono' => 'fas fa-circle-xmark'],
            'error'           => ['border' => '#FF4C4C', 'bg' => '#FFF0F0', 'text' => '#FF4C4C', 'badge_bg' => '#FFF0F0', 'badge_text' => '#991B1B', 'icono' => 'fas fa-triangle-exclamation'],
            'pendiente'       => ['border' => '#FFAA00', 'bg' => '#FFF8E1', 'text' => '#FFAA00', 'badge_bg' => '#FFF8E1', 'badge_text' => '#92400E', 'icono' => 'fas fa-clock'],
            'fallback_manual' => ['border' => '#FFAA00', 'bg' => '#FFF8E1', 'text' => '#FFAA00', 'badge_bg' => '#FFF8E1', 'badge_text' => '#92400E', 'icono' => 'fas fa-hand'],
            'no_aplica'       => ['border' => '#8B95A5', 'bg' => '#F1F3F7', 'text' => '#8B95A5', 'badge_bg' => '#F1F3F7', 'badge_text' => '#4B5563', 'icono' => 'fas fa-minus-circle'],
        ];

        $estadoLabels = [
            'aprobado'        => 'Aprobado',
            'rechazado'       => 'Rechazado',
            'error'           => 'Error API',
            'pendiente'       => 'Pendiente',
            'fallback_manual' => 'Manual requerido',
            'no_aplica'       => 'Sin verificar',
        ];
    @endphp

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Encabezado + volver --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <a href="{{ route('fai.panel.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-medium mb-3 transition-colors duration-150"
                       style="color: var(--color-text-secondary);"
                       onmouseover="this.style.color='var(--color-primary)'"
                       onmouseout="this.style.color='var(--color-text-secondary)'">
                        <i class="fas fa-arrow-left"></i> Volver a la bandeja
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-filter mr-2" style="color: var(--color-primary);"></i>
                        Panel FAI
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Estado de los 6 filtros administrativos para este expediente.
                    </p>
                </div>
            </div>

            {{-- Datos del expediente --}}
            <div class="bg-white rounded-2xl shadow-sm px-6 py-4 border border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-text-muted);">Radicación</p>
                        <p class="font-mono font-semibold text-gray-800 mt-0.5">{{ $expediente->numero_radicacion }}</p>
                    </div>
                    <div class="lg:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-text-muted);">Título</p>
                        <p class="font-medium text-gray-800 mt-0.5 line-clamp-2">{{ $expediente->titulo }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-text-muted);">Estudiante</p>
                        <p class="font-medium text-gray-800 mt-0.5">{{ $expediente->estudiante }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--color-text-muted);">Fase actual</p>
                        <span class="inline-flex items-center mt-0.5 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                              style="background-color: var(--color-accent); color: var(--color-primary);">
                            Fase {{ $expediente->fase_actual }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Grid de 6 tarjetas FAI --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($resultados as $item)
                    @php
                        $codigo    = $item->fai_codigo;
                        $resultado = $item->resultado;
                        $estado    = $resultado?->estado ?? 'no_aplica';
                        $estilo    = $estilos[$estado] ?? $estilos['no_aplica'];
                        $meta      = $labels[$codigo];
                        $rutaNombre = $verificarRoutes[$codigo] ?? null;
                        $puedeVerificar = $rutaNombre !== null && $estado !== 'aprobado';
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border-2 transition-shadow duration-150 hover:shadow-md"
                         style="border-color: {{ $estilo['border'] }}; background-color: {{ $estilo['bg'] }};">

                        {{-- Cabecera de la tarjeta --}}
                        <div class="px-5 pt-5 pb-3 flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center"
                                     style="background-color: {{ $estilo['border'] }}20;">
                                    <i class="{{ $meta['icono'] }} text-sm" style="color: {{ $estilo['border'] }};"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $codigo }}</p>
                                    <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $meta['nombre'] }}</p>
                                </div>
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background-color: {{ $estilo['badge_bg'] }};
                                         color: {{ $estilo['badge_text'] }};
                                         border: 1px solid {{ $estilo['border'] }}40;">
                                <i class="{{ $estilo['icono'] }} text-xs"></i>
                                {{ $estadoLabels[$estado] }}
                            </span>
                        </div>

                        {{-- Cuerpo: detalles del resultado --}}
                        <div class="px-5 pb-4 space-y-2 text-xs text-gray-600">
                            @if ($resultado)
                                @if ($resultado->valor_obtenido || $resultado->valor_umbral)
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-semibold text-gray-500">Obtenido:</span>
                                        <span class="font-mono font-bold" style="color: {{ $estilo['border'] }};">
                                            {{ $resultado->valor_obtenido ?? '—' }}
                                        </span>
                                        @if ($resultado->valor_umbral)
                                            <span class="text-gray-400">/ umbral:</span>
                                            <span class="font-mono">{{ $resultado->valor_umbral }}</span>
                                        @endif
                                    </div>
                                @endif

                                @if ($resultado->validado_por && trim($resultado->validado_por))
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-user-check text-gray-400"></i>
                                        <span>{{ trim($resultado->validado_por) }}</span>
                                    </div>
                                @endif

                                @if ($resultado->verificado_at)
                                    <div class="flex items-center gap-1.5 text-gray-400">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ \Carbon\Carbon::parse($resultado->verificado_at)->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif

                                @if ($estado === 'rechazado' && $resultado->motivorechazo_id)
                                    <div class="mt-2 flex items-start gap-1.5 rounded-lg px-3 py-2"
                                         style="background-color: #FFF0F0; border: 1px solid #FF4C4C40;">
                                        <i class="fas fa-circle-exclamation text-red-500 mt-0.5 flex-shrink-0"></i>
                                        <span class="text-red-700 font-medium">
                                            Motivo de rechazo #{{ $resultado->motivorechazo_id }}
                                        </span>
                                    </div>
                                @endif
                            @else
                                <p class="text-gray-400 italic">Este filtro aún no ha sido ejecutado.</p>
                            @endif
                        </div>

                        {{-- Footer: botón Verificar --}}
                        <div class="px-5 pb-5">
                            @if ($estado === 'aprobado')
                                <div class="flex items-center gap-1.5 text-xs font-semibold"
                                     style="color: var(--color-success);">
                                    <i class="fas fa-circle-check"></i>
                                    Verificado correctamente
                                </div>
                            @elseif ($puedeVerificar)
                                <a href="{{ route($rutaNombre, ['expediente_id' => $expediente->id]) }}"
                                   class="inline-flex items-center gap-1.5 text-xs font-semibold px-4 py-1.5
                                          rounded-lg transition-colors duration-150 text-white"
                                   style="background-color: var(--color-primary);"
                                   onmouseover="this.style.backgroundColor='var(--color-primary-dark)'"
                                   onmouseout="this.style.backgroundColor='var(--color-primary)'">
                                    <i class="fas fa-pen-to-square"></i>
                                    Verificar
                                </a>
                            @else
                                <button type="button"
                                        disabled
                                        title="Módulo de verificación disponible próximamente"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold px-4 py-1.5
                                               rounded-lg cursor-not-allowed opacity-50"
                                        style="background-color: var(--color-neutral-bg); color: var(--color-text-muted);">
                                    <i class="fas fa-lock"></i>
                                    Verificar
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
