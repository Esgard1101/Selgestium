<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · {{ config('app.name', 'SELGESTIUM') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="background-color: var(--color-bg); font-family: var(--font-sans);">

<div class="min-h-screen flex">

    {{-- ── Panel izquierdo: identidad institucional ─────────────────────────── --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col items-center justify-center px-12 py-16 relative overflow-hidden"
         style="background-color: var(--color-primary);">

        {{-- Fondo decorativo --}}
        <div class="absolute inset-0 opacity-10"
             style="background-image: radial-gradient(circle at 30% 20%, var(--color-accent) 0%, transparent 50%),
                                      radial-gradient(circle at 80% 80%, var(--color-primary-light) 0%, transparent 50%);"></div>

        <div class="relative z-10 text-center max-w-sm">
            <img src="{{ asset(config('university.logo_url')) }}"
                 alt="{{ config('app.name') }}"
                 class="h-24 w-auto mx-auto mb-8"
                 onerror="this.style.display='none'">

            <h1 class="text-3xl font-bold text-white mb-3">
                {{ config('app.name', 'SELGESTIUM') }}
            </h1>
            <p class="text-base font-medium mb-1" style="color: rgba(255,255,255,0.80);">
                {{ config('university.faculty_name') }}
            </p>
            <p class="text-sm" style="color: rgba(255,255,255,0.55);">
                {{ config('university.university_name') }} · {{ config('university.academic_period') }}
            </p>

            <div class="mt-10 pt-8" style="border-top: 1px solid rgba(255,255,255,0.15);">
                <p class="text-xs leading-relaxed" style="color: rgba(255,255,255,0.45);">
                    Sistema de Gestión de Expedientes de Tesis y Sustentaciones
                </p>
            </div>
        </div>
    </div>

    {{-- ── Panel derecho: formulario de login ──────────────────────────────── --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

            {{-- Logo visible solo en mobile --}}
            <div class="lg:hidden text-center mb-8">
                <img src="{{ asset(config('university.logo_url')) }}"
                     alt="{{ config('app.name') }}"
                     class="h-14 w-auto mx-auto mb-3"
                     onerror="this.style.display='none'">
                <h2 class="text-xl font-bold" style="color: var(--color-primary);">
                    {{ config('app.name', 'SELGESTIUM') }}
                </h2>
                <p class="text-sm mt-1" style="color: var(--color-text-muted);">
                    {{ config('university.faculty_name') }}
                </p>
            </div>

            {{-- Tarjeta del formulario --}}
            <div class="rounded-2xl shadow-lg p-8"
                 style="background-color: var(--color-surface); border: 1px solid var(--color-border);">

                <h2 class="text-2xl font-bold mb-1" style="color: var(--color-text-primary);">
                    Bienvenido
                </h2>
                <p class="text-sm mb-7" style="color: var(--color-text-muted);">
                    Ingresa tus credenciales institucionales para continuar.
                </p>

                {{-- Mensaje de estado (recuperación de contraseña, expiración) --}}
                @if (session('status'))
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3 mb-5 text-sm"
                         style="background-color: var(--color-success-bg); color: var(--color-success);">
                        <i class="fas fa-circle-check mt-0.5 flex-shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                {{-- Errores de validación --}}
                @if ($errors->any())
                    <div class="flex items-start gap-3 rounded-xl px-4 py-3 mb-5 text-sm"
                         style="background-color: var(--color-danger-bg); color: var(--color-danger);">
                        <i class="fas fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email"
                               class="block text-sm font-semibold mb-1.5"
                               style="color: var(--color-text-primary);">
                            Correo electrónico
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"
                                  style="color: var(--color-text-muted);">
                                <i class="fas fa-envelope text-sm"></i>
                            </span>
                            <input id="email"
                                   name="email"
                                   type="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   placeholder="usuario@selgestium.com"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm transition-colors duration-150 outline-none"
                                   style="background-color: var(--color-neutral-bg);
                                          border: 1.5px solid {{ $errors->has('email') ? 'var(--color-danger)' : 'var(--color-border)' }};
                                          color: var(--color-text-primary);"
                                   onfocus="this.style.borderColor='var(--color-primary)'; this.style.backgroundColor='var(--color-surface)'"
                                   onblur="this.style.borderColor='var(--color-border)'; this.style.backgroundColor='var(--color-neutral-bg)'">
                        </div>
                    </div>

                    {{-- Contraseña --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password"
                                   class="block text-sm font-semibold"
                                   style="color: var(--color-text-primary);">
                                Contraseña
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-xs transition-colors duration-150"
                                   style="color: var(--color-primary);"
                                   onmouseover="this.style.opacity='0.75'"
                                   onmouseout="this.style.opacity='1'">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none"
                                  style="color: var(--color-text-muted);">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input id="password"
                                   name="password"
                                   type="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm transition-colors duration-150 outline-none"
                                   style="background-color: var(--color-neutral-bg);
                                          border: 1.5px solid {{ $errors->has('password') ? 'var(--color-danger)' : 'var(--color-border)' }};
                                          color: var(--color-text-primary);"
                                   onfocus="this.style.borderColor='var(--color-primary)'; this.style.backgroundColor='var(--color-surface)'"
                                   onblur="this.style.borderColor='var(--color-border)'; this.style.backgroundColor='var(--color-neutral-bg)'">
                        </div>
                    </div>

                    {{-- Recordarme --}}
                    <div class="flex items-center gap-2">
                        <input id="remember_me"
                               name="remember"
                               type="checkbox"
                               class="w-4 h-4 rounded"
                               style="accent-color: var(--color-primary);">
                        <label for="remember_me"
                               class="text-sm cursor-pointer"
                               style="color: var(--color-text-secondary);">
                            Mantener sesión iniciada
                        </label>
                    </div>

                    {{-- Botón submit --}}
                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-white transition-opacity duration-150"
                            style="background-color: var(--color-primary);"
                            onmouseover="this.style.opacity='0.90'"
                            onmouseout="this.style.opacity='1'">
                        <i class="fas fa-arrow-right-to-bracket mr-2"></i>
                        Iniciar sesión
                    </button>
                </form>
            </div>

            <p class="text-center text-xs mt-6" style="color: var(--color-text-muted);">
                Acceso restringido al personal autorizado.
                Si necesitas acceso, contacta al administrador del sistema.
            </p>
        </div>
    </div>

</div>

</body>
</html>
