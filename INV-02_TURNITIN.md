# INV-02 · Reporte de Investigación FAI RF-02.6 (Turnitin)

## 🔍 Situación Actual
Actualmente, la verificación del reporte Turnitin se realiza de manera completamente manual y descentralizada. El asesor de tesis gestiona las revisiones directamente desde sus cuentas institucionales asignadas por la universidad, descargando los reportes de similitud generados por la plataforma.

## 🌐 Disponibilidad de API
Consultado con la Oficina de Tecnologías de la Información (OTI), la UNPRG posee licencias para el uso de la plataforma Turnitin, pero actualmente **no se dispone de acceso habilitado a la API REST** para integraciones de terceros. El acceso está limitado estrictamente a la interfaz web estándar para docentes y alumnos.

## 🔄 Flujo Acordado
Dado el impedimento técnico del API, se opta por un flujo semimanual:
1. El **Asesor** gestiona la revisión del borrador en Turnitin.
2. Una vez alcanzada la aprobación, el asesor descarga el archivo PDF oficial del reporte de similitud.
3. Este documento se adjunta digitalmente al expediente en el sistema SELGESTIUM durante la subida de documentación.

## ✍️ Quién Ingresa el Porcentaje
El porcentaje (%) exacto arrojado por el reporte será introducido en SELGESTIUM por el **Operador de la Unidad de Investigación (UI)** al cotejar el documento PDF cargado. 

## 📊 Umbrales Confirmados
- **Similitud Máxima Permitida:** 25% (según directiva RGT vigente).
- **Excepciones:** Citas bibliográficas debidamente referenciadas y filtradas.

## 📝 Notas para Esgardo
- Guardar el porcentaje en la tabla `fairesultado` campo `valor_obtenido`.
- Incluir almacenamiento del archivo PDF de evidencia en `det_expedientedocumento`.
