# SELGESTIUM — Contexto Maestro del Proyecto
> **Uso:** Entregar este archivo completo como contexto inicial a cualquier agente antes de pedirle cualquier tarea.  
> **Versión:** 1.0.0 · 2026-I · FACHSE–UNPRG

---

## ÍNDICE DE SECCIONES

| # | Sección | Para qué agente es crítica |
|---|---------|---------------------------|
| 1 | Identidad del Proyecto | Todos |
| 2 | El Problema Real (por qué existe esto) | Todos |
| 3 | Actores del Sistema | Todos |
| 4 | El Proceso: Las 11 Fases | Todos |
| 5 | Requerimientos Funcionales | Arquitecto · Sprint |
| 6 | Stack Tecnológico y Paradigmas | Todos |
| 7 | Arquitectura de Base de Datos | Arquitecto · Backend |
| 8 | Arquitectura Backend Laravel | Agente Backend · Sprint |
| 9 | Arquitectura Frontend Blade+SCSS | Agente Frontend · Sprint |
| 10 | Paleta Visual e Identidad UI | Frontend |
| 11 | Instrucciones por Tipo de Agente | Lectura obligatoria por agente |

---

## 1. IDENTIDAD DEL PROYECTO

```
Nombre del sistema : SELGESTIUM
Tipo               : SaaS universitario — una instancia por institución
Cliente piloto     : FACHSE — Facultad de Ciencias Histórico Sociales y Educación
                     Universidad Nacional Pedro Ruiz Gallo (UNPRG) · Lambayeque, Perú
Líder del proyecto : Ing. Martin Ampuero
Equipo             : Mechan Yaipen Esgardo · Pisfil Cachay Cesar Augusto
                     Salazar Flores Jesus · Cuyuche Sernaque Rafael
Período            : 2026-I
Repositorio        : GitHub Flow — rama collabs como integración, main como producción
```

**¿Qué es SELGESTIUM en una línea?**  
Un sistema web que reemplaza el correo institucional y la revisión manual de expedientes de tesis por un portal digital con validaciones automáticas en tiempo real, trazabilidad legal completa y control de calidad académica.

---

## 2. EL PROBLEMA REAL

Entender los problemas es obligatorio para cualquier agente. Cada decisión técnica existe para resolver uno de estos ejes.

### Eje 1 — Canal informal de recepción
El correo institucional actúa como "Mesa de Partes no autorizada". Consecuencias:
- La Unidad de Investigación (UI) descarga archivos manualmente y notifica por email.
- Se superan los plazos normativos de **2 días hábiles** para verificación de requisitos.
- Imposible conocer el estado real de un trámite sin llamar por teléfono.

**Solución técnica:** Portal Único de Radicación (PUR) — módulo `expediente`.

### Eje 2 — Seguridad y No Repudio
Solo usuario + contraseña. Consecuencias:
- Docentes reportan cuentas bloqueadas por cambios no autorizados.
- Imposible atribuir legalmente quién aprobó o firmó en el sistema.

**Solución técnica:** Módulo 2FA (`dosfactorconfig`, `dosfactorcodigo`) + `bit_firma` inmutable.

### Eje 3 — Verificaciones manuales
La UI revisa a mano: vouchers de pago, 160 créditos académicos, grado de bachiller (SUNEDU), similitud Turnitin. Consecuencias:
- Acumulación de expedientes.
- Personal desviado de su función real: control de calidad académica.

**Solución técnica:** FAI — Filtros Administrativos Automatizados. 6 filtros conectados a APIs externas.

### Eje 4 — Control de calidad académica
- Jurados agregan nuevas observaciones en rondas de subsanación (prohibido por RGT).
- Incumplimiento de plazos de 15 días para revisión y 7 días para programar sustentación.
- Aplicación inconsistente del Art. 123-d (cuando vence el plazo con 2/3 jurados aprobados).

**Solución técnica:** Bloqueo RGT en `det_expedienteobservacion` + `controlplazo` + `alertaplazo`.

---

## 3. ACTORES DEL SISTEMA

| Actor | Código de rol | Función principal |
|-------|--------------|-------------------|
| Estudiante / Egresado | `alumno` | Radica expediente, sube archivos, subsana observaciones |
| Asesor | `asesor` | Revisa el trabajo, firma reporte Turnitin, aprueba para presentar |
| Asesor Externo | `asesor_externo` | Igual que asesor pero sin cuenta institucional |
| Jurado (×3) | `jurado` | Evalúa proyecto e informe, registra aprobación u observaciones con 2FA |
| Unidad de Investigación | `ui` | Coordina fases, gestiona resoluciones, supervisa FAI |
| Comité Científico | `cc` | Designa jurados, valida calidad académica |
| Decanato | `decanato` | Emite resoluciones oficiales (designación, aprobación, sustentación) |
| Sistema FAI | *(automatizado)* | Valida requisitos en tiempo real vía APIs externas |

**Regla de permisos:** cada actor accede únicamente a las funciones de su rol y a las fases activas del expediente que le corresponden. Los permisos son dinámicos: se calculan en base a `rolpersona` + `fase_actual_id` del expediente.

---

## 4. EL PROCESO: LAS 11 FASES

El proceso está organizado en dos Etapas. `expediente.etapa` = `'I'` o `'II'`.

### ETAPA I — Proyecto de Tesis

| Fase | Nombre | Actor | FAI | Plazo |
|------|--------|-------|-----|-------|
| 1 | Iniciar Proyecto | Estudiante | — | — |
| 2 | Verificar Requisitos (FAI) | UI / Sistema | ✅ RF02.1, RF02.2, RF02.5 | 2 días hábiles |
| 3 | Verificar Duplicidad ALICIA | UI | ✅ (ALICIA API) | 5 días |
| 4 | Presentación de Proyecto PDF | Estudiante | — | — |
| 5 | Asignar Jurado | CC / UI | — | 5 días |
| 6 | Revisión del Proyecto | Jurado ×3 | — | **15 días** |
| 7 | Aprobación del Proyecto | UI / Decanato | — | 3 días |

### ETAPA II — Informe Final y Sustentación

| Fase | Nombre | Actor | FAI | Plazo |
|------|--------|-------|-----|-------|
| 8 | Presentación Informe Final | Estudiante / Asesor | ✅ RF02.3, RF02.4, RF02.6 | — |
| 9 | Revisión del Informe Final | Jurado ×3 | — | **15 días** |
| 10 | Programar Sustentación | UI / Decanato | — | **7 días hábiles** |
| 11 | Sustentación | Jurado / Estudiante | — | — |

### Reglas de negocio críticas (no negociables)

```
RGT-01: Un jurado NO puede agregar observaciones nuevas en rondas > 1.
         → bit_expedienteobservacion.bloqueado = true si ronda > 1.

RGT-02: Si vence el plazo de 15 días (Fase 6 o 9) con exactamente 2/3 jurados aprobando
         → habilitar Art. 123-d: el expediente avanza sin el tercer jurado.
         → controlplazo.art123d_habilitado = true.

RGT-03: Toda acción crítica (firma asesor, aprobación jurado, emisión resolución)
         requiere 2FA verificado. Se guarda el código OTP en det_expedientejurado.codigo_2fa_usado.

RGT-04: Los FAI son bloqueantes: si un filtro retorna RECHAZADO, el expediente
         no avanza a la siguiente fase. El sistema registra el intento y notifica a la UI.

RGT-05: Umbrales de similitud: Proyecto ≤ 30% · Informe Final ≤ 20%.
         Configurables en tabla `parametro` con override por `rel_parametrosucursal`.
```

---

## 5. REQUERIMIENTOS FUNCIONALES

### RF-01: Portal Único de Radicación (PUR)

| ID | Descripción |
|----|-------------|
| RF01.1 | El estudiante carga su expediente. El sistema asigna número y fecha de radicación automáticamente (formato: `FACHSE-{AÑO}-{SECUENCIA}`) |
| RF01.2 | Antes de aceptar, ejecuta FAI. Bloquea y notifica si falta un requisito |
| RF01.3 | El expediente validado se asigna automáticamente a UI o CC según la fase |
| RF01.4 | El sistema registra cada cambio de estado con fecha, hora, actor e IP |
| RF01.5 | El sistema envía notificaciones automáticas (email/SMS) en cada cambio de estado |

### RF-02: Filtros Administrativos Automatizados (FAI)

| Código | Verificación | Fase | API | Resultado |
|--------|-------------|------|-----|-----------|
| RF02.1 | Créditos académicos (≥ 160) | Fase 2 | DSA – Sistema Académico UNPRG | Aprobado / Rechazado |
| RF02.2 | Voucher de pago del proyecto | Fase 2 | DGA – Tesorería UNPRG | Aprobado / Rechazado |
| RF02.3 | Grado de Bachiller | Fase 8 | ORC / SUNEDU | Aprobado / Rechazado |
| RF02.4 | Voucher de pago de sustentación | Fase 8 | DGA – Tesorería UNPRG | Aprobado / Rechazado |
| RF02.5 | Identidad del usuario (DNI) | Todas | RENIEC | Verificado / Alerta |
| RF02.6 | Similitud Turnitin | Fases 2 y 8 | API Turnitin | Aprobado / Rechazado |

**Comportamiento ante fallo de API externa:** el sistema registra el intento en `faiintento`, loggea en `failog`, y notifica a la UI para gestión manual excepcional (modo `fallback_manual`).

### RF-03: Autenticación de Doble Factor (2FA)

- OTP temporal vía SMS o app autenticadora (TOTP)
- Obligatorio para: firma del asesor, aprobación/observación del jurado, aprobación de la UI, emisión de resoluciones
- Sincronización con RRHH: bloqueo automático de docentes cesados vía `docente_rrhh_sync`

### RF-04: Control de Calidad Académica

- Bloqueo de observaciones adicionales (RGT-01)
- Control de plazos con alertas automáticas
- Calendario digital de sustentaciones
- Clasificación automática cuantitativo/cualitativo mediante NLP (`nlpclasificacion`)
- Verificación de depósito en repositorio institucional antes de cierre del expediente

### RF-05: Gestión de Usuarios y Accesos

- Registro de estudiantes, asesores (internos y externos), docentes-jurados
- Soporte de coautoría (máx. 2 autores en pregrado)
- Roles y permisos dinámicos por fase activa

---

## 6. STACK TECNOLÓGICO Y PARADIGMAS

### Stack definitivo

```
Backend         : Laravel 11 · PHP 8.2+
Base de datos   : PostgreSQL (una BD por universidad = SaaS aislado)
Frontend        : Blade + SCSS · Materialize CSS 1.0.0 · jQuery 3.6.0
Tablas          : jQuery DataTables
Alertas/Modales : SweetAlert2
Íconos          : Font Awesome 6.4.0 · Material Icons
Gráficos        : Chart.js 4.3.0
Colas           : Laravel Jobs (Turnitin + ALICIA son asíncronos)
Almacenamiento  : GCS para imágenes · storage/public para PDFs
Autenticación   : Laravel Auth + Sanctum para API tokens
Push/Realtime   : Pusher 7.2 (para notificaciones al jurado en tiempo real)
Control versión : GitHub Flow · commits semánticos en inglés
```

### Paradigmas clave — el equipo NO puede desviarse de estos

**Paradigma 1 — SaaS por instancia**
```
Cada universidad = un servidor + una BD PostgreSQL + un .env propio.
Añadir una universidad ≠ añadir un tenant en la misma BD.
Añadir una universidad = clonar repo + nuevo .env + migrate + seed.
```

**Paradigma 2 — Jerarquía institucional**
```
universidad (1) → sucursal/facultad (N) → carrera (N)
sucursal_id viaja en toda tabla transaccional como filtro de tenant interno.
```

**Paradigma 3 — Backend: capas estrictas**
```
Request → Middleware → FormRequest → Controller (skinny) → Service → DB
- Controller: máximo 5 líneas de lógica. Solo orquesta.
- Service: toda la lógica de negocio. Usa DataBaseTrait y LibraryTrait.
- FormRequest: valida, sanitiza, inyecta sucursal_id.
- Nunca saltarse una capa.
```

**Paradigma 4 — Frontend: SPA server-driven**
```
app.blade.php es el único HTML full-page.
Todo contenido se carga por AJAX con setRun(url, params, div).
Respuesta del Controller: JSON con {title, page_title, content, content_header, content_footer}.
Nunca usar window.location ni <a href> para navegar entre módulos.
```

**Paradigma 5 — Cero hardcoding**
```
Ningún ID, rol, umbral ni URL en el código fuente.
→ Umbrales académicos: tabla parametro + rel_parametrosucursal.
→ Roles: config/roles.php + session('perfil_id').
→ URLs de APIs externas: .env + config/fai.php.
```

---

## 7. ARQUITECTURA DE BASE DE DATOS

### Grupos de tablas

| Grupo | Tablas | Descripción |
|-------|--------|-------------|
| 0 — Infraestructura Institucional | `universidad`, `sucursal`, `carrera`, `rel_lineainvestigacionsucursal`, `rel_fasesucursal` | La jerarquía SaaS |
| 1 — Heredadas Delicia | `personamaestro`, `persona`, `rol`, `rolpersona`, `parametro`, `rel_parametrosucursal`, `categoriaparametro`, `bitacora`, `ubigeo` | Sin cambios estructurales |
| 2 — Maestras / Catálogos | `tipodocumento`, `tipoinvestigacion`, `areaconocimiento`, `lineainvestigacion`, `tiporequerimiento`, `tipoobservacion`, `motivorechazo`, `fase`, `estadoexpediente`, `tiporesolucion`, `apifuente` | Seeds en Sprint 0 |
| 3 — Movimiento / Expediente | `expediente` *(cabecera)*, `det_expedientedocumento`, `det_expedientefase`, `det_expedienteobservacion`, `det_expedientejurado`, `det_expedientecoautor`, `resolucion`, `sustentacion`, `actasustentacion` | El corazón del proceso |
| 4 — FAI | `faiconfig`, `fairesultado`, `faiintento`, `failog` | Nuevo grupo sin equiv. Delicia |
| 5 — Seguridad / 2FA | `usuario`, `dosfactorconfig`, `dosfactorcodigo`, `dosfactorintento`, `sesion`, `personal_access_tokens` | Auth + No Repudio |
| 6 — Control y Seguimiento | `controlplazo`, `alertaplazo`, `repositoriodeposito`, `nlpclasificacion`, `docente_rrhh_sync`, `sustentacion`, `actasustentacion` | Plazos + calidad |
| 7 — Notificaciones | `plantillanotificacion`, `notificacion`, `det_notificaciondestinatario` | Cola de mensajes |
| 8 — Bitácoras | `bit_expediente`, `bit_firma`, `bit_accesousuario`, `bit_faiauditoria` | Inmutables, sin soft delete |

### Tabla `expediente` — la cabecera central

```sql
expediente
  id, numero_radicacion (UNIQUE), sucursal_id, carrera_id,
  estudiante_id → persona, asesor_id → persona,
  titulo, tipoinvestigacion_id, lineainvestigacion_id, areaconocimiento_id,
  tiporequerimiento_id, etapa (I/II), fase_actual_id → fase,
  estadoexpediente_id, resumen, fecha_radicacion, fecha_cierre,
  timestamps, softDeletes
```

El patrón `expediente` ↔ `det_expediente*` replica exactamente el patrón `movventa` ↔ `det_movventa` del paradigma Delicia.

### Relaciones críticas para el agente de código

```
expediente.estudiante_id       → persona.id
expediente.sucursal_id         → sucursal.id  (filtro tenant)
expediente.carrera_id          → carrera.id
det_expedientejurado.jurado_id → persona.id
det_expedientefase             → registro inmutable de trazabilidad
fairesultado.fai_codigo        → 'RF02.1' ... 'RF02.6'
bit_expediente                 → NUNCA tiene updated_at ni deleted_at
```

### Parámetros configurables (sin tocar código)

```
CREDITOS_MINIMOS         = 160
TURNITIN_PROYECTO_MAX    = 30
TURNITIN_INFORME_MAX     = 20
PLAZO_REVISION_JURADO    = 15
PLAZO_SUSTENTACION       = 7
```
→ En tabla `parametro`, override por facultad en `rel_parametrosucursal`.

---

## 8. ARQUITECTURA BACKEND LARAVEL

> Este bloque es el contrato técnico del Agente Backend. No se puede desviar de él.

### Mapa de capas (flujo completo)

```
HTTP Request
    ↓
Middleware          → auth, rol, sucursal_id en sesión
    ↓
FormRequest         → valida, sanitiza, inyecta sucursal_id en prepareForValidation()
    ↓
Controller          → orquesta: DB::transaction → Service → response()->json()
    ↓
Service             → toda la lógica de negocio
    ├── DataBaseTrait    → insertRecord / updateRecord / deleteRecord / updateOrInsertById
    └── LibraryTrait     → saveImage / savePdf / saveTempFile / deleteImage
    ↓
Model               → $fillable, $hidden, relaciones con select() explícito
    ↓
PostgreSQL
```

### Estructura de archivos por módulo

```
app/
  Http/
    Controllers/
      Web/
        ExpedienteController.php
        FaiController.php
        JuradoController.php
        PurController.php
        DashboardController.php
      Api/
        FaiApiController.php       ← respuestas de APIs externas (webhooks)
    Middleware/
      TwoFactorMiddleware.php
      RoleMiddleware.php
    Requests/
      Expediente/
        StoreExpedienteRequest.php
        UpdateExpedienteRequest.php
      Observacion/
        StoreObservacionRequest.php
      Sustentacion/
        StoreSustentacionRequest.php
  Models/
    Expediente.php
    Persona.php
    FaiResultado.php
    ExpedienteFase.php
    Observacion.php
  Services/
    ExpedienteService.php
    FaiService.php              ← orquesta todos los filtros
    FaiDsaService.php           ← créditos académicos
    FaiDgaService.php           ← vouchers de pago
    FaiReniecService.php        ← identidad DNI
    FaiSuneduService.php        ← grado bachiller
    FaiTurnitinService.php      ← similitud
    TwoFactorService.php
    NotificacionService.php
  Traits/
    DataBaseTrait.php
    LibraryTrait.php
    HasAuditLog.php             ← actor + timestamp + IP inmutable
  Jobs/
    EjecutarFaiJob.php          ← Turnitin / ALICIA asíncronos
    NotificarActoresJob.php
  Libraries/
    ApiClient.php               ← HTTP client con retry + fallback
```

### Contrato JSON del Controller (obligatorio)

```php
// Respuesta para setRun() — vista
return response()->json([
    'title'          => 'Expedientes | SELGESTIUM',
    'page_title'     => '<i class="fa-solid fa-folder-open"></i> Expedientes',
    'content'        => view('expediente.list', compact('records'))->render(),
    'content_header' => view('expediente.list_header')->render(),
    'content_footer' => view('expediente.list_footer')->render(),
]);

// Respuesta para operaciones CRUD
return response()->json(['status' => 200]);                               // éxito
return response()->json(['status'=>400,'icon'=>'warning','message'=>'…']); // negocio
// errores de validación: FormRequest lanza HTTP 422 automáticamente
```

### Ejemplo de Service con DataBaseTrait

```php
class ExpedienteService
{
    use DataBaseTrait, LibraryTrait;

    public function store(array $data, ?UploadedFile $pdf = null): \stdClass
    {
        $docUrl = $this->savePdf('expedientes', $pdf);

        $id = $this->insertRecord('expedientes', [
            'numero_radicacion'   => $this->generarNumero($data['sucursal_id']),
            'sucursal_id'         => $data['sucursal_id'],
            'carrera_id'          => $data['carrera_id'],
            'estudiante_id'       => $data['estudiante_id'],
            'titulo'              => $data['titulo'],
            'fase_actual_id'      => 1,
            'estadoexpediente_id' => config('estados.pendiente'),
            'fecha_radicacion'    => now(),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        if ($docUrl) {
            $this->insertRecord('det_expedientedocumento', [
                'expediente_id'       => $id,
                'tipo_documento'      => 'formato_01',
                'ruta_almacenamiento' => $docUrl,
                'subido_por_id'       => $data['persona_id'],
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }

        return DB::table('expedientes')->find($id);
    }
}
```

### Tenant isolation

```php
// Todo query operativo filtra por sucursal_id — sin excepción
DB::table('expedientes')
    ->where('sucursal_id', session('sucursal_id'))
    ->whereNull('deleted_at')
    ->get();
```

---

## 9. ARQUITECTURA FRONTEND BLADE + SCSS

> Este bloque es el contrato técnico del Agente Frontend. La paleta SELGESTIUM sobrescribe los tokens de Materialize.

### El patrón SPA server-driven

```
app.blade.php (único full-page)
      ↓
setRun(url, params, div, focus_id, idloader)
      ↓
$.ajax → Controller → JSON {title, page_title, content, content_header, content_footer}
      ↓
scripts.js inyecta content en #frame o #modal-form
scripts.js ejecuta content_header / content_footer inline
```

**Nunca:** `window.location`, `<a href>` entre módulos, navegación con reload.

### Estructura de vistas por módulo

```
resources/views/
  layouts/
    app.blade.php            ← sidebar, navbar, #frame, #maintenance-modal
    dashboard.blade.php
    grid/
      index.blade.php
      header.blade.php       ← <thead>
      footer.blade.php       ← paginador
      operations.blade.php   ← botones por fila
  expediente/
    list.blade.php
    maintenance.blade.php
  fai/
    panel.blade.php          ← tarjetas FAI semafóricas
  jurado/
    revision.blade.php
  sustentacion/
    calendario.blade.php
```

### Convenciones Blade + JS obligatorias

```blade
{{-- Form en modal: id SIEMPRE frmMant{modulo} en minúsculas --}}
<form id="frmMantexpediente" autocomplete="off">
    @csrf
    <input type="hidden" id="expediente_id" name="id" value="{{ $expediente->id ?? '' }}">
    ...
</form>

{{-- Input con span de error (para receiveErrors()) --}}
<div class="input-field col s12">
    <input id="titulo" name="titulo" type="text" maxlength="300"
           value="{{ $expediente->titulo ?? '' }}">
    <label for="titulo" class="{{ isset($expediente) ? 'active' : '' }}">Título del proyecto</label>
    <span class="helper-text" id="titulo_spn" data-error=""></span>
</div>

{{-- Submit en modal --}}
<script>
function guardarExpediente() {
    let formData = new FormData(document.getElementById('frmMantexpediente'));
    aceptarModal('expediente', '{{ route("expediente.store") }}', formData);
}
</script>
```

### Componente estrella — Tarjeta FAI

El panel FAI es el componente más crítico visualmente. Cada tarjeta muestra el estado de un filtro RF02.X.

```blade
{{-- resources/views/fai/card.blade.php --}}
<div class="fai-card fai-card--{{ $resultado->estado }}">
    <span class="fai-card__badge fai-card__badge--{{ $resultado->apifuente->codigo }}">
        {{ $resultado->apifuente->codigo }}
    </span>
    <div class="fai-card__icon">
        <i class="{{ $resultado->estado === 'aprobado' ? 'fa-circle-check' : 'fa-circle-xmark' }} fa-solid"></i>
    </div>
    <div class="fai-card__body">
        <p class="fai-card__label">{{ $resultado->descripcion }}</p>
        <p class="fai-card__value">{{ $resultado->valor_obtenido }}</p>
    </div>
</div>
```

```scss
// _fai-card.scss
.fai-card {
    border-radius: 8px;
    padding: 16px;
    border-left: 4px solid transparent;

    &--aprobado   { border-color: var(--color-success); background: var(--color-success-bg); }
    &--rechazado  { border-color: var(--color-danger);  background: var(--color-danger-bg);  }
    &--pendiente  { border-color: var(--color-warning); background: var(--color-warning-bg); }
    &--no_aplica  { border-color: var(--color-neutral); background: var(--color-neutral-bg); }
    &--error      { border-color: var(--color-danger);  background: var(--color-danger-bg);  }

    &__badge {
        position: absolute;
        top: 8px; right: 8px;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 0.75rem;
        font-weight: 700;

        &--RENIEC   { background: #EEF2FF; color: #3B82F6; }
        &--DSA      { background: #F0FDF4; color: #16A34A; }
        &--DGA      { background: #FFFBEB; color: #D97706; }
        &--TURNITIN { background: #FDF4FF; color: #9333EA; }
        &--ALICIA   { background: #F0F9FF; color: #0284C7; }
        &--SUNEDU   { background: #FFF7ED; color: #EA580C; }
    }
}
```

### Estructura SCSS

```
resources/scss/
  _variables.scss      ← tokens CSS de SELGESTIUM
  _base.scss           ← reset y tipografía
  _layout.scss         ← sidebar, navbar, #frame
  _components/
    _fai-card.scss     ← tarjetas de filtros FAI
    _expediente.scss   ← badge de fase, estado del expediente
    _timeline.scss     ← historial de fases (det_expedientefase)
    _calendar.scss     ← calendario de sustentaciones
    _2fa-modal.scss    ← modal de verificación OTP
  app.scss             ← punto de entrada, importa todo
```

---

## 10. PALETA VISUAL E IDENTIDAD UI

### Tokens CSS de SELGESTIUM

```scss
:root {
    // ── Institucionales ────────────────────────────────
    --color-primary:       #003087;   // Sidebar, navbar, botones primarios
    --color-primary-dark:  #001F5C;   // Hover primario
    --color-primary-light: #1A4BA8;   // Cards destacadas
    --color-accent:        #00BFFF;   // Highlights, íconos activos
    --color-accent-light:  #E0F7FF;   // Fondos sección destacada

    // ── Neutros ────────────────────────────────────────
    --color-bg:            #F4F6F9;
    --color-surface:       #FFFFFF;
    --color-border:        #DDE3EC;
    --color-text-primary:  #1A1D23;
    --color-text-secondary:#6B7280;
    --color-text-muted:    #9CA3AF;

    // ── Estados FAI + Expediente ───────────────────────
    --color-success:       #00C48C;
    --color-success-bg:    #E6FAF5;
    --color-warning:       #FFAA00;
    --color-warning-bg:    #FFF8E1;
    --color-danger:        #FF4C4C;
    --color-danger-bg:     #FFF0F0;
    --color-neutral:       #8B95A5;
    --color-neutral-bg:    #F1F3F7;
    --color-info:          #3B82F6;

    // ── Tipografía ─────────────────────────────────────
    --font-sans: 'Inter', 'Source Sans Pro', Roboto, sans-serif;
    --font-mono: 'JetBrains Mono', 'Fira Code', monospace; // códigos de radicación
}
```

### Sidebar SELGESTIUM

```scss
.sidebar {
    background: var(--color-primary);      // #003087

    .nav-item--active {
        background: var(--color-warning);  // #FFAA00
        color: var(--color-primary);
        font-weight: 700;
    }

    .nav-item:hover {
        background: rgba(255,255,255,0.10);
    }

    .nav-link { color: #FFFFFF; }
    .nav-link--muted { color: rgba(255,255,255,0.6); }
}
```

### Sistema semafórico (expediente + FAI)

| Estado | Color borde | Color fondo | Uso |
|--------|-------------|-------------|-----|
| Aprobado / Verificado | `#00C48C` | `#E6FAF5` | FAI OK, jurado aprobó |
| En proceso / Pendiente | `#FFAA00` | `#FFF8E1` | FAI en cola, revisión activa |
| Rechazado / Error | `#FF4C4C` | `#FFF0F0` | FAI falló, observado |
| No aplica | `#8B95A5` | `#F1F3F7` | FAI no ejecutado aún |

---

## 11. INSTRUCCIONES POR TIPO DE AGENTE

---

### 🏗️ AGENTE ARQUITECTO

**Propósito:** validar coherencia arquitectónica antes de que el equipo implemente. No genera código. Genera decisiones.

**Contexto prioritario para leer:** secciones 2, 4, 5, 6, 7.

**Tu rol:**
- Resolver ambigüedades antes de que el equipo escriba código.
- Validar que una nueva feature no rompe el modelo de datos ni las capas.
- Detectar dependencias entre módulos y señalar el orden de implementación.
- Proponer el contrato de una API/Service antes de que el desarrollador lo codifique.
- Decidir qué va en `parametro` vs hardcodeado vs `.env`.

**Preguntas que debes responder antes de emitir un dictamen:**
1. ¿Esta feature toca la jerarquía `universidad → sucursal → carrera`?
2. ¿Requiere un nuevo campo en `expediente` o es un `det_`?
3. ¿La regla de negocio es configurable por facultad? (→ `rel_parametrosucursal`)
4. ¿La acción requiere trazabilidad legal? (→ tabla `bit_`)
5. ¿El proceso es asíncrono? (→ Job en cola, no bloquear UI)

**Formato de respuesta esperado:**
```
DECISIÓN: [Título de la decisión]
CONTEXTO: [Por qué se necesita]
IMPACTO DB: [tablas afectadas]
IMPACTO BACKEND: [services/controllers afectados]
RESTRICCIÓN: [qué NO se puede hacer]
ACCIÓN: [qué debe hacer el desarrollador]
```

---

### 📋 AGENTE DE SPRINTS

**Propósito:** convertir un bloque de texto de requerimientos en un sprint estructurado con features, tareas y asignaciones.

**Contexto prioritario para leer:** secciones 3, 4, 5, 8, 9.

**Flujo de trabajo:**
1. Lee el texto de entrada.
2. Identifica qué módulos del sistema impacta.
3. Descompone en features (F-XX).
4. Para cada feature genera: descripción, tablas de BD involucradas, capa backend, capa frontend, criterio de aceptación, días estimados.
5. Agrupa en sprints de 5 días hábiles.
6. Asigna features respetando las dependencias (no se puede hacer frontend antes que la migración).

**Formato de salida esperado:**

```markdown
## SPRINT N — [Nombre del sprint]
**Objetivo:** [Entregable visible al final del sprint]
**Duración:** [N días]

### F-XX: [Nombre de la feature]
- **Módulo:** expediente / fai / jurado / sustentacion / 2fa / notificacion
- **Tablas BD:** expediente, det_expedientejurado, ...
- **Backend:** [Service] → [Controller] → [FormRequest]
- **Frontend:** [Vista] → [Componente SCSS]
- **Criterio de aceptación:** [Lo mínimo para marcar como DONE]
- **Dependencias:** F-YY debe estar cerrada primero
- **Estimado:** N días · Desarrollador: [Nombre]
```

**Reglas de prioridad:**
- Sprint 0 siempre: migraciones, seeders de catálogos, layout base `app.blade.php`.
- Las features FAI van después de que el módulo `expediente` esté funcional.
- El módulo 2FA es prerequisito de cualquier feature de firma o aprobación.
- Nunca asignar frontend antes de que exista el endpoint del Controller.

---

### 🎨 AGENTE FRONTEND — LARAVEL BLADE + SCSS

**Propósito:** generar vistas Blade, SCSS y scripts JS siguiendo el paradigma SPA server-driven del proyecto.

**Contexto prioritario para leer:** secciones 6, 9, 10.

**Tu stack (no proponer librerías fuera de esta lista):**

| Herramienta | Versión | Función |
|-------------|---------|---------|
| Materialize CSS/JS | 1.0.0 | UI framework base |
| jQuery | 3.6.0 | AJAX y DOM |
| SweetAlert2 | latest | Modales y alertas |
| jQuery DataTables | latest | Tablas con búsqueda y paginación |
| Chart.js | 4.3.0 | Gráficos (dashboard) |
| Font Awesome | 6.4.0 | Íconos |
| SCSS | — | Estilos con tokens CSS de SELGESTIUM |

**Principios de generación:**

1. **Todo módulo = 2 archivos Blade:** `list.blade.php` + `maintenance.blade.php`.
2. **El form siempre tiene** `id="frmMant{modulo}"` en minúsculas sin guión.
3. **Todo input tiene** su `<span class="helper-text" id="{campo}_spn" data-error="">`.
4. **El Controller responde siempre** JSON con `{title, page_title, content, content_header, content_footer}`.
5. **Nunca** poner lógica de negocio en Blade ni en scripts inline — solo llamadas a funciones definidas en archivos JS separados.
6. **Los tokens CSS** son los de la sección 10. No usar colores hardcodeados en SCSS, siempre `var(--color-*)`.
7. **La paleta** sobrescribe los defaults de Materialize — la variable `--color-primary` reemplaza el azul de Materialize.

**Componentes prioritarios a conocer:**

- `fai-card` — tarjeta semafórica de filtro (ver sección 9)
- `timeline-fase` — historial de fases del expediente (usa `det_expedientefase`)
- `badge-estado` — pill con color semafórico del estado del expediente
- `2fa-modal` — modal de verificación OTP que bloquea la acción hasta confirmar
- `calendar-sustentacion` — vista de programación de sustentaciones

**Formato de entrega:**
- Un bloque por archivo Blade.
- Un bloque por archivo SCSS parcial.
- Si hay JS, en un bloque separado con el nombre del archivo.
- Siempre comentar qué variable Blade espera la vista (`@param` comentado arriba del `@extends`).

---

## GLOSARIO RÁPIDO

| Término | Significado |
|---------|-------------|
| PUR | Portal Único de Radicación |
| FAI | Filtros Administrativos Automatizados (RF02.1–RF02.6) |
| RGT | Reglamento de Grados y Títulos |
| UI | Unidad de Investigación (actor humano, no interfaz) |
| CC | Comité Científico |
| DSA | Sistema Académico UNPRG (API de créditos) |
| DGA | Tesorería UNPRG (API de pagos) |
| sucursal | Facultad dentro de la universidad (nodo de tenant) |
| Art. 123-d | Artículo reglamentario: permite avanzar con 2/3 jurados si el 3ro no evaluó en el plazo |
| bit_ | Prefijo de tabla de bitácora — siempre inmutable |
| det_ | Prefijo de tabla de detalle (1:N de una cabecera) |
| rel_ | Prefijo de tabla pivote N:M |
| Delicia | Nombre del proyecto previo del equipo, del cual se hereda el paradigma de BD |

---

*SELGESTIUM · Contexto Maestro v1.0.0 · 2026-I*  
*Este archivo es la fuente de verdad para todos los agentes del proyecto.*
