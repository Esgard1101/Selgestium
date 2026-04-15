# Documento de Investigación FAI RF-02.5: Verificación de Identidad (RENIEC)

**Código de Feature:** INV-01
**Responsable de Investigación:** Jesus Salazar
**Implementador (Fase 2):** Esgardo
**Revisado/Aprobado por:** Ing. Ampuero (Pendiente de firma)

---

## 1. Respuestas a la Investigación

**¿Existe integración con RENIEC en otros sistemas de la UNPRG reutilizable?**
Tras la investigación, se determina que no se dispone de una API de RENIEC directa o vía OTI/UNPRG que pueda integrarse inmediatamente para este Sprint 1. Por lo tanto, el flujo inicial será 100% manual operativo (FAI), sentando las bases para la integración en el Sprint 2.

**Si no hay API: ¿la UI compara el DNI del expediente contra carnet físico, o se asume validado al registrar la cuenta?**
Se acordó que **no** se asume como validado. El operador correspondiente deberá realizar una verificación visual en sistema. El estudiante debe adjuntar una copia de su DNI en los documentos del expediente (F-08), y el operador validará que los datos coincidan exactamente con el registro de la tabla `persona`.

**¿La alerta RENIEC bloquea el expediente o solo lo marca como "pendiente de confirmación"?**
Si la verificación manual es fallida (rechazada por datos falsos o ilegibles), el expediente queda **bloqueado** (estado `rechazado` en el FAI). No podrá avanzar a la etapa de designación de jurado hasta que el estudiante subsane el documento de identidad.

---

## 2. Flujo Propuesto Acordado (Flujo Manual FAI)

1. El expediente ingresa a la bandeja de verificaciones pendientes del panel FAI (F-17).
2. El operador hace clic en "Verificar" en la tarjeta semafórica de "Identidad RENIEC".
3. El sistema carga una vista completa Blade donde muestra los datos del estudiante (`p.nombre`, `p.apellido`, `p.dni`) y un botón para visualizar el documento PDF adjuntado.
4. El operador revisa la copia del DNI y llena el formulario de verificación.
5. El sistema guarda el resultado inmutable en la tabla `fairesultado` y actualiza la tarjeta a Verde o Rojo.

---

## 3. Campos del Formulario (Para F-16)

El formulario Blade (`fai/verificacion_reniec.blade.php`) debe contener la misma estructura que los créditos, e incluir:

- **DNI Físico Validado:** Input text (pre-llenado con el DNI del sistema).
- **Estado de Verificación:** Selector semafórico (Aprobado [Verde] / Rechazado [Rojo]).
- **Observaciones:** Textarea (Obligatorio si el estado es Rechazado).

---

## 4. Notas Técnicas para Esgardo (Implementación F-16)

- **Patrón Obligatorio:** Debes crear `FaiReniecService.php`.
- **Método Manual:** Implementar `verificarManual(int $expedienteId, string $dniValidado, string $estado, int $validadoPorId, string $ip)`. Este método insertará el registro en `fairesultado` con `fai_codigo = 'RF02.5'`.
- **Método API:** Obligatorio dejar el cascarón preparado sin tocar el Controller:
    ```php
    public function verificarApi(int $expedienteId): void
    {
        // TODO: Sprint 2 — Integrar API REST PIDE / RENIEC
    }
    ```
- **Aviso de UI:** No olvides que el botón en tu Panel FAI debe navegar a esta vista usando navegación estándar con `<a href="{{ route(...) }}">`, prohibido usar llamadas AJAX o modales globales.
