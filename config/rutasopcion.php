<?php

/**
 * Mapeo de nombre de ruta → ID de opción de menú.
 *
 * Cuando se implemente la tabla opcionmenu, agregar aquí:
 *   'expediente.asignar-jurados'   => config('opcionesmenu.asignar_jurados'),
 *   'expediente.observaciones.view'=> config('opcionesmenu.observaciones'),
 *
 * Mientras la tabla no exista, dejar vacío para que VerificarPermiso no bloquee nada.
 */
return [
    // Ejemplos (descomentar cuando exista config/opcionesmenu.php con los IDs):
    // 'expediente.asignar-jurados.view' => 1,
    // 'expediente.observaciones.view'   => 2,
    // 'sustentacion.programar.show'     => 3,
    // 'sustentacion.cerrar.show'        => 4,
    // 'pur.create'                      => 5,
];
