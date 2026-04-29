<?php

/**
 * Mapeo nombre-de-ruta → opcionmenu_id.
 *
 * VerificarPermiso lee este archivo para decidir si la ruta
 * requiere un permiso específico. Las rutas NO listadas aquí
 * pasan sin verificación de menú (dashboard, rutas internas, etc.).
 *
 * IDs sincronizados con OpcionmenuSeeder:
 *   1 = Dashboard (acceso universal, no necesita mapearse aquí)
 *   2 = Radicar Expediente
 *   3 = Asignar Jurados
 *   4 = Observaciones
 *   5 = Programar Sustentacion
 *   6 = Cerrar Sustentacion
 *   7 = Historial de Acceso
 *   8 = Panel FAI
 */
return [
    'pur.create'                      => 2,
    'pur.store'                       => 2,

    'expediente.asignar-jurados.view' => 3,
    'expediente.asignar-jurados'      => 3,
    'expediente.resolucion'           => 3,

    'expediente.observaciones.view'   => 4,
    'expediente.observaciones.registrar' => 4,

    'sustentacion.programar.index'    => 5,
    'sustentacion.programar.show'     => 5,
    'sustentacion.programar.store'    => 5,

    'sustentacion.cerrar.index'       => 6,
    'sustentacion.cerrar.show'        => 6,
    'sustentacion.cerrar.store'       => 6,

    'bitacora.acceso.index'           => 7,

    // 8 = Panel FAI (ui, cc, admin)
    'fai.panel.index'                 => 8,
    'fai.panel.show'                  => 8,
    'fai.creditos.show'               => 8,
    'fai.creditos.store'              => 8,
    'fai.etapa2.index'                => 8,
    'fai.etapa2.store'                => 8,
];
