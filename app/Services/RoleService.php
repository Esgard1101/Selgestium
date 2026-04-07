<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Obtiene el rol principal activo de una persona en cualquier sucursal.
     * Retorna el primer rol encontrado (sin deleted_at).
     */
    public function getRolPrincipal(int $personaId): ?object
    {
        return DB::table('rolpersona')
            ->join('rol', 'rolpersona.rol_id', '=', 'rol.id')
            ->where('rolpersona.persona_id', $personaId)
            ->whereNull('rolpersona.deleted_at')
            ->whereNull('rol.deleted_at')
            ->select('rol.id', 'rol.descripcion', 'rolpersona.sucursal_id')
            ->first();
    }

    /**
     * Obtiene todos los roles de una persona en una sucursal específica.
     */
    public function getRolesPorSucursal(int $personaId, int $sucursalId): array
    {
        return DB::table('rolpersona')
            ->join('rol', 'rolpersona.rol_id', '=', 'rol.id')
            ->where('rolpersona.persona_id', $personaId)
            ->where('rolpersona.sucursal_id', $sucursalId)
            ->whereNull('rolpersona.deleted_at')
            ->whereNull('rol.deleted_at')
            ->select('rol.id', 'rol.descripcion')
            ->get()
            ->toArray();
    }

    /**
     * Verifica si una persona tiene un rol específico (por ID de rol).
     */
    public function hasRole(int $personaId, int $rolId): bool
    {
        return DB::table('rolpersona')
            ->where('persona_id', $personaId)
            ->where('rol_id', $rolId)
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Asigna un rol a una persona en una sucursal.
     * Usa upsert para evitar duplicados.
     */
    public function assignRole(int $personaId, int $rolId, int $sucursalId): void
    {
        $existe = DB::table('rolpersona')
            ->where('persona_id', $personaId)
            ->where('rol_id', $rolId)
            ->where('sucursal_id', $sucursalId)
            ->whereNull('deleted_at')
            ->exists();

        if (!$existe) {
            DB::table('rolpersona')->insert([
                'persona_id'  => $personaId,
                'rol_id'      => $rolId,
                'sucursal_id' => $sucursalId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    /**
     * Construye y carga en sesión todos los datos del usuario autenticado:
     * perfil_id, perfil, sucursal_id, persona_id, nombres, apellidos, nrodoc, permisos[].
     *
     * Llamar desde el Event Listener de Login.
     */
    public function loadSessionData(User $user): void
    {
        $sessionData = [
            'id'         => $user->id,
            'perfil_id'  => null,
            'perfil'     => null,
            'sucursal_id'=> null,
            'persona_id' => $user->persona_id,
            'nombres'    => $user->name,
            'apellidos'  => '',
            'nrodoc'     => '',
            'permisos'   => [],
        ];

        // Cargar datos de persona si existe el vínculo
        if ($user->persona_id) {
            $persona = DB::table('persona')
                ->where('id', $user->persona_id)
                ->whereNull('deleted_at')
                ->select('id', 'nombre', 'apellido', 'dni', 'sucursal_id')
                ->first();

            if ($persona) {
                $sessionData['nombres']    = $persona->nombre;
                $sessionData['apellidos']  = $persona->apellido;
                $sessionData['nrodoc']     = $persona->dni ?? '';
                $sessionData['sucursal_id']= $persona->sucursal_id;
            }

            // Cargar rol principal
            $rolRow = $this->getRolPrincipal($user->persona_id);
            if ($rolRow) {
                $sessionData['perfil_id']  = $rolRow->id;
                $sessionData['perfil']     = $rolRow->descripcion;
                // sucursal del rol tiene prioridad sobre sucursal de persona
                if ($rolRow->sucursal_id) {
                    $sessionData['sucursal_id'] = $rolRow->sucursal_id;
                }
            }
        }

        // Cargar array de permisos (IDs de opcionmenu) para el rol principal
        // Por ahora vacío hasta implementar tabla opcionmenu
        $sessionData['permisos'] = $this->getPermisosIds(
            $user->persona_id ?? 0,
            $sessionData['sucursal_id'] ?? 0
        );

        session($sessionData);
    }

    /**
     * Retorna array de IDs de opciones de menú permitidas para el rol de la persona.
     * Cero queries extra si no hay tabla opcionmenu aún — retorna [] de forma segura.
     */
    public function getPermisosIds(int $personaId, int $sucursalId): array
    {
        if ($personaId === 0 || $sucursalId === 0) {
            return [];
        }

        // Verificar si la tabla opcionmenu existe antes de consultar
        if (!DB::getSchemaBuilder()->hasTable('opcionmenu')) {
            return [];
        }

        return DB::table('rolpersona')
            ->join('rol_opcionmenu', 'rolpersona.rol_id', '=', 'rol_opcionmenu.rol_id')
            ->where('rolpersona.persona_id', $personaId)
            ->where('rolpersona.sucursal_id', $sucursalId)
            ->whereNull('rolpersona.deleted_at')
            ->pluck('rol_opcionmenu.opcionmenu_id')
            ->unique()
            ->values()
            ->toArray();
    }
}
