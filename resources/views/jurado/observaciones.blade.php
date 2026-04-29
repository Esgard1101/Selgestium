<x-app-layout>
    <!-- Inject Lucide Icons Script -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- Custom Mockup CSS -->
    <style>
        :root {
            --blue-deep: var(--color-primary);
            --blue-mid: var(--color-primary);
            --blue-bright: var(--color-primary);
            --yellow: var(--color-accent, #FFD000);
            --white: var(--color-surface, #FFFFFF);
            --gray-light: var(--color-neutral-bg, #F4F6FB);
            --gray-mid: var(--color-text-muted, #B8C4D8);
            --line: var(--color-border, #E2E8F0);
            --text-dark: var(--color-text-primary, #0A1628);
            --muted: var(--color-text-muted, #64748B);
            --success: var(--color-success, #00C48C);
            --warning: var(--color-warning, #FF9F43);
            --danger: var(--color-danger, #FF6B6B);
            --purple: #7C3AED;
        }
        
        .page-header { background: linear-gradient(135deg,#002B80 0%,var(--blue-deep) 62%); padding: 28px 34px 36px; position: relative; overflow: hidden; border-radius: 16px; margin-bottom: 24px;}
        .page-header::before { content: 'REV'; position: absolute; right: -12px; top: -18px; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 156px; color: rgba(255, 208, 0, 0.055); letter-spacing: -8px; }
        .breadcrumb { font-size: 12px; color: var(--gray-mid); margin-bottom: 6px; }
        .breadcrumb span { color: var(--yellow); }
        .page-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 27px; color: var(--white); margin-bottom: 5px; }
        .page-desc { font-size: 13px; color: var(--gray-mid); max-width: 650px; line-height: 1.5; }
        .rf-badge { display: inline-block; margin-top: 10px; background: var(--yellow); color: var(--blue-deep); font-family: 'Syne', sans-serif; font-weight: 800; font-size: 11px; letter-spacing: 1px; padding: 4px 14px; border-radius: 20px; }

        .stat-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 22px; }
        .stat-box { background: white; border-radius: 14px; padding: 18px; text-align: center; box-shadow: 0 2px 8px rgba(0, 30, 90, 0.06); border-top: 4px solid transparent; }
        .stat-box.b-yellow { border-top-color: var(--yellow); }
        .stat-box.b-green { border-top-color: var(--success); }
        .stat-box.b-blue { border-top-color: var(--blue-mid); }
        .stat-box.b-red { border-top-color: var(--danger); }
        .stat-big { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 28px; color: var(--text-dark); }
        .stat-label { font-size: 12px; color: #94A3B8; margin-top: 4px; }

        .workspace { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(330px, 0.6fr); gap: 22px; align-items: start; }
        .card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0, 30, 90, 0.07); overflow: hidden; }
        .card-header { padding: 18px 22px 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .card-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 14px; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        .card-title svg { width: 17px; height: 17px; color: var(--blue-mid); }
        .card-body { padding: 16px 22px 22px; }
        .section-label { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748B; margin-bottom: 12px; }

        .expediente-band { display: grid; grid-template-columns: 1fr auto; gap: 18px; align-items: center; background: linear-gradient(135deg, #F0F5FF, #FFFFFF); border: 1px solid #C8D8FF; border-radius: 14px; padding: 18px; margin-bottom: 20px; }
        .exp-title { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 800; color: var(--blue-deep); margin-bottom: 5px; }
        .exp-meta { display: flex; flex-wrap: wrap; gap: 8px 14px; color: var(--muted); font-size: 12px; }
        .exp-meta strong { color: var(--text-dark); }
        .status-chip { display: inline-flex; align-items: center; gap: 7px; border-radius: 24px; background: var(--warning); color: white; font-size: 12px; font-weight: 800; padding: 8px 12px; white-space: nowrap; }
        .status-chip svg { width: 15px; height: 15px; }

        .review-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px; }
        .review-card { border: 1px solid var(--line); border-radius: 14px; padding: 14px; background: #FAFCFF; }
        .review-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; gap: 10px; }
        .review-title { font-size: 12px; font-weight: 800; display: flex; align-items: center; gap: 7px; }
        .review-title svg { width: 15px; height: 15px; color: var(--blue-mid); }
        .score-pill { font-family: 'Syne', sans-serif; font-size: 13px; font-weight: 800; border-radius: 20px; padding: 5px 10px; background: #EEF3FF; color: var(--blue-mid); }
        .score-row { display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: center; margin-top: 9px; }
        .bar { height: 8px; border-radius: 99px; background: #E8EEF7; overflow: hidden; }
        .bar span { display: block; height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--blue-mid), var(--success)); }
        .criteria-note { font-size: 11px; color: var(--muted); line-height: 1.45; margin-top: 10px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field { display: flex; flex-direction: column; gap: 7px; }
        .field.full { grid-column: 1/-1; }
        label { font-size: 12px; font-weight: 800; color: var(--text-dark); }
        input, select, textarea { width: 100%; border: 1.5px solid var(--line); background: var(--color-neutral-bg, white); border-radius: 10px; color: var(--text-dark); font-size: 13px; outline: none; }
        input, select { height: 42px; padding: 0 12px; }
        textarea { min-height: 120px; resize: vertical; padding: 12px; line-height: 1.45; }
        input:focus, select:focus, textarea:focus { border-color: var(--blue-bright); box-shadow: 0 0 0 3px rgba(0, 85, 212, 0.10); }
        .hint { font-size: 11px; color: #94A3B8; }

        .decision-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
        .decision { position: relative; }
        .decision input { position: absolute; opacity: 0; pointer-events: none; }
        .decision span { height: 42px; border-radius: 10px; border: 1.5px solid var(--line); display: flex; align-items: center; justify-content: center; gap: 7px; font-size: 12px; font-weight: 800; color: var(--muted); cursor: pointer; background: white; }
        .decision svg { width: 15px; height: 15px; }
        .decision input:checked + span { border-color: var(--blue-mid); background: #EEF3FF; color: var(--blue-mid); }
        .decision.approve input:checked + span { border-color: var(--success); background: #EDFFF8; color: #00875A; }
        .decision.observe input:checked + span { border-color: var(--warning); background: #FFFBEC; color: #CC7A00; }
        .decision.reject input:checked + span { border-color: var(--danger); background: #FFF5F5; color: #C53030; }

        .file-row { display: flex; gap: 10px; align-items: center; padding: 12px; border: 1px solid #E7EEF9; border-radius: 12px; background: #F8FBFF; }
        .file-icon { width: 38px; height: 38px; border-radius: 10px; background: var(--blue-mid); color: white; display: flex; align-items: center; justify-content: center; flex: none; }
        .file-icon svg { width: 19px; height: 19px; }
        .file-title { font-size: 12px; font-weight: 800; }
        .file-sub { font-size: 11px; color: #94A3B8; margin-top: 2px; }

        .btn { height: 40px; border: none; border-radius: 10px; padding: 0 16px; font-size: 13px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
        .btn svg { width: 16px; height: 16px; }
        .btn.secondary { background: #EEF3FF; color: var(--blue-mid); }
        .btn.ghost { background: white; color: var(--muted); border: 1px solid var(--line); }
        .btn.primary { background: var(--yellow); color: var(--blue-deep); }
        .btn.blue { background: var(--blue-mid); color: white; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; border-top: 1px solid #EDF2F7; padding-top: 18px; }

        .countdown-box { background: linear-gradient(135deg, #001D5E, #0033A0); border-radius: 14px; padding: 20px; text-align: center; border: 1px solid rgba(255, 208, 0, 0.2); color: white; }
        .cd-label { font-size: 11px; font-weight: 800; letter-spacing: 1px; color: rgba(255, 255, 255, 0.55); text-transform: uppercase; margin-bottom: 8px; }
        .cd-time { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 34px; color: var(--yellow); letter-spacing: 1px; }
        .cd-sub { font-size: 12px; color: rgba(255, 255, 255, 0.62); margin-top: 4px; }
        .cd-progress { height: 8px; border-radius: 4px; background: rgba(255, 255, 255, 0.1); margin: 14px 0; overflow: hidden; }
        .cd-fill { height: 100%; border-radius: 4px; background: linear-gradient(90deg, var(--success), var(--yellow), var(--warning)); width: 74%; }

        .previous-list { display: flex; flex-direction: column; gap: 10px; }
        .obs-item { border-radius: 12px; border: 1px solid #E7EEF9; background: #F8FAFC; padding: 12px; }
        .obs-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 7px; }
        .obs-title { font-size: 12px; font-weight: 800; }
        .obs-badge { font-size: 10px; font-weight: 800; border-radius: 20px; padding: 4px 8px; }
        .obs-badge.open { background: #FFF3DC; color: #CC7A00; }
        .obs-badge.done { background: #DCFFF4; color: #00875A; }
        .obs-text { font-size: 11px; color: var(--muted); line-height: 1.45; }

        .rgt-box { border-radius: 14px; background: linear-gradient(135deg, #FFF5F5, #FFEBEB); border: 1px solid #FFCCCC; padding: 14px; margin-top: 16px; }
        .rgt-title { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 800; color: #C53030; margin-bottom: 6px; }
        .rgt-title svg { width: 16px; height: 16px; }
        .rgt-text { font-size: 11px; line-height: 1.5; color: #7F1D1D; }

        .timeline { display: flex; flex-direction: column; gap: 12px; }
        .timeline-item { display: grid; grid-template-columns: 34px 1fr; gap: 10px; }
        .timeline-dot { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #EAF0FF; color: var(--blue-mid); }
        .timeline-dot svg { width: 14px; height: 14px; }
        .timeline-title { font-size: 12px; font-weight: 800; }
        .timeline-text { font-size: 11px; color: var(--muted); line-height: 1.45; margin-top: 2px; }
    </style>

    <div class="page-header">
        <div class="breadcrumb">Inicio › Jurado › <span>Panel de Revisión</span></div>
        <div class="page-title">Panel de Revisión del Jurado</div>
        <div class="page-desc">Espacio de evaluación académica para revisar documentos, registrar observaciones permitidas en primera ronda y emitir aprobación u observación con trazabilidad legal.</div>
        <span class="rf-badge">F-13 · Revisión y observaciones</span>
    </div>

    <div class="stat-strip">
        <div class="stat-box b-blue"><div class="stat-big">1</div><div class="stat-label">Expedientes asignados</div></div>
        <div class="stat-box b-yellow"><div class="stat-big">5d</div><div class="stat-label">Para vencer revisión</div></div>
        <div class="stat-box b-green"><div class="stat-big">0</div><div class="stat-label">Aprobado esta semana</div></div>
        <div class="stat-box b-red"><div class="stat-big">{{ $esBloqueado ? 1 : 0 }}</div><div class="stat-label">Bloqueos activos</div></div>
    </div>

    <div class="workspace">
        <section class="card">
            <div class="card-header">
                <div class="card-title"><i data-lucide="file-search"></i>Evaluación del expediente</div>
            </div>
            <div class="card-body">
                
                {{-- Datos del Expediente --}}
                <div class="expediente-band">
                    <div>
                        <div class="exp-title">{{ $expediente->numero_radicacion }} · {{ $expediente->titulo }}</div>
                        <div class="exp-meta">
                            <span>Estudiante: <strong>{{ $expediente->estudiante_nombre ?: 'No asignado' }}</strong></span>
                            <span>Ronda: <strong>Ronda 1</strong></span>
                        </div>
                    </div>
                    <span class="status-chip"><i data-lucide="clock-3"></i>En revisión</span>
                </div>

                {{-- Criterios de Revisión --}}
                <div class="section-label">Criterios de revisión</div>
                <div class="review-grid">
                    <div class="review-card">
                        <div class="review-head">
                            <div class="review-title"><i data-lucide="target"></i>Coherencia del problema</div>
                            <span class="score-pill">82%</span>
                        </div>
                        <div class="score-row"><div class="bar"><span style="width:82%"></span></div><strong>Bueno</strong></div>
                    </div>
                    <div class="review-card">
                        <div class="review-head">
                            <div class="review-title"><i data-lucide="book-open-check"></i>Marco teórico</div>
                            <span class="score-pill">76%</span>
                        </div>
                        <div class="score-row"><div class="bar"><span style="width:76%"></span></div><strong>Aceptable</strong></div>
                    </div>
                    <div class="review-card">
                        <div class="review-head">
                            <div class="review-title"><i data-lucide="flask-conical"></i>Metodología</div>
                            <span class="score-pill">68%</span>
                        </div>
                        <div class="score-row"><div class="bar"><span style="width:68%;background:linear-gradient(90deg,var(--warning),var(--yellow));"></span></div><strong>Revisar</strong></div>
                    </div>
                    <div class="review-card">
                        <div class="review-head">
                            <div class="review-title"><i data-lucide="shield-check"></i>Normativa y forma</div>
                            <span class="score-pill">91%</span>
                        </div>
                        <div class="score-row"><div class="bar"><span style="width:91%"></span></div><strong>Óptimo</strong></div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="p-3 rounded-xl mb-4 text-sm font-bold" style="background-color: var(--color-success-bg); color: var(--color-success);">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3 rounded-xl mb-4 text-sm font-bold" style="background-color: var(--color-danger-bg); color: var(--color-danger);">
                        @foreach($errors->all() as $err)
                            <p>{{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Formulario de Registro --}}
                @if(!$esBloqueado)
                    <form action="{{ route('jurado.observaciones.store') }}" method="POST" class="form-grid mt-4">
                        @csrf
                        <input type="hidden" name="expediente_id" value="{{ $expediente->id }}">
                        
                        <div class="field full">
                            <label>Decisión del jurado</label>
                            <div class="decision-options">
                                <label class="decision approve">
                                    <input type="radio" name="tipo_decision" value="aprobado">
                                    <span><i data-lucide="check-circle-2"></i>Aprobar</span>
                                </label>
                                <label class="decision observe">
                                    <input type="radio" name="tipo_decision" value="observado" checked>
                                    <span><i data-lucide="message-square-warning"></i>Observar</span>
                                </label>
                                <label class="decision reject">
                                    <input type="radio" name="tipo_decision" value="rechazado">
                                    <span><i data-lucide="x-circle"></i>No aprobar</span>
                                </label>
                            </div>
                        </div>

                        <div class="field full">
                            <label for="descripcion">Observación académica</label>
                            <textarea id="descripcion" name="descripcion" required>{{ old('descripcion') }}</textarea>
                            <div class="hint">Las nuevas observaciones solo pueden registrarse en la primera ronda de revisión.</div>
                        </div>

                        <div class="actions full flex justify-end gap-3 w-full" style="grid-column: 1/-1;">
                            <button type="submit" class="btn primary"><i data-lucide="send"></i>Emitir evaluación</button>
                        </div>
                    </form>
                @else
                    <div class="rgt-box mt-4">
                        <div class="rgt-title"><i data-lucide="lock-keyhole"></i>🔒 Bloqueado por RGT</div>
                        <div class="rgt-text">Ya has registrado observaciones para este expediente. El botón "Nueva Observación" ha sido deshabilitado según lo estipulado por el RGT.</div>
                    </div>
                @endif
            </div>
        </section>

        <aside>
            <div class="countdown-box">
                <div class="cd-label">Plazo de revisión</div>
                <div class="cd-time">05:12:40</div>
                <div class="cd-sub">5 días restantes</div>
                <div class="cd-progress"><div class="cd-fill"></div></div>
            </div>

            <div class="card" style="margin-top:22px;">
                <div class="card-header">
                    <div class="card-title"><i data-lucide="message-square-text"></i>Observaciones previas</div>
                </div>
                <div class="card-body">
                    <div class="previous-list">
                        @forelse($observaciones as $obs)
                            <div class="obs-item">
                                <div class="obs-top">
                                    <div class="obs-title">Ronda {{ $obs->ronda }}</div>
                                    @if($obs->subsanado)
                                        <span class="obs-badge done">Subsanada</span>
                                    @else
                                        <span class="obs-badge open">Pendiente</span>
                                    @endif
                                </div>
                                <div class="obs-text">{{ $obs->descripcion }}</div>
                            </div>
                        @empty
                            <p class="text-xs text-center py-4" style="color: var(--muted);">No se han registrado observaciones.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        lucide.createIcons();
    </script>
</x-app-layout>
