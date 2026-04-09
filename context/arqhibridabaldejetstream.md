---
name: arq-hibrida-blade-jetstream
description: Arquitectura hibrida para SELGESTIUM 3.0. Usar cuando se modifiquen autenticacion, login/logout/reset/two-factor, rutas web, layouts Blade, navegacion o cualquier decision entre pagina HTML completa y endpoint AJAX explicito. Define la frontera entre Jetstream/Fortify aislado y el ERP Blade.
---

# Arquitectura hibrida Blade + Jetstream/Fortify aislado

## Contexto del incidente

Un refactor previo mezclo dos contratos incompatibles:
- rutas web Blade completas
- un shell global AJAX que esperaba JSON de pantalla (`setRun`, `#frame`, `content`)

Eso rompio paginas como `/dashboard`, contamino el layout principal del ERP y empujo a futuros agentes a tratar toda la app como si fuera un SPA parcial.

## Decision vigente

Mantener Jetstream y Fortify instalados, pero aislados solo como capa de autenticacion base.

### Zona auth soportada
- `/login`
- `/logout`
- `/forgot-password`
- `/reset-password/{token}`
- `/two-factor-challenge`

### Zona auth en cuarentena
- `user/profile`
- formularios Livewire de cuenta
- API tokens
- teams
- cualquier pantalla Jetstream no enlazada por la UI principal

### Registro publico
- `/register` deshabilitado
- los usuarios se crean por seeders o herramientas administrativas

## Contrato del ERP

- Las rutas del ERP devuelven HTML Blade completo.
- El layout principal del ERP usa `{{ $slot }}` y no depende de `#frame`.
- La navegacion del ERP no enlaza a `profile.show`, API tokens ni teams.
- Un controller web de pagina no devuelve JSON de pantalla.

## Contrato AJAX permitido

Usar AJAX solo si el modulo lo pide de forma explicita.

### Reglas
- prefijo recomendado: `/ajax/...`
- nombre recomendado: `ajax.*`
- respuesta: `JSON`
- nunca reutilizar una ruta de pagina para responder a AJAX
- nunca serializar una vista de pagina completa dentro de un payload JSON para navegar el ERP

## Reglas para futuros agentes

- Si tocas auth, rutas web, layouts o navegacion, leer primero este archivo.
- Si el cambio es una pagina del ERP, no usar `setRun()` ni `#frame`.
- Si el cambio es un endpoint AJAX real, recien ahi leer el skill frontend basado en `setRun()`.
- No expandir Jetstream fuera de auth base sin una decision explicita del equipo.
- No reactivar el registro publico sin aprobacion explicita.
- No mezclar cambios de seguridad con migraciones o seeders de dominio ajenos a la tarea.

## Checklist rapido

- `/dashboard` responde HTML
- `/register` no existe
- logout redirige a `/login`
- login mantiene enriquecimiento de sesion
- la UI principal no muestra enlace a `profile.show`
- los tests de auth y seguridad pasan
