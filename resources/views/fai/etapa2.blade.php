<x-app-layout>
    <!-- Inject Lucide Icons Script -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- Custom Mockup CSS -->
    <style>
        :root {
            --blue-deep: var(--color-primary-dark);
            --blue-mid: var(--color-primary);
            --blue-bright: var(--color-primary-light);
            --yellow: var(--color-accent);
            --white: var(--color-surface);
            --gray-light: var(--color-bg);
            --gray-mid: var(--color-border);
            --line: var(--color-border);
            --text-dark: var(--color-text-primary);
            --muted: var(--color-text-secondary);
            --success: var(--color-success);
            --warning: var(--color-warning);
            --danger: var(--color-danger);
        }

        .page-header { background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 62%); padding: 28px 34px 36px; position: relative; overflow: hidden; border-radius: 16px; margin-bottom: 24px;}
        .page-header::before { content: 'F14'; position: absolute; right: -12px; top: -18px; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 156px; color: rgba(255, 208, 0, 0.055); letter-spacing: -8px; }
        .breadcrumb { font-size: 12px; color: var(--gray-mid); margin-bottom: 6px; }
        .breadcrumb span { color: var(--yellow); }
        .page-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 27px; color: var(--white); margin-bottom: 5px; }
        .page-desc { font-size: 13px; color: var(--gray-mid); max-width: 700px; line-height: 1.5; }
        .rf-badge { display: inline-block; margin-top: 10px; background: var(--yellow); color: var(--blue-deep); font-family: 'Syne', sans-serif; font-weight: 800; font-size: 11px; letter-spacing: 1px; padding: 4px 14px; border-radius: 20px; }

        .stat-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 22px; }
        .stat-box { background: var(--color-surface); border-radius: 14px; padding: 18px; text-align: center; box-shadow: 0 2px 8px rgba(0, 30, 90, 0.06); border-top: 4px solid transparent; }
        .stat-box.b-yellow { border-top-color: var(--yellow); }
        .stat-box.b-green { border-top-color: var(--success); }
        .stat-box.b-blue { border-top-color: var(--blue-mid); }
        .stat-box.b-red { border-top-color: var(--danger); }
        .stat-big { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 28px; color: var(--text-dark); }
        .stat-label { font-size: 12px; color: var(--muted); margin-top: 4px; }

        .workspace { display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(330px, 0.75fr); gap: 22px; align-items: start; }
        .card { background: var(--color-surface); border-radius: 16px; box-shadow: 0 2px 12px rgba(0, 30, 90, 0.07); overflow: hidden; }
        .card-header { padding: 18px 22px 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .card-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 14px; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        .card-title svg { width: 17px; height: 17px; color: var(--blue-mid); }
        .card-body { padding: 16px 22px 22px; }
        .section-label { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748B; margin-bottom: 12px; }

        .selector-band { display: grid; grid-template-columns: 1fr auto; gap: 18px; align-items: end; background: linear-gradient(135deg, #F0F5FF, #FFFFFF); border: 1px solid #C8D8FF; border-radius: 14px; padding: 18px; margin-bottom: 20px; }
        .field { display: flex; flex-direction: column; gap: 7px; }
        label { font-size: 12px; font-weight: 800; color: var(--text-dark); }
        input, select, textarea { width: 100%; border: 1.5px solid var(--line); background: var(--color-neutral-bg, white); border-radius: 10px; color: var(--text-dark); font-size: 13px; outline: none; }
        input, select { height: 42px; padding: 0 12px; }
        textarea { min-height: 88px; resize: vertical; padding: 12px; line-height: 1.45; }
        input:focus, select:focus, textarea:focus { border-color: var(--blue-bright); box-shadow: 0 0 0 3px rgba(0, 85, 212, 0.10); }
        .hint { font-size: 11px; color: #94A3B8; }

        .phase-box { display: flex; align-items: center; gap: 12px; border-radius: 14px; padding: 14px; margin-bottom: 18px; background: #EDFFF8; border: 1px solid #A0F0D0; }
        .phase-box.no-aplica { background: #FFFBEC; border-color: #FFE099; }
        .phase-icon { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--success); color: white; flex: none; }
        .phase-box.no-aplica .phase-icon { background: var(--warning); }
        .phase-icon svg { width: 20px; height: 20px; }
        .phase-title { font-size: 13px; font-weight: 800; }
        .phase-text { font-size: 12px; color: var(--muted); line-height: 1.45; margin-top: 2px; }

        .fai-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; }
        .fai-card { border-radius: 14px; padding: 18px; position: relative; border: 1.5px solid #D0DCEF; background: #F8FAFC; }
        .fai-card.ok { background: linear-gradient(135deg, #EDFFF8, #CCFFF0); border-color: #A0F0D0; }
        .fai-card.warn { background: linear-gradient(135deg, #FFFBEC, #FFF3CC); border-color: #FFE099; }
        .fai-card.noapp { background: linear-gradient(135deg, #F4F6FB, #EAEFF8); border-color: #D0DCEF; }
        .api-tag { position: absolute; top: 12px; right: 12px; font-size: 9px; font-weight: 800; letter-spacing: 1px; padding: 3px 8px; border-radius: 20px; text-transform: uppercase; }
        .tag-sunedu { background: #EEF3FF; color: var(--blue-mid); }
        .tag-dga { background: #FFF8DC; color: #CC7A00; }
        .fai-icon { width: 42px; height: 42px; border-radius: 12px; background: white; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; color: var(--blue-mid); }
        .fai-icon svg { width: 23px; height: 23px; }
        .fai-name { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 14px; color: var(--text-dark); margin-bottom: 4px; }
        .fai-detail { font-size: 12px; color: #64748B; margin-bottom: 12px; line-height: 1.45; }
        .fai-result { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 800; padding: 5px 12px; border-radius: 20px; }
        .res-ok { background: #00C48C; color: white; }
        .res-warn { background: var(--warning); color: white; }
        .res-noapp { background: #94A3B8; color: white; }
        .fai-time { font-size: 10px; color: #94A3B8; margin-top: 8px; }

        .manual-form { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .manual-form .full { grid-column: 1/-1; }

        .btn { height: 40px; border: none; border-radius: 10px; padding: 0 16px; font-size: 13px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
        .btn.secondary { background: #EEF3FF; color: var(--blue-mid); }
        .btn.ghost { background: white; color: var(--muted); border: 1px solid var(--line); }
        .btn.primary { background: var(--yellow); color: var(--blue-deep); }
        .btn.blue { background: var(--blue-mid); color: white; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; border-top: 1px solid #EDF2F7; padding-top: 18px; }

        .decision-list { display: flex; flex-direction: column; gap: 10px; }
        .decision-item { display: flex; align-items: flex-start; gap: 10px; border-radius: 12px; padding: 12px; background: #F8FAFC; border: 1px solid #EDF2F7; }
        .decision-item.ok { background: #EDFFF8; border-color: #A0F0D0; }
        .decision-item.todo { background: #FFFBEC; border-color: #FFE099; }
        .decision-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex: none; background: var(--blue-mid); color: white; }
        .decision-item.ok .decision-icon { background: var(--success); }
        .decision-item.todo .decision-icon { background: var(--warning); }
        .decision-icon svg { width: 15px; height: 15px; }
        .decision-title { font-size: 12px; font-weight: 800; }
        .decision-text { font-size: 11px; color: var(--muted); line-height: 1.45; margin-top: 2px; }
    </style>

    <div class="page-header">
        <div class="breadcrumb">Inicio › Filtros FAI › <span>Etapa II</span></div>
        <div class="page-title">FAI Etapa II: Bachiller y Voucher de Sustentación</div>
        <div class="page-desc">Validación manual semafórica de RF-02.3 SUNEDU y RF-02.4 DGA.</div>
        <span class="rf-badge">F-14 · RF-02.3 + RF-02.4</span>
    </div>

    <div class="stat-strip">
        <div class="stat-box b-blue"><div class="stat-big">{{ $totalEtapa2 }}</div><div class="stat-label">Expedientes Etapa II</div></div>
        <div class="stat-box b-green"><div class="stat-big">{{ $validadosHoy }}</div><div class="stat-label">Validados hoy</div></div>
        <div class="stat-box b-yellow"><div class="stat-big">{{ $pendientesManuales }}</div><div class="stat-label">Pendientes manuales</div></div>
        <div class="stat-box b-red"><div class="stat-big">{{ $noAplicaEtapa1 }}</div><div class="stat-label">No aplica Etapa I</div></div>
    </div>

    @if(session('success'))
        <div class="p-3 rounded-xl mb-4 text-sm font-bold" style="background-color: var(--color-success-bg); color: var(--color-success);">
            {{ session('success') }}
        </div>
    @endif

    <div class="workspace">
        <section class="card">
            <div class="card-header">
                <div class="card-title"><i data-lucide="shield-check"></i>Verificación FAI para sustentación</div>
            </div>
            <div class="card-body">
                
                <form action="{{ route('fai.etapa2.index') }}" method="GET" class="selector-band">
                    <div class="field">
                        <label for="expediente_id">Selector de expediente</label>
                        <select name="expediente_id" id="expediente_id">
                            <option value="">— Selecciona un expediente —</option>
                            @foreach($expedientes as $exp)
                                <option value="{{ $exp->id }}" {{ $expedienteSeleccionado && $expedienteSeleccionado->id == $exp->id ? 'selected' : '' }}>
                                    {{ $exp->numero_radicacion }} · {{ Str::limit($exp->titulo, 40) }} (Fase {{ $exp->fase_actual }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn blue"><i data-lucide="search"></i>Cargar expediente</button>
                </form>

                @if($expedienteSeleccionado)
                    
                    @if($expedienteSeleccionado->etapa === 'I')
                        <div class="phase-box no-aplica">
                            <div class="phase-icon"><i data-lucide="alert-triangle"></i></div>
                            <div>
                                <div class="phase-title">Expediente en Etapa I (Proyecto)</div>
                                <div class="phase-text">RF-02.3 y RF-02.4 <strong>no aplican</strong>. Ambos servicios retornan estado no_aplica en esta etapa.</div>
                            </div>
                        </div>
                    @else
                        <div class="phase-box">
                            <div class="phase-icon"><i data-lucide="check"></i></div>
                            <div>
                                <div class="phase-title">Expediente en Etapa II · Fase {{ $expedienteSeleccionado->fase_actual }}</div>
                                <div class="phase-text">El expediente cumple la fase para registrar validaciones.</div>
                            </div>
                        </div>

                        <div class="fai-grid">
                            <div class="fai-card {{ $resultadoSunedu ? ($resultadoSunedu->estado === 'aprobado' ? 'ok' : 'warn') : 'noapp' }}">
                                <span class="api-tag tag-sunedu">SUNEDU</span>
                                <div class="fai-icon"><i data-lucide="graduation-cap"></i></div>
                                <div class="fai-name">RF-02.3 · Grado bachiller</div>
                                <div class="fai-detail">Verifica que el estudiante tenga grado de bachiller registrado antes de pasar a sustentación.</div>
                                @if($resultadoSunedu)
                                    <span class="fai-result {{ $resultadoSunedu->estado === 'aprobado' ? 'res-ok' : 'res-warn' }}">
                                        {{ ucfirst($resultadoSunedu->estado) }}
                                    </span>
                                @else
                                    <span class="fai-result res-noapp">Pendiente</span>
                                @endif
                            </div>

                            <div class="fai-card {{ $resultadoDga ? ($resultadoDga->estado === 'aprobado' ? 'ok' : 'warn') : 'noapp' }}">
                                <span class="api-tag tag-dga">DGA</span>
                                <div class="fai-icon"><i data-lucide="receipt"></i></div>
                                <div class="fai-name">RF-02.4 · Voucher sustentación</div>
                                <div class="fai-detail">Valida pago de derecho de sustentación contra evidencia de tesorería antes de programar acto.</div>
                                @if($resultadoDga)
                                    <span class="fai-result {{ $resultadoDga->estado === 'aprobado' ? 'res-ok' : 'res-warn' }}">
                                        {{ ucfirst($resultadoDga->estado) }}
                                    </span>
                                @else
                                    <span class="fai-result res-noapp">Pendiente</span>
                                @endif
                            </div>
                        </div>

                        <div class="section-label">Formulario manual semafórico</div>
                        
                        <form action="{{ route('fai.etapa2.store') }}" method="POST" class="manual-form">
                            @csrf
                            <input type="hidden" name="expediente_id" value="{{ $expedienteSeleccionado->id }}">

                            <div class="field">
                                <label for="sunedu_estado">Resultado SUNEDU · Grado bachiller</label>
                                <select name="sunedu_estado" id="sunedu_estado" required>
                                    <option value="aprobado" {{ $resultadoSunedu && $resultadoSunedu->estado === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                    <option value="rechazado" {{ $resultadoSunedu && $resultadoSunedu->estado === 'rechazado' ? 'selected' : '' }}>Observado</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="dga_estado">Resultado DGA · Voucher sustentación</label>
                                <select name="dga_estado" id="dga_estado" required>
                                    <option value="aprobado" {{ $resultadoDga && $resultadoDga->estado === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                    <option value="rechazado" {{ $resultadoDga && $resultadoDga->estado === 'rechazado' ? 'selected' : '' }}>Observado</option>
                                </select>
                            </div>

                            <div class="field full flex justify-end gap-3">
                                <button type="submit" class="btn blue"><i data-lucide="shield-check"></i>Registrar Verificaciones</button>
                            </div>
                        </form>
                    @endif
                @else
                    <div class="p-6 text-center text-sm text-gray-500" style="color: var(--muted);">
                        <i data-lucide="folder-open" style="width:40px; height:40px; margin: 0 auto 10px; color:var(--line);"></i>
                        Selecciona un expediente para cargar sus evaluaciones FAI vigentes.
                    </div>
                @endif
            </div>
        </section>

        <aside>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i data-lucide="clipboard-check"></i>Criterios F-14</div>
                </div>
                <div class="card-body">
                    <div class="decision-list">
                        <div class="decision-item ok">
                            <div class="decision-icon"><i data-lucide="check"></i></div>
                            <div><div class="decision-title">Aplicación por etapa</div><div class="decision-text">Ambos filtros aplican solo en Etapa II, fase 8 o superior.</div></div>
                        </div>
                        <div class="decision-item ok">
                            <div class="decision-icon"><i data-lucide="check"></i></div>
                            <div><div class="decision-title">Etapa I retorna no_aplica</div><div class="decision-text">Si se ejecutan durante Etapa I, RF-02.3 y RF-02.4 deben responder no_aplica.</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        lucide.createIcons();
    </script>
</x-app-layout>
