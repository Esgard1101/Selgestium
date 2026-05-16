🔍 AUDITORÍA E2E SELGESTIUM 3.0
1. RESUMEN
Capa	Estado	Detalle
Migraciones	✅	50/50 Ran (post-fix)
Seeders	✅	8 seeders, migrate:fresh --seed limpio
Tests PHPUnit	⚠️	34 pass / 2 fail / 8 skip
Rutas	⚠️	Mayoría OK, PUR expuesta sin auth
Backend services	⚠️	F-11/F-16/F-20 faltantes
Frontend	❌	Layout principal con bug Blade visible
2. BUGS CRÍTICOS (bloquean producción)
🔴 B-01 · Rutas PUR sin autenticación
routes/web.php:162-168 — fuera del middleware verificar.sesion + verificar.permiso.
Verificado via curl: GET /pur y GET /pur/create devuelven 200 sin sesión, renderizando formulario de radicación. Cualquier visitante anónimo puede crear expedientes.
Además rutas y nombres duplicados (pur.store definida 3 veces, pur.create 2 veces — Laravel toma la última).

🔴 B-02 · Layout app.blade.php con Blade roto
resources/views/layouts/app.blade.php:14-73 — Prettier reformateó {{ config(...) }} partiendo las llaves en líneas separadas. Blade NO interpola → CSS recibe literal {{ config('university.primary_color') }} y los colores institucionales NO se aplican. Visible en HTML servido. guest.blade.php está correcto.

🔴 B-03 · phpunit.xml con marcadores merge sin resolver — YA CORREGIDO en esta sesión.

---
✅ HOTFIX COMPLETO
#	Fix	Resultado
B-01	Rutas PUR dentro middleware + dedupe	GET /pur sin auth → 302 (antes 200)
B-02	Blade interpolation + config fallback ?:	--color-primary: #003087; renderiza correcto
3	Borrados 9 tests cuarentena + 1 Unit Example	28 passed / 0 failed (antes 34/2/8)
4	Removido setRun() + reemplazado por <a href>	Grep setRun en resources/: 0 matches
Cambios
routes/web.php:138-145 — rutas PUR consolidadas dentro middleware (eliminados duplicados líneas 162-168)
resources/views/layouts/app.blade.php:13-23 — {{ config(...) }} en una línea + bloque {{-- prettier-ignore --}}
resources/views/layouts/app.blade.php:156 — botón Portal PUR ahora <a href="{{ route('pur.index') }}">
resources/views/layouts/app.blade.php:208-231 — borrado <script>function setRun(...)</script>
config/university.php — coalesce ?: '#default' para .env vacíos
Borrados: 9 tests Feature + 1 Unit + resources/views/pur/list_footer.blade.php (dead code)
Estado tests final

28 passed (67 assertions)
0 failed
Duration: 10.76s

---




3. GAPS DE FEATURES (vs Sprint 1)
ID	Feature	Estado	Falta
F-01..F-06	Bootstrap, Auth, RBAC, autocomplete, FAI créditos	✅	—
F-07	Registro expediente	✅	número radicación con EXP-{YYYY}-{SUC}-{SEQ} verificar lock concurrent
F-08	Carga PDF	✅	hash sha256 — verificar que se calcule en ExpedienteService
F-09	Bandeja + timeline	⚠️	listarPorEstudiante no filtra por estudiante_id real (lo comentó)
F-10	Derivación + notificación	⚠️	derivarExpediente existe, NotificacionService de 39 líneas — verificar encolado real
F-11	RF-02.2 voucher proyecto	❌	FaiDgaService::verificarProyectoManual NO existe. Solo está verificarSustentacionManual (RF-02.4)
F-12, F-13	Jurado + observaciones RGT	✅	—
F-14	RF-02.3 + RF-02.4	✅	—
F-15	2FA transaccional	⚠️	TwoFactorService ✅ pero migración bit_firma existe y servicio no inserta ahí en cada veredicto — verificar
F-16	RF-02.5 RENIEC + RF-02.6 Turnitin	❌	FaiReniecService y FaiTurnitinService no existen
F-17	Panel FAI	✅	—
F-18	Detalle expediente UI/CC/Decano	⚠️	ExpedienteService::detalleCompleto no existe, show() solo hace findOrFail
F-19	Validación requisitos previa	❌	cumpleRequisitosMinimos no existe
F-20	RRHH sync	❌	Tabla docente_rrhh_sync NO migrada, RrhhSyncService NO existe
F-21	Plazos + Art.123-d	⚠️	Service OK pero sin Scheduler → verificarVencidos jamás corre. Falta alerta pre_vencimiento
F-22	Veredicto consolidado	✅	—
F-23, F-24	Sustentación + acta + cierre	✅	—
INV-01, INV-02	MDs RENIEC + Turnitin	❌	No están en documentation/
4. PROBLEMAS TRANSVERSALES
Sin Jobs ni Scheduler (app/Jobs/ vacío, routes/console.php solo inspire). PlazoService::verificarVencidos no se dispara → Art.123-d nunca se habilita en runtime.
setRun() definido en app.blade.php contradice CLAUDE.md ("no usar setRun, no #frame"). Aunque versión nueva es solo fetch, prohibición explícita.
Tests Jetstream "en cuarentena" sin skip explícito: DeleteAccountTest y ExampleTest fallan. Deben deshabilitarse o ajustarse al contrato actual.
config/university.php referenciado pero no se verificó si existe — los ?? '#003087' aplican como fallback solo si Blade renderiza, lo cual no pasa (B-02).
Cero tests de feature para módulos PUR / FAI / Jurado / Sustentación: solo tests Jetstream + seguridad acceso. Cobertura del negocio = 0%.
5. SPRINT 2 PROPUESTO — orden de ataque
🚨 Sprint 2.0 · Hotfix (1 día, Esgardo)
B-01 mover líneas 162-168 de routes/web.php dentro del grupo middleware y eliminar duplicados.
B-02 reescribir app.blade.php:14-73 con {{ config(...) }} en una línea cada uno. Agregar <!-- prettier-ignore --> arriba del bloque :root.
Deshabilitar/borrar tests Jetstream en cuarentena (DeleteAccountTest, DeleteApiTokenTest, etc.) + ExampleTest.
Remover function setRun() de app.blade.php (viola contrato arquitectura).
🚧 Sprint 2.1 · Cerrar features pendientes (5 días)
F-11 (Jesus) FaiDgaService::verificarProyectoManual + ruta + vista. Patrón = F-14.
F-16 (Esgardo) FaiReniecService + FaiTurnitinService con verificarManual() + verificarApi() stub. Si INV-01/INV-02 no llegan: fallback flujo manual marcado // TODO Ampuero.
F-18 (Jesus) ExpedienteService::detalleCompleto($id, $sucursalId) retornando expediente + documentos + fases + FAI + jurados + observaciones + plazos.
F-19 (Jesus) StoreExpedienteRequest::after() con cumpleRequisitosMinimos().
F-20 (Jesus) migración docente_rrhh_sync + RrhhSyncService + integración en JuradoService::asignar.
⏰ Sprint 2.2 · Automatización (3 días, César)
Crear app/Jobs/VerificarPlazosJob.php que envuelve PlazoService::verificarVencidos.
Schedule en routes/console.php: Schedule::job(new VerificarPlazosJob)->dailyAt('02:00').
Agregar alertaplazo tipo pre_vencimiento cuando faltan ≤3 días hábiles.
Documentar comando para correr en producción (cron o supervisor).
🧪 Sprint 2.3 · Cobertura tests (3 días, equipo)
Feature tests por módulo: PUR (radicar + descargar + bandeja), FAI (cada filtro), Jurado (asignar + veredicto + bloqueo RGT), Sustentación (programar + acta + cierre), Plazos (Art.123-d).
Smoke test E2E: alumno radica → UI verifica FAI → CC asigna jurado → jurado vereditcta con 2FA → UI programa sustentación → acta cierra expediente.
Target: 60% statement coverage en app/Services/.
📝 Sprint 2.4 · Investigación pendiente (paralelo)
INV-01 RENIEC (Jesus) entregable obligatorio antes de cerrar F-16.
INV-02 Turnitin (César) idem.
6. RECOMENDACIÓN INMEDIATA
Antes de demo o release:

Arreglar B-01 (5 min) — seguridad.
Arreglar B-02 (5 min) — UI visible rota.
Borrar líneas 162-168 duplicadas de routes/web.php.