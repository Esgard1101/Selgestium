<x-app-layout>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        :root {
            --blue-deep: var(--color-primary-dark);
            --blue-mid: var(--color-primary);
            --blue-bright: var(--color-primary-light);
            --yellow: var(--color-accent);
            --white: var(--color-surface);
            --gray-light: var(--color-bg);
            --gray-mid: var(--color-border);
            --text-dark: var(--color-text-primary);
            --muted: var(--color-text-secondary);
            --success: var(--color-success);
            --warning: var(--color-warning);
            --danger: var(--color-danger);
        }

        .page-header { background: linear-gradient(135deg, var(--blue-deep) 0%, var(--blue-mid) 62%); padding: 28px 34px 36px; border-radius: 16px; margin-bottom: 24px; position: relative; overflow: hidden; }
        .page-header::before { content: '⏱️'; position: absolute; right: 20px; top: 10px; font-size: 130px; opacity: 0.07; }
        .breadcrumb { font-size: 12px; color: var(--gray-mid); margin-bottom: 6px; }
        .breadcrumb span { color: var(--yellow); }
        .page-title { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 27px; color: var(--white); margin-bottom: 5px; }
        .page-desc { font-size: 13px; color: var(--gray-mid); max-width: 700px; line-height: 1.5; }

        .stat-strip { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: var(--white); border-radius: 16px; padding: 22px; display: flex; align-items: center; gap: 18px; box-shadow: 0 4px 12px rgba(0, 30, 90, 0.05); border: 1px solid rgba(0,0,0,0.02); border-left: 5px solid transparent; }
        .stat-card.danger { border-left-color: var(--danger); }
        .stat-card.warning { border-left-color: var(--warning); }
        .stat-card.success { border-left-color: var(--success); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .stat-card.danger .stat-icon { background: #FFE5E5; color: var(--danger); }
        .stat-card.warning .stat-icon { background: #FFF4E5; color: var(--warning); }
        .stat-card.success .stat-icon { background: #E6F9F0; color: var(--success); }
        .stat-info { display: flex; flex-direction: column; }
        .stat-num { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 32px; color: var(--text-dark); line-height: 1; }
        .stat-lbl { font-size: 13px; color: var(--muted); margin-top: 4px; }

        .table-card { background: var(--white); border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0, 30, 90, 0.05); }
        .table-title { font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 16px; color: var(--text-dark); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #F8FAFC; color: #475569; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 16px; text-align: left; border-bottom: 2px solid var(--color-border); }
        td { padding: 16px; border-bottom: 1px solid var(--color-border); font-size: 13px; color: var(--text-dark); }
        tr:hover { background: #F8FAFC; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-block; }
        .badge-red { background: #FFE5E5; color: var(--danger); }
        .badge-green { background: #E6F9F0; color: var(--success); }
        .badge-yellow { background: #FFF4E5; color: var(--warning); }
    </style>

    <div class="page-header">
        <div class="breadcrumb">Módulos › <span>Plazos</span></div>
        <div class="page-title">Control de Plazos Administrativos</div>
        <div class="page-desc">Seguimiento estricto de tiempos de evaluación (Art. 123-d del Reglamento de Grados y Títulos).</div>
    </div>

    <div class="stat-strip">
        <div class="stat-card danger">
            <div class="stat-icon"><i data-lucide="alarm-clock-off"></i></div>
            <div class="stat-info">
                <div class="stat-num">{{ $vencidosHoy }}</div>
                <div class="stat-lbl">Vencidos Hoy</div>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon"><i data-lucide="hourglass"></i></div>
            <div class="stat-info">
                <div class="stat-num">{{ $porVencer3Dias }}</div>
                <div class="stat-lbl">Por vencer (≤ 3 días)</div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-icon"><i data-lucide="shield-check"></i></div>
            <div class="stat-info">
                <div class="stat-num">{{ $art123dHabilitados }}</div>
                <div class="stat-lbl">Art. 123-d Habilitados</div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-title"><i data-lucide="clipboard-list" style="color:var(--blue-mid);"></i> desglose de Expedientes en Plazo</div>
        
        @if($plazos->isEmpty())
            <div class="py-8 text-center text-gray-500" style="color: var(--muted);">
                No hay plazos de evaluación activos registrados.
            </div>
        @else
            <div class="overflow-x-auto">
                <table>
                    <thead>
                        <tr>
                            <th>Expediente</th>
                            <th>Estudiante</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Vencimiento</th>
                            <th>Días Restantes</th>
                            <th>Estado</th>
                            <th>Art. 123-d</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plazos as $plazo)
                            <tr>
                                <td><strong>{{ $plazo->numero_radicacion }}</strong></td>
                                <td>{{ $plazo->estudiante }}</td>
                                <td>{{ \Carbon\Carbon::parse($plazo->fecha_inicio)->format('d/m/Y H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($plazo->fecha_vencimiento)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($plazo->vencido)
                                        <span class="badge badge-red">0 días</span>
                                    @else
                                        <span class="badge {{ $plazo->dias_restantes <= 3 ? 'badge-yellow' : 'badge-green' }}">
                                            {{ $plazo->dias_restantes }} días
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($plazo->vencido)
                                        <span class="badge badge-red">Vencido</span>
                                    @else
                                        <span class="badge badge-green">Vigente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($plazo->art123d_habilitado)
                                        <span class="badge badge-red animate-pulse"><i data-lucide="alert-triangle" style="width:12px;height:12px;display:inline;margin-right:4px;"></i> Habilitado</span>
                                    @else
                                        <span class="badge" style="background:#F1F5F9; color:#64748B;">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script>
        lucide.createIcons();
    </script>
</x-app-layout>
