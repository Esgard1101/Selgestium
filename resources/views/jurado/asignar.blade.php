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
        .page-header::before { content: 'JUR'; position: absolute; right: -12px; top: -18px; font-family: 'Syne', sans-serif; font-weight: 800; font-size: 156px; color: rgba(255, 208, 0, 0.055); letter-spacing: -8px; }
        .breadcrumb { font-size: 12px; color: var(--gray-mid); margin-bottom: 6px; }
        .breadcrumb span { color: var(--yellow); }
        .page-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 27px; color: var(--white); margin-bottom: 5px; }
        .page-desc { font-size: 13px; color: var(--gray-mid); max-width: 620px; line-height: 1.5; }
        .rf-badge { display: inline-block; margin-top: 10px; background: var(--yellow); color: var(--blue-deep); font-family: 'Syne', sans-serif; font-weight: 800; font-size: 11px; letter-spacing: 1px; padding: 4px 14px; border-radius: 20px; }

        .stat-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 22px; }
        .stat-box { background: white; border-radius: 14px; padding: 18px; text-align: center; box-shadow: 0 2px 8px rgba(0, 30, 90, 0.06); border-top: 4px solid transparent; }
        .stat-box.b-yellow { border-top-color: var(--yellow); }
        .stat-box.b-green { border-top-color: var(--success); }
        .stat-box.b-blue { border-top-color: var(--blue-mid); }
        .stat-box.b-red { border-top-color: var(--danger); }
        .stat-big { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 28px; color: var(--text-dark); }
        .stat-label { font-size: 12px; color: #94A3B8; margin-top: 4px; }

        .workspace { display: grid; grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.65fr); gap: 22px; align-items: start; }
        .card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0, 30, 90, 0.07); overflow: hidden; }
        .card-header { padding: 18px 22px 0; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .card-title { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 14px; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }
        .card-title svg { width: 17px; height: 17px; color: var(--blue-mid); }
        .card-body { padding: 16px 22px 22px; }
        .section-label { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748B; margin-bottom: 12px; }

        .expediente-band { display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: center; background: linear-gradient(135deg, #F0F5FF, #FFFFFF); border: 1px solid #C8D8FF; border-radius: 14px; padding: 18px; margin-bottom: 20px; }
        .exp-title { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 800; color: var(--blue-deep); margin-bottom: 5px; }
        .exp-meta { display: flex; flex-wrap: wrap; gap: 8px 14px; color: var(--muted); font-size: 12px; }
        .exp-meta strong { color: var(--text-dark); }
        .phase-chip { display: inline-flex; align-items: center; gap: 7px; border-radius: 24px; background: var(--blue-mid); color: white; font-size: 12px; font-weight: 800; padding: 8px 12px; white-space: nowrap; }
        .phase-chip svg { width: 15px; height: 15px; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field { display: flex; flex-direction: column; gap: 7px; }
        .field.full { grid-column: 1/-1; }
        label { font-size: 12px; font-weight: 800; color: var(--text-dark); }
        input, select, textarea { width: 100%; border: 1.5px solid var(--line); background: var(--color-neutral-bg, white); border-radius: 10px; color: var(--text-dark); font-size: 13px; outline: none; }
        input, select { height: 42px; padding: 0 12px; }
        textarea { min-height: 82px; resize: vertical; padding: 12px; line-height: 1.45; }
        input:focus, select:focus, textarea:focus { border-color: var(--blue-bright); box-shadow: 0 0 0 3px rgba(0, 85, 212, 0.10); }
        .hint { font-size: 11px; color: #94A3B8; }

        .jurado-field { border: 1px solid var(--line); border-radius: 14px; padding: 14px; background: #FAFCFF; margin-bottom: 16px; }
        .jurado-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .role-badge { display: inline-flex; align-items: center; gap: 6px; background: var(--blue-mid); color: white; border-radius: 20px; font-size: 11px; font-weight: 800; padding: 5px 10px; }
        .role-badge.secretario { background: #0F766E; }
        .role-badge.vocal { background: #7C3AED; }

        .actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; border-top: 1px solid #EDF2F7; padding-top: 18px; }
        .btn { height: 40px; border: none; border-radius: 10px; padding: 0 16px; font-size: 13px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
        .btn.primary { background: var(--yellow); color: var(--blue-deep); }
        .btn.ghost { background: white; color: var(--muted); border: 1px solid var(--line); }

        .validation-list { display: flex; flex-direction: column; gap: 10px; }
        .validation-item { display: flex; align-items: flex-start; gap: 10px; border-radius: 12px; padding: 12px; background: #F8FAFC; border: 1px solid #EDF2F7; }
        .validation-item.ok { background: #EDFFF8; border-color: #A0F0D0; }
        .validation-item.ok .validation-icon { background: var(--success); color: white; }
        .validation-icon { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex: none; }
        .validation-icon svg { width: 15px; height: 15px; }
        .validation-title { font-size: 12px; font-weight: 800; }
        .validation-text { font-size: 11px; color: var(--muted); line-height: 1.45; margin-top: 2px; }
    </style>

    <div class="page-header">
        <div class="breadcrumb">Inicio › Calidad & Jurado › <span>Asignar Jurado</span></div>
        <div class="page-title">Asignar Jurado</div>
        <div class="page-desc">Designación formal de presidente, secretario y vocal para un expediente aprobado por filtros administrativos, con validación de roles únicos y resolución vinculada.</div>
        <span class="rf-badge">F-12 · Asignación de jurado</span>
    </div>

    <div class="stat-strip">
        <div class="stat-box b-blue"><div class="stat-big">1</div><div class="stat-label">Pendientes de asignación</div></div>
        <div class="stat-box b-green"><div class="stat-big">18</div><div class="stat-label">Jurados activos</div></div>
        <div class="stat-box b-yellow"><div class="stat-big">4</div><div class="stat-label">Resoluciones por emitir</div></div>
        <div class="stat-box b-red"><div class="stat-big">0</div><div class="stat-label">Conflicto detectado</div></div>
    </div>

    <div class="workspace">
        <section class="card">
            <div class="card-header">
                <div class="card-title"><i data-lucide="file-user"></i>Formulario de designación</div>
                <a href="{{ route('jurado.asignar') }}" class="btn ghost"><i data-lucide="arrow-left"></i>Volver</a>
            </div>
            
            <div class="card-body">
                {{-- Datos del Expediente --}}
                <div class="expediente-band">
                    <div>
                        <div class="exp-title">{{ $expediente->numero_radicacion }} · {{ $expediente->titulo }}</div>
                        <div class="exp-meta">
                            <span>Estudiante: <strong>{{ $expediente->estudiante_nombre ?: 'No asignado' }}</strong></span>
                            <span>Asesor: <strong>{{ $expediente->asesor_nombre ?: 'Sin asesor' }}</strong></span>
                            <span>Programa: <strong>{{ $expediente->sucursal_nombre ?: 'General' }}</strong></span>
                            <span>Ingresado: <strong>{{ \Carbon\Carbon::parse($expediente->created_at)->format('d/m/Y') }}</strong></span>
                        </div>
                    </div>
                    <span class="phase-chip"><i data-lucide="shield-check"></i>Aprobado</span>
                </div>

                @if ($errors->any())
                    <div class="p-3 rounded-xl mb-4 text-sm font-bold" style="background-color: var(--color-danger-bg); color: var(--color-danger);">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('jurado.asignar.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="expediente_id" value="{{ $expediente->id }}">

                    {{-- Sección 1: Datos de Resolución --}}
                    <div class="section-label">1. Datos de la resolución</div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="numero_resolucion">Número de Resolución <span style="color:var(--danger)">*</span></label>
                            <input type="text" id="numero_resolucion" name="numero_resolucion" required
                                   value="{{ old('numero_resolucion') }}" placeholder="RES-XXXX-2026">
                        </div>
                        <div class="field">
                            <label for="fecha_emision">Fecha de Emisión <span style="color:var(--danger)">*</span></label>
                            <input type="date" id="fecha_emision" name="fecha_emision" required
                                   value="{{ old('fecha_emision', date('Y-m-d')) }}">
                        </div>
                    </div>

                    {{-- Sección 2: Designación de Jurados --}}
                    <div class="section-label mt-6">2. Designación del jurado</div>
                    
                    <div class="jurado-field">
                        <div class="jurado-head">
                            <span class="role-badge"><i data-lucide="gavel"></i>Presidente</span>
                        </div>
                        <x-autocomplete 
                            name="presidente_id" 
                            label="" 
                            endpoint="/ajax/personas/search" 
                            placeholder="Escriba nombre o DNI del Presidente..."
                        />
                    </div>

                    <div class="jurado-field">
                        <div class="jurado-head">
                            <span class="role-badge secretario"><i data-lucide="pen-tool"></i>Secretario</span>
                        </div>
                        <x-autocomplete 
                            name="secretario_id" 
                            label="" 
                            endpoint="/ajax/personas/search" 
                            placeholder="Escriba nombre o DNI del Secretario..."
                        />
                    </div>

                    <div class="jurado-field">
                        <div class="jurado-head">
                            <span class="role-badge vocal"><i data-lucide="megaphone"></i>Vocal</span>
                        </div>
                        <x-autocomplete 
                            name="vocal_id" 
                            label="" 
                            endpoint="/ajax/personas/search" 
                            placeholder="Escriba nombre o DNI del Vocal..."
                        />
                    </div>

                    <div class="actions full">
                        <button type="submit" class="btn primary"><i data-lucide="circle-check"></i>Registrar Designación</button>
                    </div>
                </form>
            </div>
        </section>

        <aside>
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i data-lucide="shield-check"></i>Reglas de validación</div>
                </div>
                <div class="card-body">
                    <div class="validation-list">
                        <div class="validation-item ok">
                            <div class="validation-icon"><i data-lucide="check"></i></div>
                            <div>
                                <div class="validation-title">Miembros únicos</div>
                                <div class="validation-text">Una persona no puede ocupar dos roles en el mismo expediente.</div>
                            </div>
                        </div>
                        <div class="validation-item ok">
                            <div class="validation-icon"><i data-lucide="check"></i></div>
                            <div>
                                <div class="validation-title">Incompatibilidad de asesor</div>
                                <div class="validation-text">El asesor no puede formar parte del jurado examinador.</div>
                            </div>
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
