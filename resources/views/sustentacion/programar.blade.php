<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

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
            --teal: #0F766E;
            --purple: #7C3AED;
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
            content: 'CAL';
            position: absolute;
            right: -12px;
            top: -18px;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 156px;
            color: rgba(255, 208, 0, 0.055);
            letter-spacing: -8px;
        }

        .breadcrumb {
            font-size: 12px;
            color: var(--gray-mid);
            margin-bottom: 6px;
        }

        .breadcrumb span {
            color: var(--yellow);
        }

        .page-title {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 27px;
            color: var(--white);
            margin-bottom: 5px;
        }

        .page-desc {
            font-size: 13px;
            color: var(--gray-mid);
            max-width: 680px;
            line-height: 1.5;
        }

        .rf-badge {
            display: inline-block;
            margin-top: 10px;
            background: var(--yellow);
            color: var(--blue-deep);
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 11px;
            letter-spacing: 1px;
            padding: 4px 14px;
            border-radius: 20px;
        }

        .stat-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        .stat-box {
            background: white;
            border-radius: 14px;
            padding: 18px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 30, 90, 0.06);
            border-top: 4px solid transparent;
        }

        .stat-box.b-yellow { border-top-color: var(--yellow); }
        .stat-box.b-green { border-top-color: var(--success); }
        .stat-box.b-blue { border-top-color: var(--blue-mid); }
        .stat-box.b-red { border-top-color: var(--danger); }

        .stat-big {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 28px;
            color: var(--text-dark);
        }

        .stat-label {
            font-size: 12px;
            color: #94A3B8;
            margin-top: 4px;
        }

        .workspace {
            display: grid;
            grid-template-columns: minmax(330px, 0.75fr) minmax(0, 1.25fr);
            gap: 22px;
            align-items: start;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 30, 90, 0.07);
            overflow: hidden;
            border: 1px solid var(--line);
        }

        .card-header {
            padding: 18px 22px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 14px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title i {
            font-size: 16px;
            color: var(--blue-mid);
        }

        .card-body {
            padding: 16px 22px 22px;
        }

        .section-label {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748B;
            margin-bottom: 12px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 14px;
        }

        .field label {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-dark);
        }

        .field input, .field select {
            width: 100%;
            border: 1.5px solid var(--line);
            background: white;
            border-radius: 10px;
            color: var(--text-dark);
            font-size: 13px;
            outline: none;
            height: 42px;
            padding: 0 12px;
        }

        .field input:focus, .field select:focus {
            border-color: var(--blue-bright);
            box-shadow: 0 0 0 3px rgba(0, 85, 212, 0.10);
        }

        .hint {
            font-size: 11px;
            color: #94A3B8;
        }

        .expediente-box {
            background: linear-gradient(135deg, #F0F5FF, #FFFFFF);
            border: 1px solid #C8D8FF;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 16px;
        }

        .exp-title {
            font-family: 'Syne', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: var(--blue-deep);
            margin-bottom: 5px;
        }

        .exp-meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .exp-meta strong {
            color: var(--text-dark);
        }

        .toggle-radio {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 14px;
        }

        .toggle-radio label {
            position: relative;
        }

        .toggle-radio input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .toggle-radio span {
            height: 42px;
            border-radius: 10px;
            border: 1.5px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 800;
            color: var(--muted);
            cursor: pointer;
            background: white;
        }

        .toggle-radio input:checked + span {
            border-color: var(--blue-mid);
            background: #EEF3FF;
            color: var(--blue-mid);
        }

        .validation-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 14px;
        }

        .validation-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-radius: 12px;
            padding: 12px;
            background: #F8FAFC;
            border: 1px solid #EDF2F7;
        }

        .validation-item.ok { background: #EDFFF8; border-color: #A0F0D0; }
        .validation-item.warn { background: #FFFBEC; border-color: #FFE099; }
        .validation-item.block { background: #FFF5F5; border-color: #FFCCCC; }

        .validation-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: none;
            color: white;
        }

        .validation-item.ok .validation-icon { background: var(--success); }
        .validation-item.warn .validation-icon { background: var(--warning); }
        .validation-item.block .validation-icon { background: var(--danger); }

        .btn {
            height: 40px;
            border: none;
            border-radius: 10px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn.secondary { background: #EEF3FF; color: var(--blue-mid); }
        .btn.ghost { background: white; color: var(--muted); border: 1px solid var(--line); }
        .btn.primary { background: var(--yellow); color: var(--blue-deep); }
        .btn.blue { background: var(--blue-mid); color: white; }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 18px;
            border-top: 1px solid #EDF2F7;
            padding-top: 18px;
        }

        .calendar-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .month-title {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: var(--blue-deep);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .cal-head {
            text-align: center;
            font-size: 10px;
            font-weight: 800;
            color: #94A3B8;
            text-transform: uppercase;
            padding: 6px 0;
        }

        .cal-day {
            min-height: 112px;
            border-radius: 12px;
            background: #F8FAFC;
            border: 1px solid #E7EEF9;
            padding: 9px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .cal-day.muted { opacity: 0.45; background: #F1F5F9; }
        .cal-day.today { border-color: var(--blue-mid); box-shadow: 0 0 0 3px rgba(0, 51, 160, 0.08); }
        
        .cal-date {
            font-size: 12px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .today-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--yellow);
        }

        .event {
            border-radius: 8px;
            padding: 7px 8px;
            color: white;
            font-size: 11px;
            line-height: 1.25;
            box-shadow: 0 2px 6px rgba(0, 30, 90, 0.12);
        }

        .event strong { display: block; font-size: 11px; margin-bottom: 2px; }
        .event.blue { background: var(--blue-mid); }
        .event.green { background: var(--teal); }
        .event.purple { background: var(--purple); }

        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: var(--muted);
            font-weight: 700;
        }

        .legend-dot { width: 10px; height: 10px; border-radius: 3px; }

        .schedule-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 16px;
        }

        .schedule-item {
            display: grid;
            grid-template-columns: 42px 1fr auto;
            gap: 10px;
            align-items: center;
            border-radius: 12px;
            padding: 12px;
            background: #F8FAFC;
            border: 1px solid #EDF2F7;
        }

        .time-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--blue-mid);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            text-align: center;
            line-height: 1.2;
        }

        .schedule-title { font-size: 12px; font-weight: 800; }
        .schedule-text { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .schedule-chip { font-size: 10px; font-weight: 800; border-radius: 20px; padding: 5px 9px; background: #EEF3FF; color: var(--blue-mid); }
    </style>

    <div class="page-header">
        <div class="breadcrumb">Inicio › Sustentación › <span>Programación y Calendario</span></div>
        <div class="page-title">Programación de Sustentación + Calendario Digital</div>
        <div class="page-desc">Agenda sustentaciones con validación de 7 días hábiles, control de colisión por lugar y hora, modalidad presencial o virtual, y calendario mensual de la facultad.</div>
        <span class="rf-badge">F-23 · Programación y calendario</span>
    </div>

    <div class="content">
        {{-- Stats --}}
        <div class="stat-strip">
            <div class="stat-box b-blue">
                <div class="stat-big">{{ $expedientes->count() }}</div>
                <div class="stat-label">Listos para programar</div>
            </div>
            <div class="stat-box b-green">
                <div class="stat-big">{{ $sustentaciones->count() }}</div>
                <div class="stat-label">Sustentaciones del mes</div>
            </div>
            <div class="stat-box b-yellow">
                <div class="stat-big">{{ $sustentaciones->where('modalidad', 'virtual')->count() }}</div>
                <div class="stat-label">Virtuales este mes</div>
            </div>
            <div class="stat-box b-red">
                <div class="stat-big">0</div>
                <div class="stat-label">Colisiones activas</div>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl mb-4 text-sm font-bold shadow-sm" style="background-color: var(--color-danger-bg); color: var(--color-danger); border: 1px solid var(--danger);">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="workspace">
            {{-- Columna Izquierda: Formulario --}}
            <section class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-calendar-plus"></i> Programar sustentación</div>
                    <button type="button" onclick="window.location.reload()" class="btn ghost"><i class="fas fa-rotate-left"></i> Limpiar</button>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('sustentacion.programar.store') }}" method="POST">
                        @csrf
                        
                        <div class="field">
                            <label for="expediente_id">Expediente</label>
                            <select id="expediente_id" name="expediente_id" required onchange="updateExpedienteInfo()">
                                <option value="">-- Seleccione un expediente --</option>
                                @foreach($expedientes as $exp)
                                    <option value="{{ $exp->id }}" data-estudiante="{{ $exp->estudiante }}" data-radicacion="{{ $exp->numero_radicacion }}">
                                        {{ $exp->numero_radicacion }} · {{ Str::limit($exp->titulo, 40) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="hint">La ruta del menú no usa parámetros; el expediente se selecciona en la pantalla.</div>
                        </div>

                        <div class="expediente-box hidden" id="box-expediente-info">
                            <div class="exp-title" id="info-numero"></div>
                            <div class="exp-meta">
                                <span>Estudiante: <strong id="info-estudiante"></strong></span>
                                <span>Estado: <strong>Aprobación de jurado consolidada</strong></span>
                            </div>
                        </div>

                        <div class="field">
                            <label for="fecha_hora">Fecha y hora</label>
                            <input id="fecha_hora" name="fecha_hora" type="datetime-local" required>
                            <div class="hint">Debe respetar el mínimo de 7 días hábiles desde la programación.</div>
                        </div>

                        <div class="field">
                            <label>Modalidad</label>
                            <div class="toggle-radio">
                                <label>
                                    <input type="radio" name="modalidad" value="presencial" checked onclick="toggleMeet(false)">
                                    <span><i class="fas fa-map-pin"></i> Presencial</span>
                                </label>
                                <label>
                                    <input type="radio" name="modalidad" value="virtual" onclick="toggleMeet(true)">
                                    <span><i class="fas fa-video"></i> Virtual</span>
                                </label>
                            </div>
                        </div>

                        <div class="field">
                            <label for="lugar">Lugar / Sala</label>
                            <input type="text" id="lugar" name="lugar" required placeholder="Ej. Auditorio FACHSE · Sala 01">
                        </div>

                        <div class="field hidden" id="field-meet">
                            <label for="enlace_virtual">Enlace virtual</label>
                            <input id="enlace_virtual" name="enlace_virtual" type="url" placeholder="https://meet.google.com/fch-sus-092">
                            <div class="hint">Obligatorio si la modalidad seleccionada es virtual.</div>
                        </div>

                        <div class="field">
                            <label for="resolucion_id">Resolución vinculada</label>
                            <select id="resolucion_id" name="resolucion_id">
                                <option value="">-- Seleccione una resolución (Opcional) --</option>
                                @foreach($resoluciones as $res)
                                    <option value="{{ $res->id }}">Res N° {{ $res->numero_resolucion }} ({{ $res->fecha_emision }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="validation-list">
                            <div class="validation-item ok">
                                <div class="validation-icon" style="background:var(--success);"><i class="fas fa-check"></i></div>
                                <div>
                                    <div class="validation-title">Plazo estándar</div>
                                    <div class="validation-text">Se verificará que la fecha cumpla los 7 días hábiles.</div>
                                </div>
                            </div>
                            <div class="validation-item warn">
                                <div class="validation-icon" style="background:var(--warning);"><i class="fas fa-triangle-exclamation"></i></div>
                                <div>
                                    <div class="validation-title">Notificación pendiente</div>
                                    <div class="validation-text">Al guardar, se notificará a estudiante, asesores y jurados.</div>
                                </div>
                            </div>
                        </div>

                        <div class="actions">
                            <button type="submit" class="btn primary"><i class="fas fa-calendar-check"></i> Programar</button>
                        </div>
                    </form>
                </div>
            </section>

            {{-- Columna Derecha: Calendario --}}
            <section class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fas fa-calendar-days"></i> Calendario mensual</div>
                </div>
                
                <div class="card-body">
                    {{-- Selector de Meses --}}
                    <form action="{{ route('sustentacion.programar.index') }}" method="GET" class="calendar-toolbar">
                        <div>
                            <div class="month-title">
                                {{ \Carbon\Carbon::create($anio, $mes, 1)->translatedFormat('F Y') }}
                            </div>
                            <div class="hint">Sustentaciones registradas en la facultad.</div>
                        </div>
                        
                        <div class="flex gap-2">
                            <select name="sucursal_id" onchange="this.form.submit()" class="rounded-xl border p-2 text-xs focus:outline-none" style="background: #FAFCFF; border-color: var(--color-border); color: var(--color-text-primary);">
                                @foreach($sucursales as $suc)
                                    <option value="{{ $suc->id }}" @if($sucursalId == $suc->id) selected @endif>{{ $suc->descripcion }}</option>
                                @endforeach
                            </select>

                            <select name="mes" onchange="this.form.submit()" class="rounded-xl border p-2 text-xs focus:outline-none" style="background: #FAFCFF; border-color: var(--color-border); color: var(--color-text-primary);">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @if($mes == $m) selected @endif>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                                @endfor
                            </select>

                            <select name="anio" onchange="this.form.submit()" class="rounded-xl border p-2 text-xs focus:outline-none" style="background: #FAFCFF; border-color: var(--color-border); color: var(--color-text-primary);">
                                @for($a = now()->year - 2; $a <= now()->year + 2; $a++)
                                    <option value="{{ $a }}" @if($anio == $a) selected @endif>{{ $a }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>

                    @php
                        $fechaInicio = \Carbon\Carbon::create($anio, $mes, 1);
                        $diasEnMes = $fechaInicio->daysInMonth;
                        $diaSemanaInicio = $fechaInicio->dayOfWeek;
                        $mesAnterior = $fechaInicio->copy()->subMonth();
                        $diasMesAnterior = $mesAnterior->daysInMonth;
                    @endphp

                    <div class="calendar-grid">
                        <div class="cal-head">Lun</div>
                        <div class="cal-head">Mar</div>
                        <div class="cal-head">Mié</div>
                        <div class="cal-head">Jue</div>
                        <div class="cal-head">Vie</div>
                        <div class="cal-head">Sáb</div>
                        <div class="cal-head">Dom</div>

                        {{-- Celdas mes anterior --}}
                        @for($i = 1; $i < $diaSemanaInicio; $i++)
                            @php $diaPrev = $diasMesAnterior - ($diaSemanaInicio - 1 - $i); @endphp
                            <div class="cal-day muted"><div class="cal-date">{{ $diaPrev }}</div></div>
                        @endfor

                        {{-- Días actuales --}}
                        @for($dia = 1; $dia <= $diasEnMes; $dia++)
                            @php
                                $diaFecha = \Carbon\Carbon::create($anio, $mes, $dia);
                                $eventosHoy = $sustentaciones->filter(function($s) use ($diaFecha) {
                                    return \Carbon\Carbon::parse($s->fecha_hora)->isSameDay($diaFecha);
                                });
                                $esHoy = $diaFecha->isToday();
                            @endphp

                            <div class="cal-day @if($esHoy) today @endif">
                                <div class="cal-date">
                                    {{ $dia }} 
                                    @if($esHoy) <span class="today-dot"></span> @endif
                                </div>

                                @foreach($eventosHoy as $ev)
                                    <div class="event @if($ev->modalidad == 'virtual') green @else blue @endif" title="{{ $ev->titulo }}">
                                        <strong>{{ \Carbon\Carbon::parse($ev->fecha_hora)->format('H:i') }}</strong>
                                        {{ Str::limit($ev->estudiante_nombre, 14) }}
                                    </div>
                                @endforeach
                            </div>
                        @endfor
                    </div>

                    <div class="legend">
                        <div class="legend-item"><span class="legend-dot" style="background:var(--blue-mid)"></span>Presencial</div>
                        <div class="legend-item"><span class="legend-dot" style="background:var(--teal)"></span>Virtual</div>
                    </div>

                    {{-- Listado abajo --}}
                    <div class="schedule-list">
                        <div class="section-label" style="margin-top:4px;margin-bottom:0;">Próximas sustentaciones</div>
                        @forelse($sustentaciones->take(4) as $ev)
                            <div class="schedule-item">
                                <div class="time-box" style="@if($ev->modalidad == 'virtual') background:var(--teal); @endif">
                                    {{ \Carbon\Carbon::parse($ev->fecha_hora)->format('d') }}<br>
                                    {{ \Carbon\Carbon::parse($ev->fecha_hora)->translatedFormat('M') }}
                                </div>
                                <div>
                                    <div class="schedule-title">{{ $ev->estudiante_nombre }}</div>
                                    <div class="schedule-text">{{ $ev->lugar }} · {{ \Carbon\Carbon::parse($ev->fecha_hora)->format('H:i A') }}</div>
                                </div>
                                <span class="schedule-chip" style="@if($ev->modalidad == 'virtual') color:var(--teal); background:#E6F7F5; @endif">
                                    {{ ucfirst($ev->modalidad) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">No hay sustentaciones registradas este mes.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        function toggleMeet(show) {
            const fieldMeet = document.getElementById('field-meet');
            if (show) {
                fieldMeet.classList.remove('hidden');
            } else {
                fieldMeet.classList.add('hidden');
            }
        }

        function updateExpedienteInfo() {
            const select = document.getElementById('expediente_id');
            const selectedOption = select.options[select.selectedIndex];
            const box = document.getElementById('box-expediente-info');
            
            if (!selectedOption.value) {
                box.classList.add('hidden');
                return;
            }

            box.classList.remove('hidden');
            document.getElementById('info-numero').innerText = selectedOption.getAttribute('data-radicacion');
            document.getElementById('info-estudiante').innerText = selectedOption.getAttribute('data-estudiante');
        }
    </script>
</x-app-layout>
