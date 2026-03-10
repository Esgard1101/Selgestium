# Selgestium
SELGESTIUN es una plataforma SaaS (Software as a Service) diseñada para automatizar y gestionar digitalmente el proceso de trámites de tesis, desde la radicación inicial hasta la sustentación y el depósito en repositorio.
Su arquitectura está pensada para ser desplegada en un modelo Base de Datos por Universidad, garantizando el aislamiento total de datos entre las instituciones que contraten el servicio.

## Stack Tecnológico Principal:
Backend: Laravel 11 (PHP)
Base de Datos: PostgreSQL
Flujo de Trabajo: GitHub Flow con ramas main (Producción), collabs (Integración) y feat/ o fix/ para desarrollo.
## Filosofía: 
Adopta patrones robustos de sistemas empresariales (ej. db_delicia), incluyendo el uso de Soft Deletes universales y un enfoque transaccional centrado en la tabla expediente.
## Características y Componentes Clave:
Arquitectura Multi-Tenant: Configuración de identidad (nombre, logo, colores) y de bases de datos separadas por cada institución (facultad/sucursal).
## Filtros Administrativos Automatizados (FAI):
 Módulo central para la verificación automatizada de requisitos académicos y administrativos mediante la integración de APIs externas.
## Servicios FAI:
 FaiDsaService (Créditos), FaiDgaService (Pagos), FaiReniecService (Identidad), FaiSuneduService (Bachiller), FaiTurnitinService (Similitud).
## Procesos Asíncronos:
 Uso de Jobs para ejecuciones lentas (ej. Turnitin/ALICIA).
## Trazabilidad y Auditoría:
 Implementación del Trait HasAuditLog para un registro inmutable de actor, IP y timestamp en cambios de fase y estados.
## Sistema de Diseño Configurable:
 Paleta de colores (--color-primary, --color-accent) inyectada dinámicamente desde el .env para adoptar la identidad visual de cada universidad cliente.

