<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpedienteRequest;
use App\Services\ExpedienteService;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\LibraryTrait;

class PurController extends Controller
{
    use LibraryTrait;

    /**
     * MUESTRA EL LISTADO DE EXPEDIENTES (Formato JSON para la SPA)
     */
    public function index(Request $request)
    {
        $sucursalId = $request->user()->sucursal_id ?? 1;


        $query = Expediente::where('sucursal_id', $sucursalId);

        $expedientes = $query->orderBy('created_at', 'desc')->paginate(10);

        // Retornamos la vista full-page Blade pasándole la variable que espera
        return view('pur.list', compact('expedientes'));
    }

    /**
     * MUESTRA EL FORMULARIO DE CREACIÓN
     */
    public function create()
    {
        return view('pur.create');
    }

    /**
     * PROCESA EL FORMULARIO Y GUARDA EL EXPEDIENTE
     */
    public function store(StoreExpedienteRequest $request, ExpedienteService $expedienteService)
    {
        try {
            $datosValidados = $request->validated();
            $sucursalId = $request->user()->sucursal_id ?? 1;
            $estudianteId = session('usuario_id') ?? 1;
            $archivoPdf = $request->file('archivo_pdf');

            // Llamamos a la función del Service
            $numeroRadicacion = $expedienteService->registrarExpediente(
                $datosValidados,
                $sucursalId,
                $estudianteId,
                $archivoPdf
            );

            $expedienteNuevo = Expediente::where('numero_radicacion', $numeroRadicacion)->first();

            if ($expedienteNuevo) {
                // Derivamos automáticamente a Fase 1
                $expedienteService->derivar(
                    $expedienteNuevo->id,
                    1,
                    $estudianteId,
                    $request->ip()
                );
            }

            // Retornamos JSON de éxito para que el frontend (Fetch) lo lea
            return redirect()->route('pur.index')->with('success', '¡Expediente registrado correctamente!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error al guardar el expediente: ' . $e->getMessage());
        }
    }

    /**
     * MUESTRA EL DETALLE DE UN EXPEDIENTE
     */
    public function show($id)
    {
        $expediente = Expediente::findOrFail($id);

        return view('pur.show', compact('expediente'));
    }

    /**
     * DESCARGA EL DOCUMENTO PDF DEL EXPEDIENTE
     */
    public function descargar($id)
    {
        try {
            $expediente = Expediente::findOrFail($id);

            $documento = DB::table('det_expedientedocumento')
                ->where('expediente_id', $id)
                ->first();

            if (!$documento || empty($documento->ruta_almacenamiento)) {
                return back()->with('error', 'Este expediente no tiene ningún documento adjunto.');
            }

            $rutaArchivo = $documento->ruta_almacenamiento;
            $rutaAbsoluta = storage_path('app/public/' . $rutaArchivo);

            if (!file_exists($rutaAbsoluta)) {
                return back()->with('error', 'El documento físico no se encuentra en el servidor. Revisa que el PDF realmente exista en tu carpeta storage.');
            }

            $nombreDescarga = $documento->nombre_original ?? ('Expediente_' . $expediente->numero_radicacion . '.pdf');

            return response()->download($rutaAbsoluta, $nombreDescarga);
        } catch (Exception $e) {
            return back()->with('error', 'Ocurrió un error al intentar descargar: ' . $e->getMessage());
        }
    }
}
