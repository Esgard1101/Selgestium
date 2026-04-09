<x-app-layout>

<div class="space-y-6">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold" style="color: var(--color-text-primary);">
                <i class="fas fa-shield-halved mr-2" style="color: var(--color-primary);"></i>
                Historial de Acceso
            </h1>
            <p class="text-sm mt-0.5" style="color: var(--color-text-muted);">
                Registro de ingresos, cierres de sesión y accesos fallidos.
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('bitacora.acceso.index') }}"
          class="rounded-2xl p-5 shadow-sm"
          style="background-color: var(--color-surface); border: 1px solid var(--color-border);">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">

            <div>
                <label class="block text-xs font-semibold mb-1" style="color: var(--color-text-secondary);">Desde</label>
                <input type="date"
                       name="fecha_desde"
                       value="{{ request('fecha_desde') }}"
                       class="w-full px-3 py-2 rounded-xl text-sm outline-none"
                       style="background-color: var(--color-neutral-bg);
                              border: 1.5px solid var(--color-border);
                              color: var(--color-text-primary);"
                       onfocus="this.style.borderColor='var(--color-primary)'"
                       onblur="this.style.borderColor='var(--color-border)'">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1" style="color: var(--color-text-secondary);">Hasta</label>
                <input type="date"
                       name="fecha_hasta"
                       value="{{ request('fecha_hasta') }}"
                       class="w-full px-3 py-2 rounded-xl text-sm outline-none"
                       style="background-color: var(--color-neutral-bg);
                              border: 1.5px solid var(--color-border);
                              color: var(--color-text-primary);"
                       onfocus="this.style.borderColor='var(--color-primary)'"
                       onblur="this.style.borderColor='var(--color-border)'">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1" style="color: var(--color-text-secondary);">Tipo de acción</label>
                <select name="accion"
                        class="w-full px-3 py-2 rounded-xl text-sm outline-none"
                        style="background-color: var(--color-neutral-bg);
                               border: 1.5px solid var(--color-border);
                               color: var(--color-text-primary);">
                    <option value="">Todas</option>
                    <option value="login"           {{ request('accion') === 'login'           ? 'selected' : '' }}>Login exitoso</option>
                    <option value="logout"          {{ request('accion') === 'logout'          ? 'selected' : '' }}>Logout</option>
                    <option value="login_fallido"   {{ request('accion') === 'login_fallido'   ? 'selected' : '' }}>Login fallido</option>
                    <option value="sesion_expirada" {{ request('accion') === 'sesion_expirada' ? 'selected' : '' }}>Sesión expirada</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 py-2 px-4 rounded-xl text-sm font-semibold text-white"
                        style="background-color: var(--color-primary);"
                        onmouseover="this.style.opacity='0.9'"
                        onmouseout="this.style.opacity='1'">
                    <i class="fas fa-magnifying-glass mr-1.5"></i>Filtrar
                </button>
                <a href="{{ route('bitacora.acceso.index') }}"
                   class="py-2 px-3 rounded-xl text-sm"
                   style="background-color: var(--color-neutral-bg); color: var(--color-text-secondary); border: 1.5px solid var(--color-border);"
                   title="Limpiar filtros">
                    <i class="fas fa-xmark"></i>
                </a>
            </div>
        </div>
    </form>

    {{-- Tabla --}}
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--color-surface); border: 1px solid var(--color-border);">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="background-color: var(--color-primary);">
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white">Usuario</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white">Acción</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white">IP</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white hidden lg:table-cell">Navegador</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white">Fecha y hora</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color: var(--color-border);">
                    @forelse ($registros as $reg)
                        <tr style="border-bottom: 1px solid var(--color-border);"
                            onmouseover="this.style.backgroundColor='var(--color-neutral-bg)'"
                            onmouseout="this.style.backgroundColor=''">

                            <td class="px-5 py-3">
                                @if ($reg->usuario_nombre)
                                    <p class="font-medium" style="color: var(--color-text-primary);">{{ $reg->usuario_nombre }}</p>
                                    <p class="text-xs" style="color: var(--color-text-muted);">{{ $reg->usuario_email }}</p>
                                @else
                                    <span class="text-xs" style="color: var(--color-text-muted);">Usuario eliminado</span>
                                @endif
                            </td>

                            <td class="px-5 py-3">
                                @php
                                    $badge = match($reg->accion) {
                                        'login'           => ['color' => 'var(--color-success)',  'bg' => 'var(--color-success-bg)',  'icon' => 'fa-circle-check',     'label' => 'Login'],
                                        'logout'          => ['color' => 'var(--color-neutral)',  'bg' => 'var(--color-neutral-bg)',  'icon' => 'fa-right-from-bracket','label' => 'Logout'],
                                        'login_fallido'   => ['color' => 'var(--color-danger)',   'bg' => 'var(--color-danger-bg)',   'icon' => 'fa-circle-xmark',     'label' => 'Login fallido'],
                                        'sesion_expirada' => ['color' => 'var(--color-warning)',  'bg' => 'var(--color-warning-bg)',  'icon' => 'fa-clock',            'label' => 'Sesión expirada'],
                                        default           => ['color' => 'var(--color-neutral)',  'bg' => 'var(--color-neutral-bg)',  'icon' => 'fa-circle',           'label' => $reg->accion],
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="background-color: {{ $badge['bg'] }}; color: {{ $badge['color'] }};">
                                    <i class="fas {{ $badge['icon'] }}"></i>
                                    {{ $badge['label'] }}
                                </span>
                            </td>

                            <td class="px-5 py-3">
                                <code class="text-xs px-2 py-0.5 rounded"
                                      style="background-color: var(--color-neutral-bg); color: var(--color-text-secondary); font-family: var(--font-mono);">
                                    {{ $reg->ip }}
                                </code>
                            </td>

                            <td class="px-5 py-3 hidden lg:table-cell max-w-xs">
                                <span class="text-xs truncate block" style="color: var(--color-text-muted);" title="{{ $reg->user_agent }}">
                                    {{ Str::limit($reg->user_agent ?? '', 55) }}
                                </span>
                            </td>

                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="text-sm font-medium" style="color: var(--color-text-primary);">
                                    {{ \Carbon\Carbon::parse($reg->created_at)->format('d/m/Y') }}
                                </span>
                                <span class="text-xs block" style="color: var(--color-text-muted);">
                                    {{ \Carbon\Carbon::parse($reg->created_at)->format('H:i:s') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <i class="fas fa-database text-3xl mb-3 block" style="color: var(--color-neutral);"></i>
                                <p class="text-sm" style="color: var(--color-text-muted);">No hay registros con los filtros aplicados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if ($registros->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--color-border);">
                {{ $registros->links() }}
            </div>
        @endif

    </div>

</div>

</x-app-layout>
