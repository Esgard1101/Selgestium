<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BitacoraAccesoService;
use Illuminate\Http\Request;

class BitacoraAccesoController extends Controller
{
    public function __construct(private BitacoraAccesoService $service) {}

    public function index(Request $request)
    {
        $registros = $this->service->listarPorSucursal(
            sucursalId: (int) session('sucursal_id'),
            filtros: $request->only(['fecha_desde', 'fecha_hasta', 'accion']),
        );

        return view('bitacora_acceso.list', compact('registros'));
    }
}
