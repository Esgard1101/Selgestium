<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpedienteRequest;
use App\Services\ExpedienteService;
use App\Models\Expediente; // Asegúrate de importar tu modelo
use Illuminate\Http\Request;
use Exception;
use App\Traits\LibraryTrait;

/**
 * @method mixed paginator($per_page, $page, $total)
 */
class PurController extends Controller
{

    use LibraryTrait;

    /**
     * MUESTRA EL LISTADO DE EXPEDIENTES
     */
    public function index(Request $request, ExpedienteService $expedienteService)
    {
        try {
            // Obtenemos los datos de sesión
            $sucursalId = $request->user()->sucursal_id ?? 1;
            $estudianteId = session('usuario_id') ?? $request->user()->persona_id ?? 1;

            // Llamamos al Service (él se encarga de la paginación nativa)
            $expedientes = $expedienteService->listarPorEstudiante($estudianteId, $sucursalId);

            // Retornamos la vista Blade directamente con la data 
            return view('pur.list', compact('expedientes'));
        } catch (\Exception $e) {
            $expedientes = collect();

            // Pasamos una variable 'errorMessage' explicita
            return view('pur.list', [
                'expedientes' => $expedientes,
                'errorMessage' => 'Ocurrió un problema al cargar los expedientes: ' . $e->getMessage()
            ]);
        }
    }



    public function create()
    {

        return view('pur.create');
    }

    public function store(StoreExpedienteRequest $request, ExpedienteService $expedienteService)
    {
        try {
            // 1. Obtenemos los datos
            $datosValidados = $request->validated();

            // 2. Definimos los IDs
            $sucursalId = $request->user()->sucursal_id ?? 1;
            $estudianteId = session('usuario_id') ?? $request->user()->persona_id ?? 1;

            // 3. Obtenemos el archivo
            $archivoPdf = $request->file('archivo_pdf');

            // 4.El orden
            $numeroRadicacion = $expedienteService->registrarExpediente(
                $datosValidados,
                $sucursalId,
                $estudianteId,
                $archivoPdf
            );

            return redirect()->route('pur.index')
                ->with('success', '¡Expediente ' . $numeroRadicacion . ' registrado correctamente!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al guardar el expediente: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Buscamos el expediente por su ID. 
        // Usamos findOrFail para que si alguien pone un ID que no existe, dé error 404 automático.
        $expediente = \App\Models\Expediente::findOrFail($id);

        // Por seguridad (siguiendo las reglas), verificamos que el alumno solo vea el suyo
        // (Descomenta esto cuando tengas el login real funcionando al 100%)
        /*
    $estudianteId = session('usuario_id') ?? request()->user()->persona_id;
    if (auth()->user()->rolpersona->rol_id === 'alumno' && $expediente->estudiante_id !== $estudianteId) {
        abort(403, 'No tienes permiso para ver este expediente.');
    }
    */

        // Retornamos la vista (que crearemos en el siguiente paso) pasándole el expediente
        return view('pur.show', compact('expediente'));
    }

    public function descargar($id)
    {
        try {
            $expediente = \App\Models\Expediente::findOrFail($id);

            $documento = \Illuminate\Support\Facades\DB::table('det_expedientedocumento')
                ->where('expediente_id', $id)
                ->first();

            if (!$documento || empty($documento->ruta_almacenamiento)) {
                return back()->with('error', 'Este expediente no tiene ningún documento adjunto.');
            }

            //  Obtenemos la ruta de la BD 
            $rutaArchivo = $documento->ruta_almacenamiento;

            $rutaAbsoluta = storage_path('app/public/' . $rutaArchivo);

            if (!file_exists($rutaAbsoluta)) {
                return back()->with('error', 'El documento físico no se encuentra en el servidor. Revisa que el PDF realmente exista en tu carpeta storage.');
            }

            $nombreDescarga = $documento->nombre_original ?? ('Expediente_' . $expediente->numero_radicacion . '.pdf');

            return response()->download($rutaAbsoluta, $nombreDescarga);
        } catch (\Exception $e) {

            return back()->with('error', 'Ocurrió un error al intentar descargar: ' . $e->getMessage());
        }
    }
}
