<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificación 2FA · {{ config('app.name', 'SELGESTIUM') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased" style="background-color: var(--color-bg); font-family: var(--font-sans);">

<div class="min-h-screen flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-sm">

        {{-- Header --}}
        <div class="text-center mb-8">
            <img src="{{ asset(config('university.logo_url')) }}"
                 alt="{{ config('app.name') }}"
                 class="h-14 w-auto mx-auto mb-4"
                 onerror="this.style.display='none'">

            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full mb-4"
                 style="background-color: var(--color-accent-light);">
                <i class="fas fa-shield-halved text-2xl" style="color: var(--color-primary);"></i>
            </div>

            <h1 class="text-xl font-bold mb-1" style="color: var(--color-text-primary);">
                Verificación en dos pasos
            </h1>
            <p class="text-sm" style="color: var(--color-text-muted);">
                Ingresa el código de tu aplicación de autenticación.
            </p>
        </div>

        {{-- Tarjeta --}}
        <div class="rounded-2xl shadow-lg p-7"
             style="background-color: var(--color-surface); border: 1px solid var(--color-border);">

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

            <div x-data="{ recovery: false }">

                {{-- Código TOTP --}}
                <form x-show="!recovery" method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="code"
                               class="block text-sm font-semibold mb-1.5"
                               style="color: var(--color-text-primary);">
                            Código de autenticación
                        </label>
                        <input id="code"
                               name="code"
                               type="text"
                               inputmode="numeric"
                               pattern="\d{6}"
                               maxlength="6"
                               required
                               autofocus
                               autocomplete="one-time-code"
                               placeholder="000000"
                               class="w-full px-4 py-3 rounded-xl text-center text-2xl font-mono tracking-[0.5em] outline-none transition-colors duration-150"
                               style="background-color: var(--color-neutral-bg);
                                      border: 1.5px solid var(--color-border);
                                      color: var(--color-text-primary);"
                               onfocus="this.style.borderColor='var(--color-primary)'; this.style.backgroundColor='var(--color-surface)'"
                               onblur="this.style.borderColor='var(--color-border)'; this.style.backgroundColor='var(--color-neutral-bg)'">
                        <p class="mt-1.5 text-xs" style="color: var(--color-text-muted);">
                            Código de 6 dígitos de tu app de autenticación
                        </p>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-white transition-opacity"
                            style="background-color: var(--color-primary);"
                            onmouseover="this.style.opacity='0.9'"
                            onmouseout="this.style.opacity='1'">
                        <i class="fas fa-check mr-2"></i>Verificar
                    </button>
                </form>

                {{-- Código de recuperación --}}
                <form x-show="recovery" x-cloak method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="recovery_code"
                               class="block text-sm font-semibold mb-1.5"
                               style="color: var(--color-text-primary);">
                            Código de recuperación
                        </label>
                        <input id="recovery_code"
                               name="recovery_code"
                               type="text"
                               required
                               autocomplete="one-time-code"
                               placeholder="xxxx-xxxx-xxxx"
                               class="w-full px-4 py-2.5 rounded-xl text-sm font-mono outline-none transition-colors duration-150"
                               style="background-color: var(--color-neutral-bg);
                                      border: 1.5px solid var(--color-border);
                                      color: var(--color-text-primary);"
                               onfocus="this.style.borderColor='var(--color-primary)'; this.style.backgroundColor='var(--color-surface)'"
                               onblur="this.style.borderColor='var(--color-border)'; this.style.backgroundColor='var(--color-neutral-bg)'">
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold text-white transition-opacity"
                            style="background-color: var(--color-primary);"
                            onmouseover="this.style.opacity='0.9'"
                            onmouseout="this.style.opacity='1'">
                        <i class="fas fa-key mr-2"></i>Usar código de recuperación
                    </button>
                </form>

                {{-- Toggle TOTP / recuperación --}}
                <div class="mt-5 pt-4" style="border-top: 1px solid var(--color-border);">
                    <button type="button"
                            x-on:click="recovery = !recovery"
                            class="w-full text-sm text-center transition-opacity"
                            style="color: var(--color-primary);"
                            onmouseover="this.style.opacity='0.75'"
                            onmouseout="this.style.opacity='1'">
                        <span x-show="!recovery">¿No tienes acceso al código? Usar código de recuperación</span>
                        <span x-show="recovery" x-cloak>Usar la aplicación de autenticación</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Volver al login --}}
        <div class="text-center mt-5">
            <a href="{{ route('login') }}"
               class="text-sm transition-colors duration-150"
               style="color: var(--color-text-muted);"
               onmouseover="this.style.color='var(--color-primary)'"
               onmouseout="this.style.color='var(--color-text-muted)'">
                <i class="fas fa-arrow-left mr-1.5"></i>Volver al inicio de sesión
            </a>
        </div>

    </div>
</div>

</body>
</html>
