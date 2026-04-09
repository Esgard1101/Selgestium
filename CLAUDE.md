# SELGESTIUM 3.0 - Contexto para agentes

## Leer siempre antes de tocar auth, rutas web, layouts o navegacion

@agents/arqhibridabaldejetstream.md
@agents/BACKEND_LARAVELv2.md
@agents/SELGESTIUM_BASE_DE_DATOS.md

## Reglas activas del proyecto

- Arquitectura hibrida: Jetstream/Fortify solo cubre auth base.
- Superficie auth soportada: `/login`, `/logout`, `/forgot-password`, `/reset-password/{token}`, `/two-factor-challenge`.
- Registro publico deshabilitado. No reactivar `/register` sin decision explicita.
- Las rutas del ERP (`/dashboard`, `/expediente/*`, `/pur/*`, `/sustentacion/*`) devuelven HTML Blade completo.
- Solo rutas AJAX explicitas (`/ajax/*` o `ajax.*`) pueden devolver JSON de UI.
- No usar `#frame`, `setRun()` ni un shell AJAX global para navegar paginas del ERP.
- No enlazar desde la UI principal a `profile.show`, API tokens, teams ni otras pantallas Jetstream en cuarentena.
- Preservar las credenciales seed ya acordadas por el equipo con password `password`.

## Leer solo si el cambio es un modulo AJAX explicito

@agents/LARAVEL-FRONTEND-SKILL/SKILL.md
@agents/LARAVEL-FRONTEND-SKILL/references_ajax-contract.md
@agents/LARAVEL-FRONTEND-SKILL/references_css-tokens.md
@agents/LARAVEL-FRONTEND-SKILL/references_grid-variables.md
@agents/LARAVEL-FRONTEND-SKILL/references_library-trait-paradigma.md

## Nota importante

Los documentos del skill frontend basado en `setRun()` son opt-in y no aplican por defecto a rutas Blade del ERP.
