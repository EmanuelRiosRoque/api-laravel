<?php

namespace App\Http\Controllers\Api;

use App\Models\Acuse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ArchivoARecibir;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\exhortoArchivos;
use Illuminate\Support\Facades\Validator;

class AcusesController extends Controller
{
    public function acuseRecibido(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'exhortoOrigenId' => 'required|uuid|exists:exhortos,exhortoOrigenId',
            'municipioAreaRecibeId' => 'nullable|exists:municipios,id',
            'areaRecibeId' => 'nullable|exists:areas,id',
            'areaRecibeNombre' => 'nullable|string',
            'urlInfo' => 'required|string',
        ])->stopOnFirstFailure();

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'La petición no se pudo realizar por datos incorrectos que se enviaron al servicio.',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        $errors = [];

        // Iniciar una transacción para asegurar la integridad de la base de datos
        DB::beginTransaction();

        try {
            $acuseData = $request->all();

            // Crear el acuse
            $acuse = Acuse::create([
                'exhortoOrigenId' => $acuseData['exhortoOrigenId'],
                'municipioAreaRecibeId' => $acuseData['municipioAreaRecibeId'],
                'areaRecibeId' => $acuseData['areaRecibeId'],
                'areaRecibeNombre' => $acuseData['areaRecibeNombre'],
                'urlInfo' => $acuseData['urlInfo'],
                'folioSeguimiento' => (string) Str::uuid(), // Generar UUID para folioSeguimiento
            ]);

            if (!$acuse) {
                $errors[] = "Error al crear el acuse: " . json_encode($acuseData);
                throw new \Exception("Error al crear el acuse");
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La operación se realizó exitosamente, el acuse se creó correctamente.',
                'errors' => null,
                'data' => [
                        "folioSeguimiento" => $acuse->folioSeguimiento,
                        "exhortoOrigenId" => $acuse->exhortoOrigenId,
                        "municipioAreaRecibeId" => $acuse->municipioAreaRecibeId,
                        "areaRecibeId" => $acuse->areaRecibeId,
                        "areaRecibeNombre" => $acuse->areaRecibeNombre,
                        "urlInfo" => $acuse->urlInfo,
                        "fechaHora" => $acuse->created_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            // Revertir la transacción si hay un error
            DB::rollBack();
            $errors[] = $e->getMessage();

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al crear el acuse.',
                'errors' => $errors,
                'data' => null
            ], 500);
        }
    }


    public function consultarArchivo(Request $request, $exhortoOrigenId)
    {
        // Validar el formato del UUID en la ruta
        $validator = Validator::make(['exhortoOrigenId' => $exhortoOrigenId], [
            'exhortoOrigenId' => 'required|uuid|exists:exhortos,exhortoOrigenId',
        ])->stopOnFirstFailure();

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'La petición no se pudo realizar por datos incorrectos que se enviaron al servicio.',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        try {
            // Buscar los acuses por exhortoOrigenId
            $acuses = Acuse::where('exhortoOrigenId', $exhortoOrigenId)->get();

            // Buscar los archivos por exhortoOrigenId
            $archivos = exhortoArchivos::where('exhortoOrigenId', $exhortoOrigenId)->get();

            // Si no hay acuses, devolver un mensaje específico
            if ($acuses->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sin acuse aún.',
                    'errors' => null,
                    'data' => [
                        'acuses' => 'Sin acuse aún',
                        'archivos' => $archivos,
                    ]
                ], 200);
            }

            // Si hay acuses, devolver la información completa
            return response()->json([
                'success' => true,
                'message' => 'Acuse(s) y archivo(s) encontrado(s).',
                'errors' => null,
                'data' => [
                    'acuses' => $acuses,
                    'archivos' => $archivos,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al buscar el acuse.',
                'errors' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

}
