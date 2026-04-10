# 🚀 SPRINT 1 — SELGESTIUM 3.0
**FACHSE · UNPRG · 2026-I**

> **Objetivo MVP:** Sistema funcional y testeable de extremo a extremo en 2 semanas.
> Un estudiante puede radicar su expediente → el sistema ejecuta FAI (flujo manual operativo) → un jurado puede evaluarlo → queda trazabilidad legal inmutable de cada acción.

> **Flujo de ramas:** todo directo a `main`. Cada desarrollador trabaja directo a `main`.

> **⚠ Módulo de Seguridad base ya implementado (Esgardo — completado):**
> Migrations `add_persona_id_to_users`, `update_rolpersona`, `create_dosfactorconfig`, `create_dosfactorcodigo`, `create_dosfactorintento`, `create_bit_accesousuario` · Event Listeners `EnriquecerSesionLogin` / `RegistrarLogout` · Middlewares `VerificarSesion` / `VerificarPermiso` · Services `RoleService`, `BitacoraAccesoService`, `TwoFactorService`.

---

## ⚠️ ARQUITECTURA VIGENTE — leer antes de tocar cualquier vista, ruta o layout

> Esta sección documenta decisiones tomadas después de un incidente real. Ignorarla generará regresiones.

### Contrato de arquitectura

```
FRONTEND = Blade full-page + Tailwind CSS
AUTH     = Jetstream/Fortify aislado (solo login/logout/2FA login)
AJAX     = solo endpoints explícitos bajo /ajax/* que devuelven JSON
```

**Lo que está prohibido:**
- `setRun()`, `#frame`, `#maintenance-modal` global, `setSection()` → estos patrones rompieron `/dashboard` en un refactor anterior. **No reintroducir bajo ningún concepto.**
- Rutas del ERP que devuelvan JSON de UI (el contrato es: ruta ERP → HTML Blade completo)
- Navegar entre módulos del ERP via AJAX interceptando `<a href>` o `window.location` de forma global
- Enlazar desde el ERP a `profile.show`, API tokens, teams ni otras pantallas Jetstream

**Framework CSS:** Tailwind CSS (clases utility). **No** Materialize CSS — el sprint original fue escrito antes del cambio de stack.

**Rutas del menú:** nunca deben requerir parámetros de URL. Si el módulo necesita un `{id}`, la ruta del menú abre un **selector/listado** y desde ahí se navega con el ID. Ver patrón implementado en `sustentacion.programar.index` y `sustentacion.cerrar.index`.

**Columnas reales de la tabla `persona`:** `nombre`, `apellido`, `dni`, `email`, `sucursal_id`. No usar `nombres`, `apellido_paterno`, `apellido_materno` — esas columnas no existen en la migración actual.

**Documentado en:** `agents/arqhibridabaldejetstream.md` · `CLAUDE.md`

---

## 👥 Equipo y módulos

| Persona | Módulo | Responsabilidad |
|---------|--------|-----------------|
| **Esgardo** *(Líder)* | Seguridad & Acceso · Front Base · FAI (implementación) | Auth, RBAC, sidebar, navbar, autocomplete, RF-02.1 (implementa), RF-02.5 (implementa con MD de Jesus), RF-02.6 (implementa con MD de César) |
| **Jesus Salazar** | PUR & Expediente · FAI RF-02.2 (investigación) · FAI RF-02.5 (investigación) | Radicación, documentos, bandeja, timeline, derivación, notificaciones, RF-02.2 (implementa), RF-02.5 (entrega MD de investigación a Esgardo) |
| **César Pisfil** | Calidad Académica · Jurado · Flujo Institucional · FAI RF-02.3 + RF-02.4 (implementa) · FAI RF-02.6 (investigación) | Jurado, observaciones, plazos, sustentación, cierre, RF-02.3 y RF-02.4 (implementa), RF-02.6 (entrega MD de investigación a Esgardo) |
| *Rafael* | — | Fuera del desarrollo crítico por ahora |

> **Distribución FAI — leer con atención:**
>
> | FAI | Código | Implementa | Investiga |
> |-----|--------|-----------|-----------|
> | Créditos académicos | RF-02.1 | Esgardo | Esgardo |
> | Voucher pago proyecto | RF-02.2 | Jesus | Jesus |
> | Grado bachiller | RF-02.3 | César | César |
> | Voucher pago sustentación | RF-02.4 | César | César |
> | Identidad RENIEC | RF-02.5 | **Esgardo** | **Jesus** → entrega MD a Esgardo |
> | Similitud Turnitin | RF-02.6 | **Esgardo** | **César** → entrega MD a Esgardo |
>
> **Jesus y César no implementan RF-02.5 y RF-02.6 respectivamente, pero su investigación es el prerequisito bloqueante para que Esgardo los programe.** Sin el MD de investigación aprobado por el Ing. Ampuero, Esgardo no puede empezar esos features.

---

## 📋 Tabla general del sprint

| Feature | Nombre | Módulo | Fase | Persona |
|---------|--------|--------|:----:|:-------:|
| F-01 | Bootstrap: migraciones núcleo, seeders, .env SaaS | Todos | 0 | Esgardo |
| F-02 | Layout base: app.blade.php, sidebar dinámico, navbar, componentes reutilizables | Seguridad & Front | 1 | Esgardo |
| F-03 | Login + RBAC + menú dinámico por rol | Seguridad & Acceso | 1 | Esgardo |
| F-04 | Sesión segura + historial de acceso administrativo | Seguridad & Acceso | 1 | Esgardo |
| F-05 | Componente `x-autocomplete` reutilizable | Seguridad & Front | 1 | Esgardo |
| F-06 | FAI RF-02.1 · Verificación manual de créditos académicos | FAI | 1 | Esgardo |
| F-07 | Registro de expediente — formulario PUR y numeración automática | PUR & Expediente | 1 | Jesus |
| F-08 | Carga de documentos PDF al expediente | PUR & Expediente | 1 | Jesus |
| F-09 | Bandeja "Mis expedientes" + timeline de fases | PUR & Expediente | 1 | Jesus |
| F-10 | Derivación automática del expediente por fase + notificación base | PUR & Expediente | 1 | Jesus |
| F-11 | FAI RF-02.2 · Verificación manual de voucher de pago (proyecto) | FAI | 1 | Jesus |
| F-12 | Asignación de jurado + resolución de designación | Calidad & Jurado | 1 | César |
| F-13 | Registro de observaciones del jurado con bloqueo RGT | Calidad & Jurado | 1 | César |
| F-14 | FAI RF-02.3 · Grado bachiller + RF-02.4 · Voucher sustentación | FAI | 1 | César |
| **INV-01** | **MD de investigación FAI RF-02.5 (RENIEC)** — entregable de Jesus para Esgardo | FAI | 1 | **Jesus** |
| **INV-02** | **MD de investigación FAI RF-02.6 (Turnitin)** — entregable de César para Esgardo | FAI | 1 | **César** |
| F-15 | 2FA transaccional en acciones críticas | Seguridad & Acceso | 2 | Esgardo |
| F-16 | FAI RF-02.5 · Identidad RENIEC *(requiere INV-01 aprobado)* + RF-02.6 · Turnitin *(requiere INV-02 aprobado)* | FAI | 2 | Esgardo |
| F-17 | Panel FAI unificado: bandeja de verificaciones pendientes + tarjetas semafóricas | FAI | 2 | Esgardo |
| F-18 | Vista detalle de expediente completa (UI, CC, Decanato) | PUR & Expediente | 2 | Jesus |
| F-19 | Validación de requisitos mínimos previo a radicación | PUR & Expediente | 2 | Jesus |
| F-20 | Sincronización RRHH: docente activo/cesado | Seguridad & Acceso | 2 | Jesus |
| F-21 | Control de plazo 15 días + alertas + trigger Art. 123-d | Calidad & Jurado | 2 | César |
| F-22 | Aprobación consolidada 3 jurados + avance automático de fase | Calidad & Jurado | 2 | César |
| F-23 | Programación de sustentación + calendario digital | Flujo Institucional | 2 | César |
| F-24 | Cierre de expediente con acta de sustentación | Flujo Institucional | 2 | César |

---

## ⚠️ Nota de investigación obligatoria — Módulo FAI

> El módulo FAI es el más estratégico y delicado del sistema. **Antes de implementar cualquier feature FAI**, el desarrollador asignado debe:
>
> 1. **Reunirse con el Ing. Ampuero** y presentar la propuesta de flujo manual (pantallas, actores, campos).
> 2. **Confirmar con las autoridades del área correspondiente** (Secretaría DSA, Tesorería DGA, ORC, UI) cómo validan actualmente ese requisito y qué información exacta necesitan ver en pantalla.
> 3. **Documentar la decisión** en un comentario del PR: "Solución validada con [nombre] el [fecha]".
> 4. **Dejar el código preparado para refactor a API:** el Service FAI debe tener `verificarManual()` para el flujo actual y `verificarApi()` vacío con `// TODO: Sprint 2` para la futura integración, sin tocar el Controller ni la vista al hacer el switch.
>
> **El Ing. Ampuero tiene la última palabra sobre el flujo de cada FAI.**

---

## ✅ F-01 · Bootstrap del proyecto — Esgardo · COMPLETADO

`php artisan migrate:fresh --seed` corre limpio. Migrations y seeders base en `main`.

**Lo que otros devs necesitan saber al hacer JOIN con `persona`:**
```sql
-- ✅ Correcto               ❌ No existe
p.nombre                     p.nombres
p.apellido                   p.apellido_paterno / p.apellido_materno
p.dni                        p.numero_documento
```

Usuarios demo (contraseña `password`): `alumno@`, `ui@`, `cc@`, `asesor@`, `decano@`, `admin@selgestium.com`.

---

## ✅ F-02 · Layout base: app.blade.php, sidebar dinámico, navbar — Esgardo · COMPLETADO

Layout full-page Blade funcional. Sidebar genera `route($item->ruta_nombre)` para todos los ítems del menú.

**Lo que otros devs necesitan saber:**

- Extender con `<x-app-layout>…</x-app-layout>` — sin `@extends`.
- Tokens CSS ya disponibles en `:root`: `--color-primary`, `--color-accent`, `--color-success`, `--color-danger`, `--color-warning`. Usar estos en Tailwind arbitrary values o CSS inline; no hardcodear hex.
- **Regla crítica — rutas del menú:** `opcionmenu.ruta_nombre` debe resolver `route($nombre)` **sin parámetros**. Si tu módulo necesita `{id}`, agrega una ruta `*.index` parameterless que actúe de selector. Ver `sustentacion.programar.index` como ejemplo. Romper esta regla lanza `UrlGenerationException` en el sidebar de todos los usuarios.
- AJAX solo bajo `/ajax/*` devolviendo `[{id, label, sublabel}]`. No usar AJAX para cargar páginas completas del ERP.

---

## ✅ F-03 · Login + RBAC + menú dinámico por rol — Esgardo · COMPLETADO

Auth vía Fortify, RBAC con `VerificarPermiso`, sidebar dinámico por rol funcionando.

**Lo que otros devs necesitan saber:**

- Para agregar una opción al menú: insertar fila en `opcionmenu` + relación en la tabla pivote del rol. Usar `OpcionmenuSeeder` como plantilla (cada ítem lleva `id`, `ruta_nombre`, `descripcion`, `icono`, `grupo`, `orden`).
- Para proteger una ruta con permiso: agregarla en `config/rutasopcion.php` → `'mi.ruta' => $opcionmenu_id`. El middleware `VerificarPermiso` la bloquea automáticamente si el rol no tiene ese ID.
- `VerificarSesion` redirige a `/login` si no hay sesión. No agregar `auth` de Jetstream a rutas del ERP — usar el middleware propio.

---

## ✅ F-04 · Sesión segura + historial de acceso — Esgardo · COMPLETADO

Bitácora de acceso en `/seguridad/bitacora-acceso` (ruta `bitacora.acceso.index`). Solo lectura, solo rol `admin`.

**Lo que otros devs necesitan saber:**

- Cada login/logout queda en `bit_accesousuario` automáticamente vía event listeners. No llamar el service manualmente.
- La tabla es inmutable — sin update, sin delete.
- Paginación server-side con links Blade nativos. **No** jQuery DataTables (no está en el stack).

---

## ✅ F-05 · Componente `x-autocomplete` reutilizable — Esgardo · COMPLETADO

**Cómo usarlo en cualquier formulario:**

```blade
<x-autocomplete
    url="/ajax/personas/search"
    name="estudiante_id"
    placeholder="Buscar estudiante por nombre o DNI..."
/>
```

El componente escribe el `id` seleccionado en el input hidden `name` y muestra el `label` visible. Al limpiar el campo borra el hidden.

**Para agregar un nuevo endpoint de búsqueda:**
1. Crear `GET /ajax/{recurso}/search?q=` en `routes/web.php` bajo el grupo `ajax.*`.
2. Devolver `[{"id": 1, "label": "Nombre visible", "sublabel": "Dato secundario"}]`. Nada más.
3. Pasar la URL al atributo `url` del componente.

Endpoint `/ajax/personas/search` ya existe. Para asesores, crear `/ajax/asesores/search` filtrando `rolpersona.rol_id = asesor`.

---

## ✅ F-06 · FAI RF-02.1 · Créditos académicos — Esgardo · COMPLETADO

Verificación manual operativa. Operador: rol `ui`. Ruta: `GET/POST /fai/creditos`.

**Patrón obligatorio para F-11 (Jesus) y F-14 (César) — cualquier FAI nuevo debe seguir esto:**

1. **Crear `{Nombre}Service.php`** con exactamente dos métodos públicos:
   ```php
   public function verificarManual(int $expedienteId, /* datos manuales */, int $validadoPorId, string $ip): object
   // Inserta en fairesultado (inmutable: solo created_at, sin updated_at/deleted_at)
   // Resuelve umbral desde tabla `parametro` — nunca hardcodear valores
   
   public function verificarApi(int $expedienteId): void
   {
       // TODO: Sprint 2 — integrar [nombre API]
   }
   ```

2. **`fairesultado` es inmutable.** Solo `created_at`. Campos clave: `expediente_id`, `fai_codigo` (`RF02.X`), `estado` (`aprobado|rechazado|pendiente|error`), `valor_obtenido`, `valor_umbral`, `validado_por_id`.

3. **Umbral desde `parametro`**, nunca hardcodeado:
   ```php
   // Override por sucursal primero, luego global (sucursal_id IS NULL)
   DB::table('parametro')->where('codigo', 'MI_PARAMETRO')->where('sucursal_id', $id)->value('valor')
   ?? DB::table('parametro')->where('codigo', 'MI_PARAMETRO')->whereNull('sucursal_id')->value('valor')
   ```

4. **Ruta del menú sin parámetros** → abre selector de expedientes en la fase correcta → desde el selector se navega al form con `{id}`.

Ver `FaiDsaService.php` y `FaiController.php` como implementación de referencia.

---

## 📝 F-07 · Registro de expediente — formulario PUR y numeración automática — Jesus · Fase 1

**Migraciones:**
- `create_expedientes_table` — campos ya existentes en migración vigente. Verificar que incluya: `numero_radicacion` (unique), `sucursal_id`, `estudiante_id`, `asesor_id`, `titulo`, `tipo`, `etapa`, `fase_actual`, `estado`, `timestamps`, `softDeletes`
- `create_det_expedientecoautor_table` — `id`, `expediente_id`, `persona_id`, `created_at`

> ⚠ La tabla `expediente` ya existe. Usar `php artisan migrate` — no recrear con `migrate:fresh` si hay data.

**Migration de datos:**
- Ítem "Radicar Expediente" en `opcionmenu` para roles `alumno` y `asesor`

**Services:**
- `ExpedienteService::store(array $data)` → genera número `EXP-{YYYY}-{SUC_ID}-{SEQ}` dentro de `DB::transaction()`, inserta via `DataBaseTrait`
- `ExpedienteService::generarNumeroRadicacion(sucursal_id)` → privado, `SELECT MAX + 1` con lock dentro de la transaction

**FormRequest:**
- `StoreExpedienteRequest` → `titulo` required max:300, `asesor_id` exists in `persona`. `prepareForValidation()` inyecta `sucursal_id` y `estudiante_id` desde sesión

**Vistas Blade (Tailwind):**
- `pur/create.blade.php` → secciones: datos del proyecto, asesor (usa `<x-autocomplete url="/ajax/asesores/search">`)
- Validación inline via `@error` de Blade en cada campo

**Criterio de aceptación:**
- Expediente creado en fase 1, estado `pendiente`, número único
- Dos peticiones simultáneas no generan el mismo `numero_radicacion`
- Campos requeridos vacíos muestran error inline en el input correcto

---

## 📎 F-08 · Carga de documentos PDF al expediente — Jesus · Fase 1

**Migraciones:**
- `create_det_expedientedocumento_table` — `id`, `expediente_id`, `tipo_documento`, `nombre_original`, `ruta_almacenamiento`, `tamanio_bytes`, `hash_sha256`, `subido_por_id`, `activo`, `timestamps`, `softDeletes`

**Services:**
- `ExpedienteService::adjuntarDocumento(expediente_id, tipo, UploadedFile $file, subido_por_id)` → `LibraryTrait::savePdf('expedientes/{expediente_id}', $file)`, calcula `hash_sha256`, inserta en `det_expedientedocumento`
- Máx. 10MB, solo `application/pdf`

**FormRequest:**
- `UploadDocumentoRequest` → `expediente_id` exists, `tipo_documento` in lista válida, `archivo` required | mimes:pdf | max:10240

**Vistas Blade (Tailwind):**
- Sección adjuntos en formulario PUR: lista de documentos con nombre, tipo, tamaño, enlace de descarga

**Criterio de aceptación:**
- Archivo guardado en `storage/public/expedientes/{expediente_id}/{uuid}.pdf`
- PDF > 10MB rechazado con mensaje claro en el campo

---

## 📬 F-09 · Bandeja "Mis expedientes" + timeline de fases — Jesus · Fase 1

**Migraciones:**
- `create_det_expedientefase_table` — `id`, `expediente_id`, `fase_id`, `estadoexpediente_id`, `actor_id`, `observacion`, `ip_actor`, `fecha_inicio`, `fecha_fin`, `created_at`. **Inmutable** — sin `updated_at` ni `deleted_at`

**Services:**
- `ExpedienteService::listarPorEstudiante(estudiante_id, sucursal_id, filtros)` → Collection paginada
- `ExpedienteService::registrarCambioFase(expediente_id, nueva_fase_id, actor_id, ip)` → inserta en `det_expedientefase` + actualiza `expediente.fase_actual` en `DB::transaction()`

**Vistas Blade (Tailwind):**
- `expediente/list.blade.php` → tabla Blade: número radicación, título, fase actual (badge), estado (badge semafórico), fecha, acciones
- `expediente/show.blade.php` → pestañas Blade: Información · Documentos · FAI · Timeline
- Timeline: lista ordenada de `det_expedientefase` con actor y timestamp por nodo

> **Sin DataTables:** paginación server-side de Laravel, ordenamiento por `created_at DESC`.

**Criterio de aceptación:**
- Estudiante solo ve sus expedientes (filtrado por `estudiante_id` de sesión)
- Timeline muestra cada cambio de fase con actor y fecha/hora
- Expediente en fase 3 muestra exactamente 3 nodos completados

---

## 🔀 F-10 · Derivación automática + notificación base — Jesus · Fase 1

**Migraciones:**
- `create_plantillanotificacion_table` — `id`, `sucursal_id` (NULL=global), `codigo`, `canal`, `asunto`, `cuerpo`, `activo`, `timestamps`
- `create_notificaciones_table`
- `create_det_notificaciondestinatario_table`

**Services:**
- `ExpedienteService::derivar(expediente_id, nueva_fase_id, actor_id, ip)` → regla: fases 1-3 → UI, fase 4 → CC, fase 5+ → Jurado. Actualiza fase, registra en `det_expedientefase`, encola notificación
- `NotificacionService::encolar(expediente_id, codigo_plantilla, destinatarios[])` → insert en `notificaciones` + `det_notificaciondestinatario`

**Criterio de aceptación:**
- Al radicar (fase 1) se deriva automáticamente a UI
- Derivación registrada en `det_expedientefase` con actor y timestamp
- Al menos 1 notificación encolada al radicar

---

## 💳 F-11 · FAI RF-02.2 · Verificación manual de voucher de pago (proyecto) — Jesus · Fase 1

> **🔎 INVESTIGAR ANTES DE IMPLEMENTAR — validar con Ing. Ampuero**

**Preguntas que Jesus debe responder y documentar:**
1. ¿Quién verifica el voucher: la UI, la Tesorería directamente, o ambas?
2. ¿El voucher tiene un número de operación estándar que deba registrarse?
3. ¿Existe un sistema de la DGA con consulta en línea para el Sprint 2?

**Patrón a seguir (igual que F-06):**
- Service: `FaiDgaService::verificarProyectoManual(expediente_id, numero_operacion, validado_por_id, ip)`
- Service: `FaiDgaService::verificarProyectoApi(expediente_id)` → vacío `// TODO: Sprint 2`
- FormRequest: `StoreVerificacionVoucherRequest` → `expediente_id` exists, `numero_operacion` required | string | max:50
- Vista Blade (Tailwind): `fai/verificacion_voucher_proyecto.blade.php` → misma estructura semafórica que `verificacion_creditos.blade.php`
- Ruta del menú sin parámetros → selector de expediente → formulario

**Criterio de aceptación:**
- No se puede verificar sin voucher adjunto (validación en Service)
- `verificarProyectoApi()` existe con comentario TODO
- PR incluye `fai_rf022_decision.md` con flujo validado por el Ing. Ampuero

---

## 👨‍⚖️ F-12 · Asignación de jurado + resolución de designación — César · Fase 1

**Migraciones:**
- `create_det_expedientejurado_table` — `id`, `expediente_id`, `jurado_id`, `rol_jurado`, `fecha_asignacion`, `resolucion_id`, `aprobado` (NULL=pendiente), `fecha_evaluacion`, `codigo_2fa_usado`, `activo`, `timestamps`. Restricción única: `(expediente_id, jurado_id, rol_jurado)`
- `create_resoluciones_table` — `id`, `expediente_id`, `sucursal_id`, `tiporesolucion_id`, `numero_resolucion`, `fecha_emision`, `documento_url`, `emitido_por_id`, `timestamps`, `softDeletes`

**Migration de datos:**
- Ítems en `opcionmenu`: "Asignar Jurado" (rol `cc`), "Mis Revisiones" (rol `jurado`)
- Ambos ítems con `ruta_nombre` sin parámetros de URL

**Services:**
- `JuradoService::asignar(expediente_id, array $jurados, actor_id)` → exactamente 3, roles únicos, ninguno puede ser el asesor del expediente
- `JuradoService::registrarResolucion(expediente_id, numero, fecha, emitido_por_id)`

**FormRequest:**
- `StoreAsignacionJuradoRequest` → 3 jurados distintos, existen en `persona` con rol `jurado`, ninguno es el asesor

**Vistas Blade (Tailwind):**
- `jurado/asignar.blade.php` → formulario con 3 campos `<x-autocomplete url="/ajax/personas/search">` (presidente, secretario, vocal) + campo número de resolución. Página completa, no modal global.
- `jurado/mis_revisiones.blade.php` → listado del jurado: expedientes asignados con estado

**Criterio de aceptación:**
- Solo exactamente 3 jurados con roles distintos
- Docente que es asesor del expediente no puede ser asignado como jurado
- Resolución vinculada a los 3 registros de `det_expedientejurado`

---

## 💬 F-13 · Registro de observaciones del jurado con bloqueo RGT — César · Fase 1

**Migraciones:**
- `create_det_expedienteobservacion_table` — `id`, `expediente_id`, `jurado_id`, `tipoobservacion_id`, `ronda`, `descripcion`, `bloqueado`, `subsanado`, `fecha_subsanacion`, `timestamps`, `softDeletes`

**Services:**
- `ObservacionService::registrar(expediente_id, jurado_id, tipoobservacion_id, descripcion, actor_id)`:
  1. `MAX(ronda) WHERE expediente_id=X AND jurado_id=Y`
  2. Si ya existe observación con `ronda=1` → lanza excepción bloqueada por RGT
  3. Si no → inserta con `ronda=1`, registra en `bit_expediente`
- `ObservacionService::marcarSubsanado(observacion_id, actor_id)` → `subsanado=true`, `fecha_subsanacion=now()`

**FormRequest:**
- `StoreObservacionRequest` → `expediente_id` exists, `tipoobservacion_id` exists, `descripcion` required min:20

**Vistas Blade (Tailwind):**
- `jurado/observaciones.blade.php` → formulario + lista de observaciones previas por ronda con badges de estado
- Si `bloqueado=true`: botón "Nueva Observación" deshabilitado con badge "🔒 Bloqueado por RGT"

**Criterio de aceptación:**
- Observación ronda 2 lanza error: "Observaciones adicionales bloqueadas por el RGT"
- Estudiante puede ver observaciones pero no editarlas
- `bit_expediente` registra cada observación con actor + IP + timestamp

---

## 🏛️ F-14 · FAI RF-02.3 · Grado bachiller + RF-02.4 · Voucher sustentación — César · Fase 1

> **🔎 INVESTIGAR ANTES DE IMPLEMENTAR — validar con Ing. Ampuero**

**Patrón a seguir (igual que F-06):**
- Services: `FaiSuneduService::verificarManual()` + `verificarApi()` vacío // TODO
- Services: `FaiDgaService::verificarSustentacionManual()` + `verificarSustentacionApi()` vacío // TODO
- Vistas Blade (Tailwind) con estructura semafórica idéntica a `verificacion_creditos.blade.php`
- Rutas del menú sin parámetros → selector de expediente → formulario
- Ambos FAI aplican solo en Etapa II (fase 8+); retornan `no_aplica` si se llaman en Etapa I

**Criterio de aceptación:**
- RF-02.3 y RF-02.4 retornan `no_aplica` si se ejecutan en Etapa I
- Ambos `*Api()` existen con comentario TODO
- PR incluye `fai_rf023_decision.md` y `fai_rf024_decision.md` validados por el Ing. Ampuero

---

## 🔍 INV-01 · MD de investigación FAI RF-02.5 (RENIEC) — Jesus · Fase 1

> **Entregable de investigación — no requiere código.**

**Preguntas a responder:**
1. ¿Existe integración con RENIEC en otros sistemas de la UNPRG reutilizable?
2. Si no hay API: ¿la UI compara el DNI del expediente contra carnet físico, o se asume validado al registrar la cuenta?
3. ¿La alerta RENIEC bloquea el expediente o solo lo marca como "pendiente de confirmación"?

**Formato:** `INV-01_RENIEC.md` con: flujo actual, flujo propuesto acordado, campos del formulario, disponibilidad de API, notas para Esgardo.

**Criterio de aceptación:**
- Documento completado y validado con firma/confirmación del Ing. Ampuero
- Entregado a Esgardo antes del inicio de la Semana 2

---

## 🔍 INV-02 · MD de investigación FAI RF-02.6 (Turnitin) — César · Fase 1

> **Entregable de investigación — no requiere código.**

**Preguntas a responder:**
1. ¿La FACHSE/UNPRG tiene contrato Turnitin con acceso a la API REST?
2. ¿El asesor descarga el PDF del reporte y lo entrega impreso o se sube digitalmente?
3. Si no hay API: ¿el asesor sube el PDF y un operador ingresa el porcentaje manualmente?

**Formato:** `INV-02_TURNITIN.md` con: situación actual, disponibilidad de API, flujo acordado, quién ingresa el porcentaje, umbrales confirmados, notas para Esgardo.

**Criterio de aceptación:**
- Documento completado y validado con firma/confirmación del Ing. Ampuero
- Entregado a Esgardo antes del inicio de la Semana 2

---

## 🔑 F-15 · 2FA transaccional en acciones críticas — Esgardo · Fase 2

> *Tablas ya creadas. Esta feature cierra el circuito visual e integra las acciones críticas.*

**Migraciones:**
- `create_bit_firma_table` — `id`, `expediente_id`, `firmante_id`, `rol_firmante`, `tipo_firma`, `codigo_2fa_usado`, `ip_actor`, `created_at`. **Inmutable**

**Services:**
- `TwoFactorService::generar(usuario_id, proposito, expediente_id)` → OTP 6 dígitos, expira en `TWO_FACTOR_EXPIRY_MINUTES` minutos (de `.env`)
- `TwoFactorService::verificar(usuario_id, codigo, proposito)` → valida no expirado y no usado
- `TwoFactorService::registrarFirma(expediente_id, firmante_id, rol, tipo, codigo, ip)` → insert en `bit_firma`

**Vistas Blade (Tailwind):**
- `fai/confirmar_2fa.blade.php` → página completa (no modal global) con input 6 dígitos, contador 5 min, botón "Reenviar código". Al verificar: POST a endpoint de confirmación y redirige a la acción completada.
- No usar modal JS global. Cada acción que requiere 2FA redirige a esta página con `?proposito=X&expediente_id=Y` y al confirmar redirige de vuelta.

**Acciones que requieren 2FA:**
- Firma del asesor en reporte Turnitin
- Aprobación/observación del jurado
- Emisión de resolución por Decanato

**Criterio de aceptación:**
- Aprobar expediente sin 2FA → redirige a la página de confirmación 2FA
- Código expirado rechazado con mensaje claro
- Cada uso de 2FA en `bit_firma` con código y timestamp

---

## 🔬 F-16 · FAI RF-02.5 · Identidad RENIEC + RF-02.6 · Turnitin — Esgardo · Fase 2

> **Prerequisito bloqueante:** Esgardo no empieza hasta tener `INV-01_RENIEC.md` e `INV-02_TURNITIN.md` validados.
> **Fallback si los MDs llegan tarde:** implementar con flujo de comparación interna de DNI (RF-02.5) e ingreso manual de porcentaje (RF-02.6), ambos marcados `// TODO: ajustar según decisión Ampuero`.

**Patrón a seguir (igual que F-06):**
- Services con `verificarManual()` + `verificarApi()` vacío // TODO
- Vistas Blade (Tailwind) con selector de expediente → formulario semafórico
- Rutas sin parámetros en el menú

---

## ✅🚦 F-17 · Panel FAI unificado: bandeja + tarjetas semafóricas — Esgardo · Fase 2 COMPLETADO

**Migration de datos:**
- Ítem "Panel FAI" en `opcionmenu` para roles `ui`, `cc`, `admin`

**Services:**
- `FaiService::verificacionesPendientes(sucursal_id)` → expedientes con al menos 1 FAI sin verificar
- `FaiService::resultadosParaExpediente(expediente_id)` → últimos resultados de cada `fai_codigo`

**Vistas Blade (Tailwind):**
- `fai/bandeja.blade.php` → listado de expedientes con verificaciones pendientes: número, estudiante, fase, FAIs faltantes (badges)
- `fai/panel_expediente.blade.php` → 6 tarjetas FAI en grid:
  - Estado semafórico: verde (aprobado), rojo (rechazado), amarillo (pendiente), gris (no_aplica)
  - Valor obtenido vs umbral, quién validó, timestamp
  - Botón "Verificar" si pendiente → enlace Blade `<a href="{{ route('fai.creditos.show', ...) }}">` (no `setRun()`)

> ⚠ El botón "Verificar" usa `<a href>` con ruta Blade, no `setRun()`. Cada FAI tiene su propia URL de verificación.

**Criterio de aceptación:**
- Panel muestra solo expedientes de la sucursal activa
- Resultado `rechazado` muestra el motivo
- Botón "Verificar" navega a la página de verificación correcta (navegación completa, no AJAX)

---

## 🗂️ F-18 a F-24 · Fase 2 — Sin cambios de arquitectura

Los features F-18 al F-24 mantienen el mismo contrato que los anteriores:
- Páginas Blade completas (Tailwind), sin `setRun()` ni `#frame`
- Rutas en el menú sin parámetros obligatorios
- Queries con JOIN a `persona` usan `p.nombre` y `p.apellido`
- Controllers con try/catch devuelven `collect()` vacío en error de BD (no 500)
- Todos los selectores de expediente/sustentación son páginas index que filtran por fase activa

---

## 🗂️ F-18 · Vista detalle de expediente completa — UI, CC, Decanato — Jesus · Fase 2

**Services:**
- `ExpedienteService::detalleCompleto(expediente_id, sucursal_id)` → expediente + documentos + fases + FAI + jurados + observaciones + plazos

**Vistas Blade:**
- `expediente/detalle_ui.blade.php` → pestañas: Información · Documentos · FAI · Jurado · Timeline · Plazos
- Botón "Derivar a siguiente fase" con confirmación SweetAlert (solo roles `ui` y `cc`)

**Criterio de aceptación:**
- UI puede descargar todos los documentos del expediente
- Decanato puede ver lista de jurados y resultado de evaluación
- Botón "Derivar" solo aparece para roles autorizados

---

## 🛡️ F-19 · Validación de requisitos mínimos previo a radicación — Jesus · Fase 2

**Services:**
- `ExpedienteService::cumpleRequisitosMinimos(array $data)` → PDF adjunto, tamaño ≤ 10MB, asesor activo, campos completos

**FormRequest (actualización `StoreExpedienteRequest`):**
- Hook `after` que llama `cumpleRequisitosMinimos()`. Si falla: HTTP 422 con errores por campo

**Vistas Blade:**
- Checklist visual en `pur/create.blade.php`: ✓ PDF adjunto · ✓ Asesor activo · ✓ Campos completos

**Criterio de aceptación:**
- Sin PDF: rechazado con mensaje en el campo correspondiente
- Asesor inactivo en RRHH: "Asesor no disponible en el sistema"

---

## 🏢 F-20 · Sincronización RRHH: docente activo/cesado — Jesus · Fase 2

**Migraciones:**
- `create_docente_rrhh_sync_table` — `id`, `persona_id`, `activo_rrhh`, `cargo`, `regimen`, `sincronizado_at`, `fuente`, `timestamps`

**Services:**
- `RrhhSyncService::estaActivo(persona_id)` → consulta `docente_rrhh_sync`. Si sin registro reciente: toma `persona.activo` como fallback
- `RrhhSyncService::bloquearCesados()` → Job nocturno: `usuario.bloqueado=true` para `activo_rrhh=false`
- En `JuradoService::asignar()`: llama `RrhhSyncService::estaActivo()` antes de insertar

**Vistas Blade:**
- En autocompletado de asignación de jurado: badge "Cesado 🔴" para docentes inactivos, no seleccionables

**Criterio de aceptación:**
- Docente `activo_rrhh=false` no puede ser asignado como jurado
- El autocompletado muestra el badge visualmente antes del error al guardar

---

## ⏱️ F-21 · Control de plazo 15 días + alertas + trigger Art. 123-d — César · Fase 2

**Migraciones:**
- `create_controlplazo_table` — `id`, `expediente_id`, `fase_id`, `fecha_inicio`, `fecha_vencimiento`, `dias_habiles`, `vencido`, `art123d_habilitado`, `timestamps`
- `create_alertaplazo_table` — `id`, `controlplazo_id`, `expediente_id`, `destinatario_id`, `tipo_alerta` (pre_vencimiento/vencimiento/art123d), `canal`, `enviado_at`, `created_at`

**Migration de datos:**
- `insert_menu_control_plazos` → ítem "Control de Plazos" para roles `ui` y `cc`

**Services:**
- `PlazoService::iniciar(expediente_id, fase_id)` → calcula `fecha_vencimiento` excluyendo fines de semana
- `PlazoService::verificarVencidos()` → Job nocturno: actualiza `vencido=true`, encola alertas
- `PlazoService::verificarArt123d(expediente_id)` → si vencido y exactamente 2/3 jurados aprobaron → `art123d_habilitado=true`

**Vistas Blade:**
- `plazo/dashboard.blade.php` → 3 tarjetas: vencidos hoy, por vencer en 3 días, con Art.123-d habilitado + DataTable con días restantes (badge rojo si ≤ 0)

**Criterio de aceptación:**
- Al entrar a fase 6 se crea `controlplazo` con 15 días hábiles
- Al vencer: `vencido=true` y alerta en `alertaplazo`
- Con 2/3 jurados aprobados y plazo vencido: `art123d_habilitado=true` visible en dashboard

---

## 🗳️ F-22 · Aprobación consolidada 3 jurados + avance automático de fase — César · Fase 2

**Services:**
- `JuradoService::registrarVeredicto(expediente_id, jurado_id, aprobado, codigo_2fa, ip)`:
  1. Verifica 2FA via `TwoFactorService::verificar()`
  2. Actualiza `det_expedientejurado.aprobado` y `fecha_evaluacion`
  3. Registra en `bit_firma`
  4. Llama `verificarAprobacionConsolidada()`
- `JuradoService::verificarAprobacionConsolidada(expediente_id)`:
  - 3/3 aprobados → avanza fase via `ExpedienteService::derivar()`, estado `aprobado`
  - 1 desaprueba → estado `en_subsanacion`, notifica al estudiante
  - 2/3 aprobados + plazo vencido → `PlazoService::verificarArt123d()`

**Vistas Blade:**
- `jurado/veredicto.blade.php` → modal: botón "Aprobar" (verde) / "Registrar observación" (naranja). Antes de confirmar → modal 2FA
- Dashboard jurado: progreso "1/3 jurados aprobaron" con avatares y estado individual

**Criterio de aceptación:**
- Aprobación requiere código 2FA válido
- Con 3 aprobaciones: expediente avanza solo sin intervención manual
- Con 1 desaprobación: estado `en_subsanacion` + notificación al estudiante

---

## 📅 F-23 · Programación de sustentación + calendario digital — César · Fase 2

**Migraciones:**
- `create_sustentacion_table` — `id`, `expediente_id` (unique), `sucursal_id`, `fecha_hora`, `lugar`, `modalidad`, `enlace_virtual`, `resolucion_id`, `estado`, `nota_final`, `resultado`, `acta_url`, `timestamps`, `softDeletes`

**Migration de datos:**
- `insert_menu_sustentacion` → ítems "Programar Sustentación" (roles `ui`, `cc`) y "Calendario" (todos los roles)

**Services:**
- `SustentacionService::programar(expediente_id, fecha_hora, lugar, modalidad, resolucion_id, actor_id)` → valida plazo 7 días hábiles, valida no colisión sala + hora
- `SustentacionService::calendario(sucursal_id, mes, anio)` → sustentaciones del mes

**Vistas Blade:**
- `sustentacion/programar.blade.php` → datetime picker, campo lugar, toggle presencial/virtual, URL Meet si virtual
- `sustentacion/calendario.blade.php` → vista mensual: días del mes, sustentaciones como bloques de color con estudiante y hora

**Criterio de aceptación:**
- No se puede programar fuera del plazo de 7 días hábiles
- No se pueden programar dos sustentaciones en el mismo lugar y hora
- Calendario muestra todas las sustentaciones del mes de la facultad

---

## 📋 F-24 · Cierre de expediente con acta de sustentación — César · Fase 2

**Migraciones:**
- `create_actasustentacion_table` — `id`, `sustentacion_id` (unique), `expediente_id`, `nota_jurado1`, `nota_jurado2`, `nota_jurado3`, `nota_promedio`, `resultado`, `observaciones_acta`, `acta_url`, `firmado_por_presidente_at`, `timestamps`

**Services:**
- `SustentacionService::registrarActa(sustentacion_id, notas[], resultado, observaciones)` → calcula promedio en Service, inserta en `actasustentacion`, si aprobado → llama `ExpedienteService::cerrar()`
- `ExpedienteService::cerrar(expediente_id, actor_id)` → `estadoexpediente_id=cerrado`, `fecha_cierre=now()`, registra en `bit_expediente` con `accion='cierre_expediente'`

**Vistas Blade:**
- `sustentacion/acta.blade.php` → 3 campos nota (0-20), selector resultado, textarea observaciones. Botón "Cerrar Expediente" solo para `presidente_jurado`
- Expediente cerrado → readonly, badge "Cerrado ✓" verde, sin botones de acción

**Criterio de aceptación:**
- Expediente cerrado no acepta más cambios de estado
- Nota promedio calculada en Service, no en la vista
- Cierre en `bit_expediente` con actor + IP + timestamp

## 📌 Checklist de PR — aplicable a cualquier feature del sprint

```
Arquitectura
  [ ] No hay setRun(), #frame, #maintenance-modal global en el código nuevo
  [ ] Rutas del menú resuelven route($nombre) sin parámetros
  [ ] Controller devuelve view() o redirect(), nunca response()->json() de UI
  [ ] AJAX solo bajo /ajax/* con respuesta {id, label, sublabel}

Base de datos
  [ ] Queries con JOIN a persona usan p.nombre y p.apellido (no nombres, apellido_paterno)
  [ ] Tablas inmutables (fairesultado, det_expedientefase, bit_*) sin updated_at ni deleted_at
  [ ] Umbrales y configuración leídos de parametro, nunca hardcodeados
  [ ] migrate:fresh --seed corre sin errores

Blade + Tailwind
  [ ] Vistas usan clases Tailwind CSS — no clases Materialize (row, col, btn, waves-effect, etc.)
  [ ] Errores de FormRequest mostrados via @error('campo') en cada campo
  [ ] Controllers con try/catch — error de BD retorna collect() vacío, no 500

FAI
  [ ] verificarManual() implementado
  [ ] verificarApi() existe vacío con // TODO: Sprint 2
  [ ] Resultado insertado en fairesultado con validado_por_id, verificado_at, created_at
  [ ] PR referencia el documento de investigación validado por el Ing. Ampuero
```
