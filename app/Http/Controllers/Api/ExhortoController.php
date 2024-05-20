<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Acuse;
use App\Models\Partes;
use App\Models\Exhortos;
use Illuminate\Http\Request;
use App\Models\ArchivoARecibir;
use App\Models\exhortoArchivos;
use App\Models\RespuestaExhorto;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\RegistroActualizacion;
use App\Models\RespuestaExhortoArchivo;
use Illuminate\Support\Facades\Validator;

class ExhortoController extends Controller
{
    public function requestExhorto(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'municipioDestinoId' => 'required|exists:municipios,id',
            'materiaClave' => 'required|string',
            'estadoOrigenId' => 'required|exists:estados,id',
            'municipioOrigenId' => 'required|exists:municipios,id',
            'juzgadoOrigenId' => 'nullable|exists:juzgados,id',
            'juzgadoOrigenNombre' => 'required|string',
            'numeroExpedienteOrigen' => 'required|string',
            'numeroOficioOrigen' => 'nullable|string',
            'tipoJuicioAsuntoDelitos' => 'required|string',
            'juezExhortante' => 'nullable|string',
            'fojas' => 'required|integer',
            'diasResponder' => 'required|integer',
            'tipoDiligenciacionNombre' => 'nullable|string',
            'fechaOrigen' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'partes.*.nombre' => 'nullable|string',
            'partes.*.apellidoPaterno' => 'nullable|string',
            'partes.*.apellidoMaterno' => 'nullable|string',
            'partes.*.genero' => 'nullable|string',
            'partes.*.esPersonaMoral' => 'nullable|boolean',
            'partes.*.tipoParte' => 'nullable|integer',
            'partes.*.tipoParteNombre' => 'nullable|string',
            'archivos.*.nombreArchivo' => 'required|string',
            'archivos.*.hashSha1' => 'required|string',
            'archivos.*.hashSha256' => 'required|string',
            'archivos.*.tipoDocumento' => 'required|integer',
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
            // Crear el exhorto
            $exhortoData = $request->only([
                'municipioDestinoId',
                'materiaClave',
                'estadoOrigenId',
                'municipioOrigenId',
                'juzgadoOrigenId',
                'juzgadoOrigenNombre',
                'numeroExpedienteOrigen',
                'numeroOficioOrigen',
                'tipoJuicioAsuntoDelitos',
                'juezExhortante',
                'fojas',
                'diasResponder',
                'tipoDiligenciacionNombre',
                'fechaOrigen',
                'observaciones'
            ]);

            $exhorto = Exhortos::create($exhortoData);

            if (!$exhorto) {
                throw new \Exception("Error al crear el exhorto");
            }

            // Crear partes solo si existen en la solicitud
            if ($request->has('partes')) {
                foreach ($request->partes as $parteData) {
                    if (!empty($parteData['nombre']) || !empty($parteData['apellidoPaterno']) || !empty($parteData['apellidoMaterno'])) {
                        $parte = Partes::create([
                            'exhortoOrigenId' => $exhorto->exhortoOrigenId,
                            'nombre' => $parteData['nombre'],
                            'apellidoPaterno' => $parteData['apellidoPaterno'],
                            'apellidoMaterno' => $parteData['apellidoMaterno'],
                            'genero' => $parteData['genero'],
                            'esPersonaMoral' => $parteData['esPersonaMoral'],
                            'tipoParte' => $parteData['tipoParte'],
                            'tipoParteNombre' => $parteData['tipoParteNombre'],
                        ]);

                        if (!$parte) {
                            $errors[] = "Error al crear la parte: " . json_encode($parteData);
                        }
                    }
                }
            }

            // Crear archivos
            foreach ($request->archivos as $archivoData) {
                $archivo = ArchivoARecibir::create([
                    'exhortoOrigenId' => $exhorto->exhortoOrigenId,
                    'nombreArchivo' => $archivoData['nombreArchivo'],
                    'hashSha1' => $archivoData['hashSha1'],
                    'hashSha256' => $archivoData['hashSha256'],
                    'tipoDocumento' => $archivoData['tipoDocumento'],
                ]);

                if (!$archivo) {
                    $errors[] = "Error al crear el archivo: " . json_encode($archivoData);
                }
            }

            // Si hay errores, revertir la transacción y devolver una respuesta con los errores
            if (!empty($errors)) {
                throw new \Exception("Hubo un problema al crear las partes o archivos.");
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La operación se realizó exitosamente, el exhorto se creó correctamente.',
                'errors' => null,
                'data' => [
                    "exhortoOrigenId" => $exhorto->exhortoOrigenId,
                    "fechaHora" => $exhorto->created_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            // Revertir la transacción si hay un error
            DB::rollBack();
            $errors[] = $e->getMessage();

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al crear el exhorto.',
                'errors' => $errors,
                'data' => null
            ], 500);
        }
    }
    public function responseExhorto($id)
    {
        try {
            $exhorto = Exhortos::with('partes', 'archivos')->find($id);

            if (!$exhorto) {
                $data = [
                    'success' => false,
                    'message' => 'El recurso que se quiere obtener no existe.',
                    'errors' => 'No se encontró un exhorto con el ID proporcionado.',
                    'data' => null
                ];
                return response()->json($data, 404);
            }

            $data = [
                'success' => true,
                'message' => 'La operación se realizó exitosamente, el flujo del proceso se realizó como se esperaba.',
                'errors' => '',
                'data' => ["exhorto" => $exhorto->toArray()]
            ];

            return response()->json($data, 200);
        } catch (\Exception $e) {
            $data = [
                'success' => false,
                'message' => 'Error interno del servidor.',
                'errors' => $e->getMessage(),
            ];
            return response()->json($data, 500);
        }
    }
    public function requestExhortoArchivo(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'exhortoOrigenId' => 'required|uuid', // Validar como UUID
            'archivo' => 'required|string',
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
            $archivoData = $request->all(); // Asumiendo que los datos del archivo vienen en la misma solicitud

            // Decodificar el archivo de base64
            $archivoDecodificado = base64_decode($archivoData['archivo']);
            $archivoTamaño = strlen($archivoDecodificado); // Obtener el tamaño en bytes

            // Crear el archivo
            $archivo = exhortoArchivos::create([
                'exhortoOrigenId' => $archivoData['exhortoOrigenId'],
                'archivo' => $archivoData['archivo'],
            ]);

            if (!$archivo) {
                $errors[] = "Error al crear el archivo: " . json_encode($archivoData);
                throw new \Exception("Error al crear el archivo");
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La operación se realizó exitosamente, el flujo del proceso se realizó como se esperaba.',
                'errors' => null,
                'data' => [
                    "ArchivoRecibido" => [
                        "nombreArchivo" => $archivo->archivo,
                        "tamaño" => $archivoTamaño,
                    ],
                ]
            ], 200);

        } catch (\Exception $e) {
            // Revertir la transacción si hay un error
            DB::rollBack();
            $errors[] = $e->getMessage();

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al crear el archivo.',
                'errors' => $errors,
                'data' => null
            ], 500);
        }
    }
    public function buscarExhortoPorFolioSeguimiento(Request $request, $folioSeguimiento)
    {
        // Validar el formato del UUID
        $validator = Validator::make(['folioSeguimiento' => $folioSeguimiento], [
            'folioSeguimiento' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'El formato del folio de seguimiento es incorrecto.',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        // Buscar el acuse por folioSeguimiento
        $acuse = Acuse::where('folioSeguimiento', $folioSeguimiento)->first();

        if (!$acuse) {
            return response()->json([
                'success' => false,
                'message' => 'Acuse no encontrado.',
                'errors' => null,
                'data' => null
            ], 404);
        }

        // Obtener la información relacionada del exhortoOrigenId, incluyendo archivos y partes
        $exhorto = Exhortos::with(['archivos', 'partes'])->where('exhortoOrigenId', $acuse->exhortoOrigenId)->first();

        if (!$exhorto) {
            return response()->json([
                'success' => false,
                'message' => 'Exhorto no encontrado.',
                'errors' => null,
                'data' => null
            ], 404);
        }

        // Devolver la información del acuse, el exhorto, los archivos y las partes
        return response()->json([
            'success' => true,
            'message' => 'Acuse y exhorto encontrados.',
            'errors' => null,
            'data' => [
                // 'Acuse' => $acuse,
                'Exhorto' => [
                    'exhortoOrigenId' => $exhorto->exhortoOrigenId,
                    'municipioDestinoId' => $exhorto->municipioDestinoId,
                    'materiaClave' => $exhorto->materiaClave,
                    'estadoOrigenId' => $exhorto->estadoOrigenId,
                    'municipioOrigenId' => $exhorto->municipioOrigenId,
                    'juzgadoOrigenId' => $exhorto->juzgadoOrigenId,
                    'juzgadoOrigenNombre' => $exhorto->juzgadoOrigenNombre,
                    'numeroExpedienteOrigen' => $exhorto->numeroExpedienteOrigen,
                    'numeroOficioOrigen' => $exhorto->numeroOficioOrigen,
                    'tipoJuicioAsuntoDelitos' => $exhorto->tipoJuicioAsuntoDelitos,
                    'juezExhortante' => $exhorto->juezExhortante,
                    'partes' => $exhorto->partes,
                    'fojas' => $exhorto->fojas,
                    'diasResponder' => $exhorto->diasResponder,
                    'tipoDiligenciacionNombre' => $exhorto->tipoDiligenciacionNombre,
                    'fechaOrigen' => $exhorto->fechaOrigen,
                    'observaciones' => $exhorto->observaciones,
                    'archivos' => $exhorto->archivos,
                    'created_at' => $exhorto->created_at,
                    'updated_at' => $exhorto->updated_at,
                ],
            ]
        ], 200);
    }
    public function recibirRespuestaExhorto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exhortoId' => 'required|uuid',
            'respuestaOrigenId' => 'required|uuid',
            'municipioTurnadoId' => 'required|integer',
            'areaTurnadoId' => 'nullable|string',
            'areaTurnadoNombre' => 'required|string',
            'numeroExhorto' => 'nullable|string',
            'tipoDiligenciado' => 'required|integer',
            'observaciones' => 'nullable|string',
            'archivos' => 'required|array',
            'archivos.*.nombreArchivo' => 'required|string',
            'archivos.*.hashSha1' => 'required|string',
            'archivos.*.hashSha256' => 'required|string',
            'archivos.*.tipoDocumento' => 'required|integer',
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
            $respuestaExhortoData = $request->only([
                'exhortoId',
                'respuestaOrigenId',
                'municipioTurnadoId',
                'areaTurnadoId',
                'areaTurnadoNombre',
                'numeroExhorto',
                'tipoDiligenciado',
                'observaciones'
            ]);

            // Crear la respuesta del exhorto
            $respuestaExhorto = RespuestaExhorto::create($respuestaExhortoData);

            if (!$respuestaExhorto) {
                throw new \Exception("Error al crear la respuesta del exhorto");
            }

            // Crear archivos
            foreach ($request->archivos as $archivoData) {
                $archivo = ArchivoARecibir::create([
                    'exhortoOrigenId' => $respuestaExhorto->exhortoId,
                    'nombreArchivo' => $archivoData['nombreArchivo'],
                    'hashSha1' => $archivoData['hashSha1'],
                    'hashSha256' => $archivoData['hashSha256'],
                    'tipoDocumento' => $archivoData['tipoDocumento'],
                ]);

                if (!$archivo) {
                    $errors[] = "Error al crear el archivo: " . json_encode($archivoData);
                }
            }

            // Si hay errores, revertir la transacción y devolver una respuesta con los errores
            if (!empty($errors)) {
                throw new \Exception("Hubo un problema al crear los archivos.");
            }

            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La operación se realizó exitosamente, la respuesta del exhorto se creó correctamente.',
                'errors' => null,
                'data' => [
                    'exhortoId' => $respuestaExhorto->exhortoId,
                    'respuestaExhortoId' => $respuestaExhorto->id,
                    'fechaHora' => $respuestaExhorto->created_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            // Revertir la transacción si hay un error
            DB::rollBack();
            $errors[] = $e->getMessage();

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al crear la respuesta del exhorto.',
                'errors' => $errors,
                'data' => null
            ], 500);
        }
    }
    public function recibirRespuestaExhortoArchivo(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'exhortoId' => 'required|uuid|exists:exhortos,exhortoOrigenId',
            'respuestaOrigenId' => 'required|uuid',
            'archivo' => 'required|string'
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
            $archivoData = $request->only(['exhortoId', 'respuestaOrigenId', 'archivo']);

            // Iniciar una transacción para asegurar la integridad de la base de datos
            DB::beginTransaction();

            //Insertar en la tabla respuesta exhorto archivos
            $resExhortoArchivo = RespuestaExhortoArchivo::create([
                'exhortoId' => $archivoData['exhortoId'],
                'respuestaOrigenId' => $archivoData['respuestaOrigenId']
            ]);

            // Crear el registro de archivo en la tabla archivo_a_recibirs
            $archivoARecibir = exhortoArchivos::create([
                'exhortoOrigenId' => $archivoData['exhortoId'],
                'archivo' => $archivoData['archivo'],
            ]);

            if (!$resExhortoArchivo) {
                throw new \Exception("Error al crear el archivo de respuesta del exhorto archivo");
            }
            if (!$archivoARecibir) {
                throw new \Exception("Error al crear el archivo de respuesta del exhorto");
            }


            // Confirmar la transacción
            DB::commit();

            // Consultar la tabla archivo_a_recibirs para traer los registros que coincidan con exhortoId
            $exhortosArchivos = exhortoArchivos::where('exhortoOrigenId', $archivoData['exhortoId'])->get();

            // Consultar la tabla acuses para traer los registros que coincidan con exhortoId
            $acuses = Acuse::where('exhortoOrigenId', $archivoData['exhortoId'])->get();

            $acuseData = $acuses->isEmpty() ? 'Sin acuses' : $acuses;

            return response()->json([
                'success' => true,
                'message' => 'La operación se realizó exitosamente, el archivo de respuesta del exhorto se creó correctamente.',
                'errors' => null,
                'data' => [
                    'archivoARecibirId' => $archivoARecibir->id,
                    'fechaHora' => $archivoARecibir->created_at,
                    'exhortosArchivos' => $exhortosArchivos,
                    'acuses' => $acuseData,
                ]
            ], 200);

        } catch (\Exception $e) {
            // Revertir la transacción si hay un error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al crear el archivo de respuesta del exhorto.',
                'errors' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    public function actualizarExhortoRequest(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'exhortoId' => 'required|uuid|exists:exhortos,exhortoOrigenId',
            'actualizacionOrigenId' => 'required|uuid',
            'tipoActualizacion' => 'required|string',
            'fechaHora' => 'required|date',
            'descripcion' => 'required|string',
            'areaTurnadoNombre' => 'nullable|string',
            'numeroExhorto' => 'nullable|string'
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
            // Iniciar una transacción para asegurar la integridad de la base de datos
            DB::beginTransaction();


            // Buscar el registro en la tabla respuesta_exhortos
            $respuestaExhorto = RespuestaExhorto::where('exhortoId', $request->input('exhortoId'))->first();


            if (!$respuestaExhorto) {
                throw new \Exception("Respuesta del exhorto no encontrada");
            }

            // Actualizar los campos si están presentes en la solicitud
            if ($request->has('areaTurnadoNombre')) {
                $respuestaExhorto->areaTurnadoNombre = $request->input('areaTurnadoNombre');
            }

            if ($request->has('numeroExhorto')) {
                $respuestaExhorto->numeroExhorto = $request->input('numeroExhorto');
            }

            // Guardar los cambios
            $respuestaExhorto->save();

            // Formatear la fecha correctamente para MySQL
            $fechaHora = Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $request->input('fechaHora'))->toDateTimeString();

            // Registrar la actualización
            $registroActualizacion = RegistroActualizacion::create([
                'exhortoId'=> $request->input('exhortoId'),
                'actualizacionOrigenId'=> $request->input('actualizacionOrigenId'),
                'tipoActualizacion'=> $request->input('tipoActualizacion'),
                'fechaHora'=> $fechaHora,
                'descripcion'=> $request->input('descripcion'),
            ]);

            if (!$registroActualizacion) {
                throw new \Exception("Hubo un problema al registrar la actualizacion");
            }


            // Confirmar la transacción
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La operación se realizó exitosamente, la respuesta del exhorto se actualizó correctamente.',
                'errors' => null,
                'data' => [
                    'exhortoId' => $respuestaExhorto->exhortoId,
                    'actualizacionOrigenId' => $registroActualizacion->actualizacionOrigenId,
                    'fechaHora' => $respuestaExhorto->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            // Revertir la transacción si hay un error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema al actualizar la respuesta del exhorto.',
                'errors' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

}
