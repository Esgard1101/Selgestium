<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="rounded-2xl p-6 shadow-sm mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl" style="background-color: var(--color-accent-light); color: var(--color-primary);">
                    <i class="fas fa-calendar-days text-lg"></i>
                </span>
                <div>
                    <h2 class="text-lg font-bold" style="color: var(--color-text-primary);">Calendario de Sustentaciones</h2>
                    <p class="text-xs" style="color: var(--color-text-secondary);">Consulta la programación mensual oficial de la facultad</p>
                </div>
            </div>

            {{-- Filtros --}}
            <form action="{{ route('sustentacion.calendario') }}" method="GET" class="flex flex-wrap gap-3">
                <select name="sucursal_id" class="rounded-xl border p-2 text-xs focus:outline-none" style="background: #FAFCFF; border-color: var(--color-border); color: var(--color-text-primary);">
                    @foreach($sucursales as $suc)
                        <option value="{{ $suc->id }}" @if($sucursalId == $suc->id) selected @endif>{{ $suc->descripcion }}</option>
                    @endforeach
                </select>

                <select name="mes" class="rounded-xl border p-2 text-xs focus:outline-none" style="background: #FAFCFF; border-color: var(--color-border); color: var(--color-text-primary);">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @if($mes == $m) selected @endif>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endfor
                </select>

                <select name="anio" class="rounded-xl border p-2 text-xs focus:outline-none" style="background: #FAFCFF; border-color: var(--color-border); color: var(--color-text-primary);">
                    @for($a = now()->year - 2; $a <= now()->year + 2; $a++)
                        <option value="{{ $a }}" @if($anio == $a) selected @endif>{{ $a }}</option>
                    @endfor
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold transition hover:opacity-90" style="background-color: var(--color-primary); color: white;">
                    Filtrar
                </button>
            </form>
        </div>

        @php
            $fechaInicio = \Carbon\Carbon::create($anio, $mes, 1);
            $diasEnMes = $fechaInicio->daysInMonth;
            $diaSemanaInicio = $fechaInicio->dayOfWeek; // 0 (Dom) a 6 (Sab)
            $mesAnterior = $fechaInicio->copy()->subMonth();
            $diasMesAnterior = $mesAnterior->daysInMonth;
        @endphp

        {{-- Grid del Calendario --}}
        <div class="rounded-2xl shadow-sm overflow-hidden border" style="background-color: var(--color-surface); border-color: var(--color-border);">
            {{-- Días de la Semana --}}
            <div class="grid grid-cols-7 text-center font-bold text-xs border-b py-3" style="background-color: var(--color-neutral-bg); border-color: var(--color-border); color: var(--color-text-secondary);">
                <div>DOM</div>
                <div>LUN</div>
                <div>MAR</div>
                <div>MIÉ</div>
                <div>JUE</div>
                <div>VIE</div>
                <div>SÁB</div>
            </div>

            {{-- Cuadrícula de días --}}
            <div class="grid grid-cols-7 auto-rows-[120px] divide-x divide-y" style="divide-color: var(--color-border); border-color: var(--color-border);">
                {{-- Celdas vacías del mes anterior --}}
                @for($i = 0; $i < $diaSemanaInicio; $i++)
                    @php $diaPrev = $diasMesAnterior - ($diaSemanaInicio - 1 - $i); @endphp
                    <div class="p-2 text-right text-xs text-gray-300 bg-gray-50" style="background-color: #F9FAFB;">
                        {{ $diaPrev }}
                    </div>
                @endfor

                {{-- Días del Mes Actual --}}
                @for($dia = 1; $dia <= $diasEnMes; $dia++)
                    @php
                        $diaFecha = \Carbon\Carbon::create($anio, $mes, $dia);
                        $eventosHoy = $sustentaciones->filter(function($s) use ($diaFecha) {
                            return \Carbon\Carbon::parse($s->fecha_hora)->isSameDay($diaFecha);
                        });
                        $esHoy = $diaFecha->isToday();
                    @endphp

                    <div class="p-2 relative flex flex-col gap-1 overflow-y-auto @if($esHoy) bg-blue-50 @endif" style="@if($esHoy) background-color: var(--color-accent-light); @endif">
                        <span class="text-xs font-bold block text-right @if($esHoy) text-blue-700 @else text-gray-600 @endif" style="@if($esHoy) color: var(--color-primary); @endif">
                            {{ $dia }}
                        </span>

                        {{-- Eventos / Sustentaciones --}}
                        @foreach($eventosHoy as $ev)
                            <div class="rounded-lg p-1.5 text-3xs font-bold flex flex-col gap-0.5 shadow-sm border border-opacity-30 hover:scale-95 transition-transform cursor-pointer" 
                                 style="background-color: #EEF2FF; border-color: #C7D2FE; color: var(--color-primary);"
                                 title="Proyecto: {{ $ev->titulo }}">
                                <div class="flex items-center gap-1 truncate">
                                    <i class="fas fa-clock text-opacity-70" style="font-size: 8px;"></i>
                                    {{ \Carbon\Carbon::parse($ev->fecha_hora)->format('H:i') }}
                                </div>
                                <div class="truncate">{{ $ev->estudiante_nombre }}</div>
                                <div class="text-3xs font-normal truncate text-gray-500" style="color: var(--color-text-muted);">{{ $ev->lugar }}</div>
                            </div>
                        @endforeach
                    </div>
                @endfor

                {{-- Celdas vacías del mes posterior --}}
                @php
                    $celdasOcupadas = $diaSemanaInicio + $diasEnMes;
                    $celdasRestantes = (ceil($celdasOcupadas / 7) * 7) - $celdasOcupadas;
                @endphp
                @for($j = 1; $j <= $celdasRestantes; $j++)
                    <div class="p-2 text-right text-xs text-gray-300 bg-gray-50" style="background-color: #F9FAFB;">
                        {{ $j }}
                    </div>
                @endfor
            </div>
        </div>
    </div>
</x-app-layout>
