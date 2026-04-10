# SELGESTIUM — Diseño Completo de Base de Datos
**Paradigma:** db_delicia · Laravel 11 · PostgreSQL · Multi-Universidad / Multi-Facultad  
**Versión:** v1.0.0 · 2026-I  
**Arquitecto:** Ing. Martin Ampuero — Equipo SELGESTIUM  

---

## Índice

1. [Filosofía del Modelo](#filosofía-del-modelo)
2. [Convenciones de Nomenclatura](#convenciones-de-nomenclatura)
3. [Mapa de Tablas por Grupo](#mapa-de-tablas-por-grupo)
4. [GRUPO 0 — Infraestructura Institucional](#grupo-0--infraestructura-institucional)
5. [GRUPO 1 — Tablas Heredadas del Paradigma Delicia](#grupo-1--tablas-heredadas-del-paradigma-delicia)
6. [GRUPO 2 — Tablas Maestras / Catálogos](#grupo-2--tablas-maestras--catálogos)
7. [GRUPO 3 — Tablas de Movimiento / Expediente](#grupo-3--tablas-de-movimiento--expediente)
8. [GRUPO 4 — Tablas FAI (Filtros Administrativos Automatizados)](#grupo-4--tablas-fai)
9. [GRUPO 5 — Seguridad y 2FA](#grupo-5--seguridad-y-2fa)
10. [GRUPO 6 — Control y Seguimiento](#grupo-6--control-y-seguimiento)
11. [GRUPO 7 — Notificaciones](#grupo-7--notificaciones)
12. [GRUPO 8 — Bitácoras Especializadas](#grupo-8--bitácoras-especializadas)
13. [Diagrama de Relaciones Clave](#diagrama-de-relaciones-clave)
14. [Resumen de Tablas](#resumen-de-tablas)

---

## Filosofía del Modelo

```
UNIVERSIDAD (1) ──► FACULTAD (N) ──► CARRERA (N)
     │                   │
     └── sucursal_id  ───┘   ← columna vertebral en toda tabla transaccional
```

- Cada **universidad** que contrata SELGESTIUM tiene su propia instancia Laravel + BD PostgreSQL (SaaS aislado).
- Dentro de esa instancia, la jerarquía `universidad → facultad → carrera` se modela con la tabla `sucursal` extendida: una fila puede ser una Facultad o una Carrera.
- Todo expediente, usuario y parámetro lleva `sucursal_id` para filtrar por Facultad/Carrera sin JOIN innecesarios.
- Prefijos de nomenclatura siguen el paradigma Delicia: `det_`, `rel_`, `bit_`, `mov` (solo `expediente` actúa como cabecera de movimiento).

---

## Convenciones de Nomenclatura

| Prefijo | Tipo de Tabla | Ejemplo |
|---------|---------------|---------|
| *(sin prefijo)* | Maestras, entidades principales | `persona`, `expediente`, `carrera` |
| `det_` | Detalle de cabecera (1:N) | `det_expedientedocumento` |
| `rel_` | Pivote N:M | `rel_parametrosucursal` |
| `bit_` | Bitácora especializada | `bit_expediente` |
| `fai` | Filtros automatizados | `fairesultado`, `failog` |
| `dos` | 2FA / seguridad | `dosfactorconfig` |

**Reglas generales:**
- Todos los nombres en **español**, en minúsculas, sin guiones.
- Claves foráneas: `tabla_id` (ej. `persona_id`, `sucursal_id`).
- Toda tabla transaccional incluye: `created_at`, `updated_at`, `deleted_at` (soft delete).
- Toda tabla lleva `sucursal_id` si almacena datos operativos.

---

## Mapa de Tablas por Grupo

| # | Grupo | Tablas | Equiv. Delicia |
|---|-------|--------|----------------|
| 0 | Infraestructura Institucional | 5 | `empresa`, `tipocomercio` |
| 1 | Heredadas sin cambios | 10 | Directo de db_delicia |
| 2 | Maestras / Catálogos | 14 | `categoria`, `marca`… |
| 3 | Movimiento / Expediente | 9 | `movventa`, `det_movventa` |
| 4 | FAI (nuevo grupo) | 4 | *(sin equivalente)* |
| 5 | Seguridad / 2FA | 6 | `usuario` extendido |
| 6 | Control y Seguimiento | 7 | `kardex`, `cierre` |
| 7 | Notificaciones | 3 | `appmovil` extendido |
| 8 | Bitácoras Especializadas | 4 | `bit_venta`, `bitacora` |
| **TOTAL** | | **62** | |

---

## GRUPO 0 — Infraestructura Institucional

> Modela la jerarquía Universidad → Facultad → Carrera. Es la capa SaaS que identifica a la institución cliente y su estructura académica interna.

---

### Tabla: `universidad`
*Datos de la institución cliente. Una fila por instancia desplegada.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | Identificador |
| `nombre` | VARCHAR(200) | NOT NULL | Nombre completo (ej. "UNPRG") |
| `nombre_corto` | VARCHAR(50) | NOT NULL | Siglas (ej. "UNPRG") |
| `ruc` | VARCHAR(11) | UNIQUE | RUC institucional |
| `logo_url` | VARCHAR(500) | NULL | URL del logo en CDN |
| `color_primario` | VARCHAR(7) | DEFAULT '#003087' | Hex para UI |
| `color_acento` | VARCHAR(7) | DEFAULT '#00BFFF' | Hex para UI |
| `dominio_email` | VARCHAR(100) | NULL | Dominio institucional (ej. "unprg.edu.pe") |
| `direccion` | TEXT | NULL | Dirección física |
| `telefono` | VARCHAR(20) | NULL | Teléfono central |
| `activo` | BOOLEAN | DEFAULT true | Estado del cliente SaaS |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `sucursal`
*Representa una Facultad o Sede dentro de la universidad. Columna vertebral del modelo.*  
*Una fila = una Facultad (ej. "FACHSE"). Las Carreras tienen su propia tabla `carrera`.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | Identificador |
| `universidad_id` | BIGINT | FK → universidad | Universidad propietaria |
| `nombre` | VARCHAR(200) | NOT NULL | Nombre de la Facultad |
| `nombre_corto` | VARCHAR(30) | NOT NULL | Siglas (ej. "FACHSE") |
| `tipo` | VARCHAR(30) | DEFAULT 'facultad' | `facultad` \| `sede` \| `filial` |
| `decano_persona_id` | BIGINT | FK → persona, NULL | Decano actual |
| `direccion` | TEXT | NULL | |
| `email_institucional` | VARCHAR(150) | NULL | |
| `activo` | BOOLEAN | DEFAULT true | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

**Índices:** `(universidad_id, activo)`

---

### Tabla: `carrera`
*Programa Académico / Escuela Profesional dentro de una Facultad.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `sucursal_id` | BIGINT | FK → sucursal | Facultad a la que pertenece |
| `nombre` | VARCHAR(200) | NOT NULL | Nombre completo de la carrera |
| `nombre_corto` | VARCHAR(50) | NULL | Siglas (ej. "EBU") |
| `codigo_sunedu` | VARCHAR(20) | NULL | Código oficial SUNEDU |
| `nivel` | VARCHAR(30) | DEFAULT 'pregrado' | `pregrado` \| `posgrado` \| `segunda_especialidad` |
| `modalidad` | VARCHAR(30) | DEFAULT 'presencial' | `presencial` \| `semipresencial` \| `virtual` |
| `director_persona_id` | BIGINT | FK → persona, NULL | Director de Escuela |
| `creditos_minimos_tesis` | SMALLINT | DEFAULT 160 | Override del parámetro global |
| `activo` | BOOLEAN | DEFAULT true | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

**Índices:** `(sucursal_id, nivel, activo)`

---

### Tabla: `rel_lineainvestigacionsucursal`
*Líneas de investigación habilitadas por facultad.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `sucursal_id` | BIGINT | FK → sucursal | |
| `lineainvestigacion_id` | BIGINT | FK → lineainvestigacion | |
| `activo` | BOOLEAN | DEFAULT true | |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `rel_fasesucursal`
*Override de configuración de fases por facultad (plazos, actor responsable).*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `sucursal_id` | BIGINT | FK → sucursal | |
| `fase_id` | BIGINT | FK → fase | |
| `plazo_dias` | SMALLINT | NULL | Override del plazo reglamentario |
| `actor_rol_id` | BIGINT | FK → rol, NULL | Rol responsable en esta facultad |
| `activo` | BOOLEAN | DEFAULT true | |
| `created_at` | TIMESTAMP | NOT NULL | |

---

## GRUPO 1 — Tablas Heredadas del Paradigma Delicia

> Se reutilizan sin cambios estructurales. Solo se extienden con `sucursal_id` donde sea necesario.

---

### Tabla: `personamaestro`
*Raíz de identidad: datos oficiales del DNI/RENIEC. Sin modificar.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `numero_documento` | VARCHAR(15) | UNIQUE, NOT NULL | DNI / Pasaporte |
| `tipodocumento_id` | BIGINT | FK → tipodocumento | |
| `nombres` | VARCHAR(100) | NOT NULL | |
| `apellido_paterno` | VARCHAR(80) | NOT NULL | |
| `apellido_materno` | VARCHAR(80) | NULL | |
| `sexo` | CHAR(1) | CHECK IN ('M','F') | |
| `fecha_nacimiento` | DATE | NULL | |
| `verificado_reniec` | BOOLEAN | DEFAULT false | |
| `verificado_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `persona`
*Todos los actores del sistema: estudiantes, asesores, jurados, administrativos.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `personamaestro_id` | BIGINT | FK → personamaestro, NULL | Vinculado tras verificación RENIEC |
| `sucursal_id` | BIGINT | FK → sucursal | Facultad de adscripción |
| `carrera_id` | BIGINT | FK → carrera, NULL | Carrera (para estudiantes) |
| `tipodocumento_id` | BIGINT | FK → tipodocumento | |
| `numero_documento` | VARCHAR(15) | NOT NULL | |
| `nombres` | VARCHAR(100) | NOT NULL | |
| `apellido_paterno` | VARCHAR(80) | NOT NULL | |
| `apellido_materno` | VARCHAR(80) | NULL | |
| `codigo_universitario` | VARCHAR(20) | NULL | Solo para estudiantes |
| `email` | VARCHAR(150) | NOT NULL | |
| `email_institucional` | VARCHAR(150) | NULL | |
| `telefono` | VARCHAR(20) | NULL | |
| `grado_academico` | VARCHAR(50) | NULL | Bachiller, Magíster, Doctor |
| `tipo_persona` | VARCHAR(30) | DEFAULT 'estudiante' | `estudiante` \| `docente` \| `administrativo` \| `externo` |
| `activo` | BOOLEAN | DEFAULT true | |
| `deleted_at` | TIMESTAMP | NULL | Soft delete |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

**Índices:** `(sucursal_id, tipo_persona, activo)`, `(numero_documento)`

---

### Tabla: `rol`
*Catálogo de roles del sistema.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `descripcion` | VARCHAR(80) | NOT NULL | Nombre del rol |
| `codigo` | VARCHAR(30) | UNIQUE | `alumno`, `asesor`, `jurado`, `ui`, `cc`, `decanato`, `admin` |
| `activo` | BOOLEAN | DEFAULT true | |

**Datos fijos (seeds):**

| id | descripcion | codigo |
|----|-------------|--------|
| 1 | Estudiante / Egresado | `alumno` |
| 2 | Asesor | `asesor` |
| 3 | Jurado | `jurado` |
| 4 | Unidad de Investigación | `ui` |
| 5 | Comité Científico | `cc` |
| 6 | Decanato | `decanato` |
| 7 | Administrativo | `admin` |
| 8 | Asesor Externo | `asesor_externo` |

---

### Tabla: `rolpersona`
*Pivote persona ↔ rol ↔ sucursal. Una persona puede tener múltiples roles en diferentes facultades.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `persona_id` | BIGINT | FK → persona | |
| `rol_id` | BIGINT | FK → rol | |
| `sucursal_id` | BIGINT | FK → sucursal | |
| `activo` | BOOLEAN | DEFAULT true | |
| `created_at` | TIMESTAMP | NOT NULL | |

**Restricción única:** `(persona_id, rol_id, sucursal_id)`

---

### Tabla: `parametro`
*Reglas de negocio configurables globalmente (créditos mínimos, umbrales Turnitin, plazos).*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `categoriaparametro_id` | BIGINT | FK → categoriaparametro | |
| `codigo` | VARCHAR(50) | UNIQUE | ej. `CREDITOS_MINIMOS` |
| `descripcion` | VARCHAR(200) | NOT NULL | |
| `valor` | VARCHAR(200) | NOT NULL | Valor por defecto |
| `tipo_dato` | VARCHAR(20) | DEFAULT 'texto' | `entero` \| `decimal` \| `texto` \| `booleano` |
| `activo` | BOOLEAN | DEFAULT true | |

**Datos fijos (seeds):**

| codigo | descripcion | valor |
|--------|-------------|-------|
| `CREDITOS_MINIMOS` | Créditos mínimos para tesis | `160` |
| `TURNITIN_PROYECTO_MAX` | Umbral similitud proyecto % | `30` |
| `TURNITIN_INFORME_MAX` | Umbral similitud informe % | `20` |
| `PLAZO_REVISION_JURADO` | Días para revisión del jurado | `15` |
| `PLAZO_SUSTENTACION` | Días para programar sustentación | `7` |

---

### Tabla: `rel_parametrosucursal`
*Override de parámetros por Facultad/Carrera. Si existe registro aquí, tiene precedencia sobre `parametro`.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `parametro_id` | BIGINT | FK → parametro | |
| `sucursal_id` | BIGINT | FK → sucursal | |
| `valor` | VARCHAR(200) | NOT NULL | Valor específico para esta facultad |
| `activo` | BOOLEAN | DEFAULT true | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

**Restricción única:** `(parametro_id, sucursal_id)`

---

### Tabla: `categoriaparametro`
*Agrupador de parámetros para gestión en el panel de configuración.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `descripcion` | VARCHAR(100) | NOT NULL | ej. "Requisitos FAI", "Plazos", "Notificaciones" |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `bitacora`
*Log general de acciones del sistema (complementa las bitácoras especializadas).*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `persona_id` | BIGINT | FK → persona, NULL | Actor (NULL si es sistema) |
| `sucursal_id` | BIGINT | FK → sucursal, NULL | |
| `accion` | VARCHAR(100) | NOT NULL | ej. `login`, `crear_expediente` |
| `tabla_afectada` | VARCHAR(80) | NULL | |
| `registro_id` | BIGINT | NULL | ID del registro afectado |
| `detalle` | JSONB | NULL | Datos adicionales |
| `ip` | VARCHAR(45) | NULL | IPv4 o IPv6 |
| `user_agent` | TEXT | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |

**Nota:** Inmutable. Sin `updated_at` ni `deleted_at`.

---

### Tabla: `ubigeo`
*Departamento / Provincia / Distrito del Perú. Sin cambios.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | VARCHAR(6) | PK | Código INEI |
| `nombre` | VARCHAR(100) | NOT NULL | |
| `tipo` | VARCHAR(20) | NOT NULL | `departamento` \| `provincia` \| `distrito` |
| `padre_id` | VARCHAR(6) | FK → ubigeo, NULL | |

---

## GRUPO 2 — Tablas Maestras / Catálogos

> Data de referencia inmutable o semi-inmutable. Se poblan con seeders desde el sprint 0.

---

### Tabla: `tipodocumento`

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `descripcion` | VARCHAR(60) | NOT NULL | DNI, Pasaporte, Carné Extranjería |
| `codigo` | VARCHAR(10) | UNIQUE | |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `tipoinvestigacion`

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `descripcion` | VARCHAR(80) | NOT NULL | Cuantitativo, Cualitativo, Mixto |
| `codigo` | VARCHAR(20) | UNIQUE | `cuantitativo` \| `cualitativo` \| `mixto` |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `areaconocimiento`

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `descripcion` | VARCHAR(150) | NOT NULL | Área académica del proyecto |
| `codigo_ocde` | VARCHAR(20) | NULL | Clasificación OCDE |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `lineainvestigacion`

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `areaconocimiento_id` | BIGINT | FK → areaconocimiento | |
| `descripcion` | VARCHAR(200) | NOT NULL | |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `tiporequerimiento`
*Proyecto de Tesis vs Informe Final.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `descripcion` | VARCHAR(80) | NOT NULL | ej. "Proyecto de Tesis", "Informe Final" |
| `etapa` | CHAR(1) | CHECK IN ('I','II') | Etapa del proceso |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `tipoobservacion`
*Catálogo de tipos de observación que el jurado puede registrar.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `descripcion` | VARCHAR(150) | NOT NULL | |
| `sucursal_id` | BIGINT | FK → sucursal, NULL | NULL = global |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `motivorechazo`
*Catálogo de motivos por los cuales un FAI rechaza un expediente.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `fai_codigo` | VARCHAR(10) | NOT NULL | RF02.1 … RF02.6 |
| `descripcion` | VARCHAR(200) | NOT NULL | |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `fase`
*Las 11 fases del proceso de gestión de tesis. Data maestra fija.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | SMALLINT | PK | 1 al 11 |
| `numero` | SMALLINT | UNIQUE, NOT NULL | Orden de la fase |
| `nombre` | VARCHAR(100) | NOT NULL | ej. "Iniciar Proyecto" |
| `descripcion` | TEXT | NULL | |
| `etapa` | CHAR(1) | CHECK IN ('I','II') | I = Proyecto, II = Informe/Sustentación |
| `actor_principal` | VARCHAR(30) | NOT NULL | `alumno` \| `ui` \| `jurado` \| `cc` \| `decanato` |
| `plazo_dias_default` | SMALLINT | NULL | Plazo reglamentario base |
| `requiere_fai` | BOOLEAN | DEFAULT false | Ejecuta FAI al entrar a esta fase |
| `activo` | BOOLEAN | DEFAULT true | |

**Datos fijos (seeds):**

| id | nombre | etapa | actor_principal | plazo_dias | requiere_fai |
|----|--------|-------|-----------------|------------|--------------|
| 1 | Iniciar Proyecto | I | alumno | — | false |
| 2 | Verificar Requisitos (FAI) | I | ui/fai | 2 | **true** |
| 3 | Verificar Duplicidad ALICIA | I | ui | 5 | false |
| 4 | Presentación de Proyecto | I | alumno | — | false |
| 5 | Asignar Jurado | I | cc/ui | 5 | false |
| 6 | Revisión del Proyecto | I | jurado | 15 | false |
| 7 | Aprobación del Proyecto | I | ui/decanato | 3 | false |
| 8 | Presentación Informe Final | II | alumno/asesor | — | **true** |
| 9 | Revisión del Informe Final | II | jurado | 15 | false |
| 10 | Programar Sustentación | II | ui/decanato | 7 | false |
| 11 | Sustentación | II | jurado/alumno | — | false |

---

### Tabla: `estadoexpediente`

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `codigo` | VARCHAR(30) | UNIQUE | `pendiente`, `en_revision`, `observado`, `aprobado`, `rechazado`, `cerrado` |
| `descripcion` | VARCHAR(100) | NOT NULL | |
| `color_hex` | VARCHAR(7) | NULL | Para UI semafórica |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `tiporesolucion`

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `descripcion` | VARCHAR(100) | NOT NULL | Designación Jurado, Aprobación Proyecto, Sustentación |
| `codigo` | VARCHAR(30) | UNIQUE | |
| `activo` | BOOLEAN | DEFAULT true | |

---

### Tabla: `apifuente`
*Catálogo de APIs externas integradas con el sistema FAI.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK | |
| `codigo` | VARCHAR(20) | UNIQUE | `DSA`, `DGA`, `RENIEC`, `SUNEDU`, `TURNITIN`, `ALICIA` |
| `descripcion` | VARCHAR(100) | NOT NULL | |
| `tipo_protocolo` | VARCHAR(10) | DEFAULT 'REST' | `REST` \| `SOAP` |
| `timeout_segundos` | SMALLINT | DEFAULT 30 | |
| `activo` | BOOLEAN | DEFAULT true | |

---

## GRUPO 3 — Tablas de Movimiento / Expediente

> `expediente` es la cabecera. Las tablas `det_` son sus detalles. Equivale al patrón `movventa` + `det_movventa` de Delicia.

---

### Tabla: `expediente` *(cabecera principal)*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `numero_radicacion` | VARCHAR(30) | UNIQUE, NOT NULL | Generado automáticamente (ej. `FACHSE-2026-000123`) |
| `sucursal_id` | BIGINT | FK → sucursal | Facultad donde se tramita |
| `carrera_id` | BIGINT | FK → carrera | Carrera del estudiante |
| `estudiante_id` | BIGINT | FK → persona | Autor principal |
| `asesor_id` | BIGINT | FK → persona, NULL | Asesor designado |
| `titulo` | VARCHAR(300) | NOT NULL | Título del proyecto/informe |
| `tipoinvestigacion_id` | BIGINT | FK → tipoinvestigacion | |
| `tipoinvestigacion_nlp_id` | BIGINT | FK → tipoinvestigacion, NULL | Clasificación automática NLP |
| `lineainvestigacion_id` | BIGINT | FK → lineainvestigacion, NULL | |
| `areaconocimiento_id` | BIGINT | FK → areaconocimiento, NULL | |
| `tiporequerimiento_id` | BIGINT | FK → tiporequerimiento | Proyecto o Informe Final |
| `etapa` | CHAR(1) | CHECK IN ('I','II'), DEFAULT 'I' | |
| `fase_actual_id` | SMALLINT | FK → fase | Fase activa |
| `estadoexpediente_id` | BIGINT | FK → estadoexpediente | |
| `resumen` | TEXT | NULL | Resumen del proyecto |
| `palabras_clave` | VARCHAR(300) | NULL | |
| `fecha_radicacion` | TIMESTAMP | NOT NULL | |
| `fecha_cierre` | TIMESTAMP | NULL | |
| `deleted_at` | TIMESTAMP | NULL | Soft delete |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

**Índices:** `(sucursal_id, estadoexpediente_id, fase_actual_id)`, `(estudiante_id)`, `(numero_radicacion)`

---

### Tabla: `det_expedientedocumento`
*Archivos PDF adjuntos al expediente.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `tipo_documento` | VARCHAR(50) | NOT NULL | `proyecto_pdf`, `informe_pdf`, `voucher_pago`, `reporte_turnitin`, `formato_01` |
| `nombre_original` | VARCHAR(300) | NOT NULL | Nombre del archivo al subir |
| `ruta_almacenamiento` | VARCHAR(500) | NOT NULL | Path en storage institucional |
| `tamanio_bytes` | BIGINT | NOT NULL | |
| `hash_sha256` | VARCHAR(64) | NULL | Integridad del archivo |
| `subido_por_id` | BIGINT | FK → persona | |
| `activo` | BOOLEAN | DEFAULT true | |
| `deleted_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `det_expedientefase`
*Historial completo de fases del expediente. Trazabilidad inmutable.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `fase_id` | SMALLINT | FK → fase | |
| `estadoexpediente_id` | BIGINT | FK → estadoexpediente | Estado al entrar a la fase |
| `actor_id` | BIGINT | FK → persona | Quien ejecutó el cambio |
| `observacion` | TEXT | NULL | Comentario del cambio |
| `ip_actor` | VARCHAR(45) | NULL | |
| `fecha_inicio` | TIMESTAMP | NOT NULL | |
| `fecha_fin` | TIMESTAMP | NULL | NULL = fase activa |
| `created_at` | TIMESTAMP | NOT NULL | |

**Nota:** Sin `updated_at` ni `deleted_at`. Registro inmutable.

---

### Tabla: `det_expedienteobservacion`
*Observaciones del jurado por ronda. Con bloqueo RGT.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `jurado_id` | BIGINT | FK → persona | Jurado que observa |
| `tipoobservacion_id` | BIGINT | FK → tipoobservacion | |
| `ronda` | SMALLINT | DEFAULT 1, NOT NULL | Número de ronda de revisión |
| `descripcion` | TEXT | NOT NULL | |
| `bloqueado` | BOOLEAN | DEFAULT false | true = no se puede agregar más (RGT) |
| `subsanado` | BOOLEAN | DEFAULT false | El estudiante subsanó |
| `fecha_subsanacion` | TIMESTAMP | NULL | |
| `deleted_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

**Regla de negocio:** Si `ronda > 1` y ya existen observaciones de `ronda = 1` para ese jurado en ese expediente, el sistema bloquea la inserción (constraint a nivel de aplicación + check en `bloqueado`).

---

### Tabla: `det_expedientejurado`
*Los 3 jurados asignados a un expediente.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `jurado_id` | BIGINT | FK → persona | |
| `rol_jurado` | VARCHAR(20) | NOT NULL | `presidente` \| `secretario` \| `vocal` |
| `fecha_asignacion` | TIMESTAMP | NOT NULL | |
| `resolucion_id` | BIGINT | FK → resolucion, NULL | Resolución de designación |
| `aprobado` | BOOLEAN | NULL | NULL = pendiente, true = aprobado, false = observado |
| `fecha_evaluacion` | TIMESTAMP | NULL | |
| `codigo_2fa_usado` | VARCHAR(10) | NULL | Código OTP con el que firmó |
| `activo` | BOOLEAN | DEFAULT true | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

**Restricción única:** `(expediente_id, jurado_id, rol_jurado)`

---

### Tabla: `det_expedientecoautor`
*Coautores del proyecto (máx. 2 en pregrado).*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `persona_id` | BIGINT | FK → persona | El coautor |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `resolucion`
*Resoluciones emitidas por Decanato.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `sucursal_id` | BIGINT | FK → sucursal | |
| `tiporesolucion_id` | BIGINT | FK → tiporesolucion | |
| `numero_resolucion` | VARCHAR(50) | NOT NULL | Número oficial |
| `fecha_emision` | DATE | NOT NULL | |
| `documento_url` | VARCHAR(500) | NULL | PDF de la resolución |
| `emitido_por_id` | BIGINT | FK → persona | |
| `deleted_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `sustentacion`
*Programación de fecha, hora y lugar de sustentación.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente, UNIQUE | Un expediente → una sustentación |
| `sucursal_id` | BIGINT | FK → sucursal | |
| `fecha_hora` | TIMESTAMP | NOT NULL | |
| `lugar` | VARCHAR(200) | NOT NULL | Aula o sala |
| `modalidad` | VARCHAR(20) | DEFAULT 'presencial' | `presencial` \| `virtual` |
| `enlace_virtual` | VARCHAR(500) | NULL | URL Zoom/Meet si es virtual |
| `resolucion_id` | BIGINT | FK → resolucion, NULL | |
| `estado` | VARCHAR(20) | DEFAULT 'programada' | `programada` \| `realizada` \| `postergada` \| `cancelada` |
| `nota_final` | DECIMAL(4,2) | NULL | Calificación (0-20) |
| `resultado` | VARCHAR(20) | NULL | `aprobado` \| `desaprobado` |
| `acta_url` | VARCHAR(500) | NULL | PDF del acta |
| `deleted_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

## GRUPO 4 — Tablas FAI

> Filtros Administrativos Automatizados. Grupo nuevo sin equivalente en Delicia. El corazón diferenciador del sistema.

---

### Tabla: `faiconfig`
*Configuración de cada filtro FAI por sucursal (URL del servicio, token, habilitado).*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `sucursal_id` | BIGINT | FK → sucursal | Facultad que configura este FAI |
| `apifuente_id` | BIGINT | FK → apifuente | |
| `fai_codigo` | VARCHAR(10) | NOT NULL | RF02.1 … RF02.6 |
| `url_servicio` | VARCHAR(500) | NULL | Override de URL (si vacío, usa .env global) |
| `token_servicio` | VARCHAR(500) | NULL | Token encriptado |
| `activo` | BOOLEAN | DEFAULT true | |
| `modo_fallback` | VARCHAR(20) | DEFAULT 'manual' | `manual` \| `auto_aprobar` \| `auto_rechazar` |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

**Restricción única:** `(sucursal_id, fai_codigo)`

---

### Tabla: `fairesultado`
*Resultado de cada verificación FAI. Registro auditable por expediente.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `fai_codigo` | VARCHAR(10) | NOT NULL | RF02.1 … RF02.6 |
| `apifuente_id` | BIGINT | FK → apifuente | |
| `estado` | VARCHAR(20) | NOT NULL | `aprobado` \| `rechazado` \| `pendiente` \| `error` \| `fallback_manual` |
| `valor_obtenido` | VARCHAR(200) | NULL | Resultado cuantificable (ej. "175 créditos", "18%") |
| `valor_umbral` | VARCHAR(200) | NULL | Umbral aplicado (ej. "160 créditos mín.") |
| `respuesta_raw` | JSONB | NULL | Respuesta completa de la API |
| `motivorechazo_id` | BIGINT | FK → motivorechazo, NULL | Si estado = rechazado |
| `ip_actor` | VARCHAR(45) | NULL | |
| `verificado_at` | TIMESTAMP | NOT NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |

**Nota:** Inmutable. Sin `updated_at` ni `deleted_at`.  
**Índices:** `(expediente_id, fai_codigo)`, `(estado)`

---

### Tabla: `faiintento`
*Reintentos cuando la API externa falla. Para auditoría y reintento automático.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `fai_codigo` | VARCHAR(10) | NOT NULL | |
| `intento_numero` | SMALLINT | NOT NULL | 1, 2, 3… |
| `http_status` | SMALLINT | NULL | Código HTTP de respuesta |
| `error_mensaje` | TEXT | NULL | |
| `duracion_ms` | INT | NULL | Tiempo de respuesta |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `failog`
*Log detallado de cada llamada HTTP a APIs externas.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `apifuente_id` | BIGINT | FK → apifuente | |
| `fai_codigo` | VARCHAR(10) | NOT NULL | |
| `url_llamada` | VARCHAR(500) | NULL | URL real consumida |
| `metodo_http` | VARCHAR(10) | DEFAULT 'POST' | |
| `request_payload` | JSONB | NULL | Datos enviados (sin tokens) |
| `response_body` | JSONB | NULL | Respuesta recibida |
| `http_status` | SMALLINT | NULL | |
| `duracion_ms` | INT | NULL | Tiempo en ms |
| `created_at` | TIMESTAMP | NOT NULL | |

---

## GRUPO 5 — Seguridad y 2FA

---

### Tabla: `usuario`
*Credenciales de acceso. Desacoplado de `persona` para separar identidad de acceso.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `persona_id` | BIGINT | FK → persona, UNIQUE | |
| `email` | VARCHAR(150) | UNIQUE, NOT NULL | Email de acceso |
| `password` | VARCHAR(255) | NOT NULL | Bcrypt hash |
| `email_verificado_at` | TIMESTAMP | NULL | |
| `ultimo_login_at` | TIMESTAMP | NULL | |
| `activo` | BOOLEAN | DEFAULT true | |
| `bloqueado` | BOOLEAN | DEFAULT false | Bloqueo por intentos fallidos |
| `bloqueado_hasta` | TIMESTAMP | NULL | |
| `remember_token` | VARCHAR(100) | NULL | |
| `deleted_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `dosfactorconfig`
*Configuración 2FA por usuario.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `usuario_id` | BIGINT | FK → usuario, UNIQUE | |
| `driver` | VARCHAR(10) | DEFAULT 'totp' | `totp` \| `sms` |
| `secret_totp` | VARCHAR(255) | NULL | Secret TOTP encriptado |
| `telefono_sms` | VARCHAR(20) | NULL | Para driver SMS |
| `activo` | BOOLEAN | DEFAULT false | |
| `activado_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `dosfactorcodigo`
*Códigos OTP generados (con expiración). Para driver SMS.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `usuario_id` | BIGINT | FK → usuario | |
| `codigo` | VARCHAR(10) | NOT NULL | OTP de 6 dígitos |
| `proposito` | VARCHAR(50) | NOT NULL | `login` \| `firma_jurado` \| `aprobacion_ui` \| `emision_resolucion` |
| `expediente_id` | BIGINT | FK → expediente, NULL | Expediente relacionado (acciones críticas) |
| `expira_at` | TIMESTAMP | NOT NULL | |
| `usado_at` | TIMESTAMP | NULL | NULL = no usado |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `dosfactorIntento`
*Intentos de verificación 2FA. Para bloqueo por fuerza bruta.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `usuario_id` | BIGINT | FK → usuario | |
| `exitoso` | BOOLEAN | NOT NULL | |
| `ip` | VARCHAR(45) | NOT NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `sesion`
*Sesiones activas con IP y user-agent. Equivalente a `sessions` de Laravel, pero enriquecida.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | VARCHAR(255) | PK | Session ID de Laravel |
| `usuario_id` | BIGINT | FK → usuario, NULL | NULL = sesión anónima |
| `ip` | VARCHAR(45) | NULL | |
| `user_agent` | TEXT | NULL | |
| `payload` | TEXT | NOT NULL | Serializado de Laravel |
| `last_activity` | INT | NOT NULL | Timestamp Unix |

---

### Tabla: `personal_access_tokens`
*Tokens API — Laravel Sanctum. Sin modificar.*

| Columna | Tipo | Restricciones |
|---------|------|---------------|
| `id` | BIGINT | PK |
| `tokenable_type` | VARCHAR(255) | NOT NULL |
| `tokenable_id` | BIGINT | NOT NULL |
| `name` | VARCHAR(255) | NOT NULL |
| `token` | VARCHAR(64) | UNIQUE |
| `abilities` | TEXT | NULL |
| `last_used_at` | TIMESTAMP | NULL |
| `expires_at` | TIMESTAMP | NULL |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

---

## GRUPO 6 — Control y Seguimiento

---

### Tabla: `controlplazo`
*Fechas de inicio y vencimiento por fase y expediente.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `fase_id` | SMALLINT | FK → fase | |
| `fecha_inicio` | TIMESTAMP | NOT NULL | |
| `fecha_vencimiento` | TIMESTAMP | NOT NULL | Calculada al iniciar la fase |
| `dias_habiles` | SMALLINT | NOT NULL | Plazo en días hábiles |
| `vencido` | BOOLEAN | DEFAULT false | Se actualiza con job nocturno |
| `art123d_habilitado` | BOOLEAN | DEFAULT false | 2/3 jurados aprobaron y venció |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `alertaplazo`
*Registro de alertas enviadas por vencimiento de plazo.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `controlplazo_id` | BIGINT | FK → controlplazo | |
| `expediente_id` | BIGINT | FK → expediente | |
| `destinatario_id` | BIGINT | FK → persona | |
| `tipo_alerta` | VARCHAR(30) | NOT NULL | `pre_vencimiento` \| `vencimiento` \| `art123d` |
| `canal` | VARCHAR(20) | NOT NULL | `email` \| `sms` \| `sistema` |
| `enviado_at` | TIMESTAMP | NOT NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `actasustentacion`
*Acta de calificación registrada por el jurado tras la sustentación.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `sustentacion_id` | BIGINT | FK → sustentacion, UNIQUE | |
| `expediente_id` | BIGINT | FK → expediente | |
| `nota_jurado1` | DECIMAL(4,2) | NULL | |
| `nota_jurado2` | DECIMAL(4,2) | NULL | |
| `nota_jurado3` | DECIMAL(4,2) | NULL | |
| `nota_promedio` | DECIMAL(4,2) | NULL | Calculado automáticamente |
| `resultado` | VARCHAR(20) | NOT NULL | `aprobado` \| `desaprobado` |
| `observaciones_acta` | TEXT | NULL | |
| `acta_url` | VARCHAR(500) | NULL | PDF generado |
| `firmado_por_presidente_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `repositoriodeposito`
*Verificación de depósito en repositorio institucional.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente, UNIQUE | |
| `repositorio_url` | VARCHAR(500) | NULL | URL en repositorio institucional |
| `codigo_repositorio` | VARCHAR(100) | NULL | Handle o código asignado |
| `depositado_at` | TIMESTAMP | NULL | |
| `verificado_por_id` | BIGINT | FK → persona, NULL | |
| `verificado_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `nlpclasificacion`
*Resultados de clasificación automática cuantitativo/cualitativo mediante NLP.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `tipoinvestigacion_sugerido_id` | BIGINT | FK → tipoinvestigacion | Sugerencia del modelo |
| `confianza` | DECIMAL(5,4) | NULL | Score 0.0 - 1.0 |
| `tokens_analizados` | JSONB | NULL | Palabras clave detectadas |
| `modelo_version` | VARCHAR(50) | NULL | Versión del modelo NLP |
| `confirmado_por_id` | BIGINT | FK → persona, NULL | UI confirma o descarta |
| `confirmado_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `docente_rrhh_sync`
*Cache de sincronización con el sistema de RRHH para verificar docentes activos.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `persona_id` | BIGINT | FK → persona | |
| `activo_rrhh` | BOOLEAN | NOT NULL | Estado en RRHH |
| `cargo` | VARCHAR(100) | NULL | Cargo oficial |
| `regimen` | VARCHAR(50) | NULL | Nombrado, Contratado, etc. |
| `sincronizado_at` | TIMESTAMP | NOT NULL | |
| `fuente` | VARCHAR(50) | DEFAULT 'rrhh_api' | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

## GRUPO 7 — Notificaciones

---

### Tabla: `plantillanotificacion`
*Templates de email/SMS configurables por sucursal.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `sucursal_id` | BIGINT | FK → sucursal, NULL | NULL = plantilla global |
| `codigo` | VARCHAR(50) | NOT NULL | ej. `expediente_radicado`, `fai_aprobado`, `plazo_vencimiento` |
| `canal` | VARCHAR(20) | NOT NULL | `email` \| `sms` |
| `asunto` | VARCHAR(200) | NULL | Solo para email |
| `cuerpo` | TEXT | NOT NULL | Con variables `{{nombre}}`, `{{numero_radicacion}}` |
| `activo` | BOOLEAN | DEFAULT true | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `notificacion`
*Cola de notificaciones generadas por el sistema.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente, NULL | |
| `plantillanotificacion_id` | BIGINT | FK → plantillanotificacion | |
| `canal` | VARCHAR(20) | NOT NULL | `email` \| `sms` \| `sistema` |
| `estado` | VARCHAR(20) | DEFAULT 'pendiente' | `pendiente` \| `enviado` \| `fallido` |
| `intentos` | SMALLINT | DEFAULT 0 | |
| `enviado_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `det_notificaciondestinatario`
*A quién fue enviada cada notificación y su estado de lectura.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `notificacion_id` | BIGINT | FK → notificacion | |
| `persona_id` | BIGINT | FK → persona | |
| `email_enviado` | VARCHAR(150) | NULL | Email usado al momento de envío |
| `estado` | VARCHAR(20) | DEFAULT 'pendiente' | `pendiente` \| `entregado` \| `leido` \| `fallido` |
| `leido_at` | TIMESTAMP | NULL | |
| `entregado_at` | TIMESTAMP | NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |
| `updated_at` | TIMESTAMP | NOT NULL | |

---

## GRUPO 8 — Bitácoras Especializadas

> Todas las bitácoras son **inmutables**: sin `updated_at`, sin `deleted_at`, sin soft delete.

---

### Tabla: `bit_expediente`
*Cada cambio de estado en un expediente. Registro legal inmutable.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `actor_id` | BIGINT | FK → persona | |
| `accion` | VARCHAR(100) | NOT NULL | ej. `cambio_fase`, `aprobacion_jurado`, `rechazo_fai` |
| `fase_anterior_id` | SMALLINT | FK → fase, NULL | |
| `fase_nueva_id` | SMALLINT | FK → fase, NULL | |
| `estado_anterior_id` | BIGINT | FK → estadoexpediente, NULL | |
| `estado_nuevo_id` | BIGINT | FK → estadoexpediente, NULL | |
| `detalle` | JSONB | NULL | Datos adicionales de la acción |
| `ip_actor` | VARCHAR(45) | NULL | |
| `codigo_2fa` | VARCHAR(10) | NULL | OTP usado si requirió 2FA |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `bit_firma`
*Registro de firmas digitales del asesor y jurado (con código 2FA).*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `expediente_id` | BIGINT | FK → expediente | |
| `firmante_id` | BIGINT | FK → persona | |
| `rol_firmante` | VARCHAR(20) | NOT NULL | `asesor` \| `jurado` \| `presidente_jurado` |
| `tipo_firma` | VARCHAR(50) | NOT NULL | `aprobacion_proyecto` \| `reporte_turnitin` \| `acta_sustentacion` |
| `codigo_2fa_usado` | VARCHAR(10) | NOT NULL | |
| `ip_actor` | VARCHAR(45) | NOT NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `bit_accesousuario`
*Login/logout con IP, user-agent y resultado.*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `usuario_id` | BIGINT | FK → usuario | |
| `accion` | VARCHAR(20) | NOT NULL | `login` \| `logout` \| `login_fallido` \| `bloqueo` |
| `ip` | VARCHAR(45) | NOT NULL | |
| `user_agent` | TEXT | NULL | |
| `resultado` | BOOLEAN | NOT NULL | true = exitoso |
| `created_at` | TIMESTAMP | NOT NULL | |

---

### Tabla: `bit_faiauditoria`
*Registro de cuando la UI interviene manualmente en un resultado FAI (modo fallback).*

| Columna | Tipo | Restricciones | Descripción |
|---------|------|---------------|-------------|
| `id` | BIGINT | PK, autoincrement | |
| `fairesultado_id` | BIGINT | FK → fairesultado | |
| `expediente_id` | BIGINT | FK → expediente | |
| `actor_id` | BIGINT | FK → persona | Personal de UI |
| `decision_manual` | VARCHAR(20) | NOT NULL | `aprobado` \| `rechazado` |
| `justificacion` | TEXT | NOT NULL | |
| `ip_actor` | VARCHAR(45) | NOT NULL | |
| `created_at` | TIMESTAMP | NOT NULL | |

---

## Diagrama de Relaciones Clave

```
universidad (1)
    └──► sucursal / facultad (N)
              └──► carrera (N)
              └──► rel_parametrosucursal
              └──► rel_fasesucursal
              └──► rel_lineainvestigacionsucursal

persona (1)
    ├──► personamaestro (maestro de identidad)
    ├──► rolpersona ◄──► rol
    ├──► usuario ──► dosfactorconfig
    └──► expediente (como estudiante, asesor, jurado)

expediente (cabecera central)
    ├──► det_expedientedocumento
    ├──► det_expedientefase       ← trazabilidad completa
    ├──► det_expedienteobservacion
    ├──► det_expedientejurado
    ├──► det_expedientecoautor
    ├──► fairesultado ──► failog
    ├──► controlplazo ──► alertaplazo
    ├──► sustentacion ──► actasustentacion
    ├──► resolucion
    ├──► repositoriodeposito
    ├──► bit_expediente            ← log inmutable
    └──► notificacion ──► det_notificaciondestinatario
```

---

## Resumen de Tablas

| # | Tabla | Grupo | Tipo |
|---|-------|-------|------|
| 1 | `universidad` | 0 - Infraestructura | Maestra |
| 2 | `sucursal` | 0 - Infraestructura | Maestra |
| 3 | `carrera` | 0 - Infraestructura | Maestra |
| 4 | `rel_lineainvestigacionsucursal` | 0 - Infraestructura | Pivote |
| 5 | `rel_fasesucursal` | 0 - Infraestructura | Pivote |
| 6 | `personamaestro` | 1 - Heredadas | Maestra |
| 7 | `persona` | 1 - Heredadas | Transaccional |
| 8 | `rol` | 1 - Heredadas | Catálogo |
| 9 | `rolpersona` | 1 - Heredadas | Pivote |
| 10 | `parametro` | 1 - Heredadas | Configuración |
| 11 | `rel_parametrosucursal` | 1 - Heredadas | Override |
| 12 | `categoriaparametro` | 1 - Heredadas | Catálogo |
| 13 | `bitacora` | 1 - Heredadas | Log |
| 14 | `ubigeo` | 1 - Heredadas | Geográfica |
| 15 | `tipodocumento` | 2 - Maestras | Catálogo |
| 16 | `tipoinvestigacion` | 2 - Maestras | Catálogo |
| 17 | `areaconocimiento` | 2 - Maestras | Catálogo |
| 18 | `lineainvestigacion` | 2 - Maestras | Catálogo |
| 19 | `tiporequerimiento` | 2 - Maestras | Catálogo |
| 20 | `tipoobservacion` | 2 - Maestras | Catálogo |
| 21 | `motivorechazo` | 2 - Maestras | Catálogo |
| 22 | `fase` | 2 - Maestras | Catálogo fijo |
| 23 | `estadoexpediente` | 2 - Maestras | Catálogo |
| 24 | `tiporesolucion` | 2 - Maestras | Catálogo |
| 25 | `apifuente` | 2 - Maestras | Catálogo |
| 26 | `expediente` | 3 - Movimiento | Cabecera principal |
| 27 | `det_expedientedocumento` | 3 - Movimiento | Detalle |
| 28 | `det_expedientefase` | 3 - Movimiento | Detalle |
| 29 | `det_expedienteobservacion` | 3 - Movimiento | Detalle |
| 30 | `det_expedientejurado` | 3 - Movimiento | Detalle |
| 31 | `det_expedientecoautor` | 3 - Movimiento | Detalle |
| 32 | `resolucion` | 3 - Movimiento | Documento |
| 33 | `sustentacion` | 3 - Movimiento | Transaccional |
| 34 | `actasustentacion` | 3 - Movimiento | Documento |
| 35 | `faiconfig` | 4 - FAI | Configuración |
| 36 | `fairesultado` | 4 - FAI | Transaccional |
| 37 | `faiintento` | 4 - FAI | Log |
| 38 | `failog` | 4 - FAI | Log HTTP |
| 39 | `usuario` | 5 - Seguridad | Auth |
| 40 | `dosfactorconfig` | 5 - Seguridad | Config 2FA |
| 41 | `dosfactorcodigo` | 5 - Seguridad | OTP |
| 42 | `dosfactorintento` | 5 - Seguridad | Log 2FA |
| 43 | `sesion` | 5 - Seguridad | Sesiones |
| 44 | `personal_access_tokens` | 5 - Seguridad | API Tokens |
| 45 | `controlplazo` | 6 - Control | Seguimiento |
| 46 | `alertaplazo` | 6 - Control | Seguimiento |
| 47 | `repositoriodeposito` | 6 - Control | Verificación |
| 48 | `nlpclasificacion` | 6 - Control | IA |
| 49 | `docente_rrhh_sync` | 6 - Control | Sincronización |
| 50 | `plantillanotificacion` | 7 - Notificaciones | Config |
| 51 | `notificacion` | 7 - Notificaciones | Cola |
| 52 | `det_notificaciondestinatario` | 7 - Notificaciones | Detalle |
| 53 | `bit_expediente` | 8 - Bitácoras | Log inmutable |
| 54 | `bit_firma` | 8 - Bitácoras | Log inmutable |
| 55 | `bit_accesousuario` | 8 - Bitácoras | Log inmutable |
| 56 | `bit_faiauditoria` | 8 - Bitácoras | Log inmutable |
| — | `failed_jobs` | Laravel | Cola |
| — | `jobs` | Laravel | Cola |
| — | `migrations` | Laravel | Control |
| — | `password_resets` | Laravel | Auth |

**Total de tablas de negocio: 56**  
**Total incluyendo tablas Laravel: 60**

---

*SELGESTIUM · Diseño de Base de Datos v1.0.0 · 2026-I · Paradigma db_delicia · Laravel 11 + PostgreSQL*
