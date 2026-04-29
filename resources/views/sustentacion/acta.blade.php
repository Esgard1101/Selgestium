<x-app-layout>
    {{-- Estilos Premium Inspirados en la Guía --}}
    <style>
        :root {
            --blue-deep: #001D5E;
            --blue-mid: #0033A0;
            --blue-bright: #0055D4;
            --yellow: #FFD000;
            --white: #FFFFFF;
            --gray-light: #F4F6FB;
            --gray-mid: #B8C4D8;
            --line: #E2E8F0;
            --text-dark: #0A1628;
            --muted: #64748B;
            --success: #00C48C;
            --warning: #FF9F43;
            --danger: #FF6B6B;
        }

        .page-header {
            background: linear-gradient(135deg, #002B80 0%, var(--blue-deep) 62%);
            padding: 28px 34px 36px;
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            margin-bottom: 24px;
        }
        .page-header::before {
            content: 'ACT';
            position: absolute;
            right: -12px;
            top: -18px;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 156px;
            color: rgba(255, 208, 0, 0.055);
            letter-spacing: -8px;
            pointer-events: none;
        }
        .breadcrumb { font-size: 12px; color: var(--gray-mid); margin-bottom: 6px; }
        .breadcrumb span { color: var(--yellow); }
        .page-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 27px; color: var(--white); margin-bottom: 5px; }
        .page-desc { font-size: 13px; color: var(--gray-mid); max-width: 710px; line-height: 1.5; }

        .workspace {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(330px, 0.75fr);
            gap: 22px;
            align-items: start;
        }

        .card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0, 30, 90, 0.07); overflow: hidden; border: 1px solid var(--line); }
        .card-header { padding: 18px 22px 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .card-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 14px; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 16px 22px 22px; }

        .expediente-band {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 18px;
            align-items: center;
            background: linear-gradient(135deg, #F0F5FF, #FFFFFF);
            border: 1px solid #C8D8FF;
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 20px;
        }
        .exp-title { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 800; color: var(--blue-deep); margin-bottom: 5px; }
        .exp-meta { display: flex; flex-wrap: wrap; gap: 8px 14px; color: var(--muted); font-size: 12px; }
        .exp-meta strong { color: var(--text-dark); }
        
        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 24px;
            background: var(--success);
            color: white;
            font-size: 12px;
            font-weight: 800;
            padding: 8px 12px;
            white-space: nowrap;
        }
        .status-chip.pendiente {
            background: var(--warning);
        }

        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .field { display: flex; flex-direction: column; gap: 7px; }
        .field.full { grid-column: 1 / -1; }
        label { font-size: 12px; font-weight: 800; color: var(--text-dark); }
        input, select, textarea {
            width: 100%;
            border: 1.5px solid var(--line);
            background: white;
            border-radius: 10px;
            color: var(--text-dark);
            font-size: 13px;
            outline: none;
            transition: all 0.2s;
        }
        input, select { height: 42px; padding: 0 12px; }
        textarea { min-height: 110px; resize: vertical; padding: 12px; line-height: 1.45; }
        input:focus, select:focus, textarea:focus {
            border-color: var(--blue-bright);
            box-shadow: 0 0 0 3px rgba(0, 85, 212, 0.10);
        }

        .score-summary { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 18px 0; }
        .avg-card { border-radius: 14px; padding: 18px; background: linear-gradient(135deg, #001D5E, #0033A0); color: white; }
        .avg-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: rgba(255, 255, 255, 0.55); margin-bottom: 8px; }
        .avg-value { font-family: 'Syne', sans-serif; font-size: 42px; font-weight: 800; color: var(--yellow); line-height: 1; }
        .result-card { border-radius: 14px; padding: 18px; background: linear-gradient(135deg, #EDFFF8, #CCFFF0); border: 1px solid #A0F0D0; }
        .result-label { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #00875A; margin-bottom: 8px; }
        .result-value { font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 800; color: #006A3A; }

        .rule-list { display: flex; flex-direction: column; gap: 10px; }
        .rule-item { display: flex; align-items: flex-start; gap: 10px; border-radius: 12px; padding: 12px; background: #F8FAFC; border: 1px solid #EDF2F7; }
        .rule-item.ok { background: #EDFFF8; border-color: #A0F0D0; }
        .rule-item.warn { background: #FFFBEC; border-color: #FFE099; }
        .rule-item.lock { background: #FFF5F5; border-color: #FFCCCC; }
        .rule-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex: none; color: white; }
        .rule-item.ok .rule-icon { background: var(--success); }
        .rule-item.warn .rule-icon { background: var(--warning); }
        .rule-item.lock .rule-icon { background: var(--danger); }
        .rule-title { font-size: 12px; font-weight: 800; }
        .rule-text { font-size: 11px; color: var(--muted); line-height: 1.45; margin-top: 2px; }

        .readonly-panel { border-radius: 14px; background: linear-gradient(135deg, #EDFFF8, #FFFFFF); border: 1px solid #A0F0D0; padding: 16px; margin-top: 16px; }
        .readonly-title { display: flex; align-items: center; gap: 8px; color: #00875A; font-size: 13px; font-weight: 800; margin-bottom: 6px; }
        .readonly-text { font-size: 12px; color: #376B59; line-height: 1.5; }

        .btn-submit {
            width: 100%;
            background: var(--yellow);
            color: var(--blue-deep);
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(255, 208, 0, 0.2);
        }
        .btn-submit:hover { background: #F5C400; transform: translateY(-1px); }
    </style>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        
        {{-- Encabezado Estilizado --}}
        <div class="page-header">
            <div class="breadcrumb">Inicio › Sustentación › <span>Acta y Cierre</span></div>
            <div class="page-title">Acta de Sustentación</div>
            <div class="page-desc">Registro de calificaciones, cálculo de promedio y cierre irreversible del expediente estudiantil.</div>
        </div>

        <div class="workspace">
            {{-- Panel Principal: Formulario o Vista ReadOnly --}}
            <section class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-file-signature text-blue-mid"></i> 
                        Datos de Evaluación Final
                    </div>
                </div>
                <div class="card-body">
                    
                    {{-- Banda del Expediente --}}
                    <div class="expediente-band">
                        <div>
                            <div class="exp-title">{{ $expediente->numero_radicacion }} · {{ $expediente->titulo }}</div>
                            <div class="exp-meta">
                                <span>Estudiante: <strong>{{ $expediente->estudiante->nombre ?? 'N/A' }} {{ $expediente->estudiante->apellido ?? '' }}</strong></span>
                                <span>Modalidad: <strong>{{ ucfirst($sustentacion->modalidad ?? 'N/A') }}</strong></span>
                                <span>Lugar: <strong>{{ $sustentacion->lugar ?? 'N/A' }}</strong></span>
                            </div>
                        </div>
                        @if($expediente->estado === 'cerrado')
                            <span class="status-chip"><i class="fas fa-circle-check"></i> Cerrado</span>
                        @else
                            <span class="status-chip pendiente"><i class="fas fa-clock"></i> Pendiente</span>
                        @endif
                    </div>

                    @if($expediente->estado !== 'cerrado')
                        {{-- MODO EDICIÓN --}}
                        <form action="{{ route('sustentacion.cerrar.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="sustentacion_id" value="{{ $sustentacion->id ?? '' }}">

                            <div class="form-grid">
                                <div class="field">
                                    <label for="nota1">Nota Jurado 1</label>
                                    <input id="nota1" type="number" name="nota1" min="0" max="20" step="0.1" required @if(!$esPresidente) readonly @endif>
                                    <span class="text-xs text-gray-400">Presidente: {{ $nombrePresidente }}</span>
                                </div>
                                <div class="field">
                                    <label for="nota2">Nota Jurado 2</label>
                                    <input id="nota2" type="number" name="nota2" min="0" max="20" step="0.1" required @if(!$esPresidente) readonly @endif>
                                    <span class="text-xs text-gray-400">Secretario: {{ $nombreSecretario }}</span>
                                </div>
                                <div class="field">
                                    <label for="nota3">Nota Jurado 3</label>
                                    <input id="nota3" type="number" name="nota3" min="0" max="20" step="0.1" required @if(!$esPresidente) readonly @endif>
                                    <span class="text-xs text-gray-400">Vocal: {{ $nombreVocal }}</span>
                                </div>
                                
                                <div class="field">
                                    <label for="resultado">Veredicto</label>
                                    <select id="resultado" name="resultado" required @if(!$esPresidente) disabled @endif>
                                        <option value="">-- Seleccionar --</option>
                                        <option value="aprobado">Aprobado</option>
                                        <option value="desaprobado">Desaprobado</option>
                                    </select>
                                </div>

                                <div class="field">
                                    <label for="firma">Firma del Presidente</label>
                                    <input id="firma" type="text" value="Pendiente de cierre" readonly>
                                </div>

                                <div class="field">
                                    <label for="acta">URL del Acta</label>
                                    <input id="acta" type="text" value="Generado tras el cierre" readonly>
                                </div>
                                
                                <div class="field full">
                                    <label for="observaciones">Observaciones del Acta</label>
                                    <textarea id="observaciones" name="observaciones" placeholder="Recomendaciones o descargos..." @if(!$esPresidente) readonly @endif></textarea>
                                </div>
                            </div>

                            @if($esPresidente)
                                <div class="mt-6">
                                    <button type="submit" class="btn-submit">
                                        <i class="fas fa-lock"></i> Guardar y Cerrar Expediente
                                    </button>
                                </div>
                            @endif
                        </form>
                    @else
                        {{-- MODO LECTURA --}}
                        <div class="form-grid">
                            <div class="field">
                                <label>Nota Jurado 1</label>
                                <input type="text" value="{{ $acta->nota_jurado1 }}" readonly>
                                <span class="text-xs text-gray-400">Presidente: {{ $nombrePresidente }}</span>
                            </div>
                            <div class="field">
                                <label>Nota Jurado 2</label>
                                <input type="text" value="{{ $acta->nota_jurado2 }}" readonly>
                                <span class="text-xs text-gray-400">Secretario: {{ $nombreSecretario }}</span>
                            </div>
                            <div class="field">
                                <label>Nota Jurado 3</label>
                                <input type="text" value="{{ $acta->nota_jurado3 }}" readonly>
                                <span class="text-xs text-gray-400">Vocal: {{ $nombreVocal }}</span>
                            </div>
                            
                            <div class="field">
                                <label>Resultado</label>
                                <input type="text" value="{{ ucfirst($acta->resultado) }}" readonly>
                            </div>

                            <div class="field">
                                <label>Firma del Presidente</label>
                                <input type="text" value="{{ $acta->created_at ?? 'N/A' }}" readonly>
                            </div>

                            <div class="field">
                                <label>URL del Acta</label>
                                <input type="text" value="/storage/actas/ACT-SUS-{{ $acta->id }}.pdf" readonly>
                            </div>

                            <div class="field full">
                                <label>Observaciones Registradas</label>
                                <textarea readonly>{{ $acta->observaciones_acta ?? 'Ninguna' }}</textarea>
                            </div>
                        </div>

                        <div class="score-summary">
                            <div class="avg-card">
                                <div class="avg-label">Promedio Calculado</div>
                                <div class="avg-value">{{ $acta->nota_promedio }}</div>
                            </div>
                            <div class="result-card">
                                <div class="result-label">Resultado Final</div>
                                <div class="result-value uppercase" style="color: {{ strtolower($acta->resultado) === 'aprobado' ? '#006A3A' : '#9B1C1C' }}">
                                    {{ $acta->resultado }}
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </section>

            {{-- Barra Lateral: Reglas y Validaciones --}}
            <aside class="space-y-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title"><i class="fas fa-shield-halved text-blue-mid"></i> Reglas de Cierre</div>
                    </div>
                    <div class="card-body">
                        <div class="rule-list">
                            <div class="rule-item ok">
                                <div class="rule-icon"><i class="fas fa-calculator"></i></div>
                                <div>
                                    <div class="rule-title">Promedio en Service</div>
                                    <div class="rule-text">`nota_promedio` calculada en backend, no alterable por el cliente.</div>
                                </div>
                            </div>
                            
                            <div class="rule-item warn">
                                <div class="rule-icon"><i class="fas fa-user-shield"></i></div>
                                <div>
                                    <div class="rule-title">Acción Autorizada</div>
                                    <div class="rule-text">Solo el **Presidente del Jurado** puede finalizar el proceso.</div>
                                </div>
                            </div>

                            @if($expediente->estado === 'cerrado')
                                <div class="rule-item lock">
                                    <div class="rule-icon"><i class="fas fa-ban"></i></div>
                                    <div>
                                        <div class="rule-title">Bloqueo Permanente</div>
                                        <div class="rule-text">Los expedientes cerrados no admiten cambios retroactivos.</div>
                                    </div>
                                </div>
                                
                                <div class="readonly-panel">
                                    <div class="readonly-title"><i class="fas fa-circle-info"></i> Modo Solo Lectura</div>
                                    <div class="readonly-text">Esta acta fue confirmada satisfactoriamente.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </aside>
        </div>

    </div>
</x-app-layout>
