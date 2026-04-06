<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpedienteRequest;
use App\Services\ExpedienteService;
use Illuminate\Http\Request;
use Exception;

class PurController extends Controller
{
    /**
     * Muestra el formulario de radicación 
     */
    public function create()
    {
        // Aquí luego enviaremos a la vista los datos necesarios 
        return view('pur.create');
    }

    /**
     * Recibe los datos del formulario, los valida y los envía al Service.
     */
    public function store(StoreExpedienteRequest $request, ExpedienteService $expedienteService)
    {
        try {
            //Si el código llega aquí, el FormRequest ya validó todo (PDF, título, etc.)
            $datos = $request->validated();
            $archivoPdf = $request->file('archivo_pdf');

            // Obtenemos el usuario del Request
            $user = $request->user();

            // Usamos el operador ?? (Null Coalescing). 
            // Esto significa: "Si lo de la izquierda es null, usa el 1 a la fuerza"
            $estudianteId = $user->id ?? 1;
            $sucursalId = $user->sucursal_id ?? 1;
            // Delegamos toda la lógica pesada al Service (Regla #1)
            $numeroRadicacion = $expedienteService->registrarExpediente(
                $datos,
                $sucursalId,
                $estudianteId,
                $archivoPdf
            );

            //  Redirigimos al estudiante con un mensaje de éxito y su número de trámite
            return redirect()->back()->with('success', "¡Expediente registrado con éxito! Tu número de radicación es: $numeroRadicacion");
        } catch (Exception $e) {
            // Si algo falla en la transacción del Service, regresamos con el error
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al registrar el expediente: ' . $e->getMessage());
        }
    }
}
