<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Materias;
use Illuminate\Http\Request;

class materiasController extends Controller
{
    public function index()
    {
        $materias = Materias::all();

        if ($materias->isEmpty()) {
            $data = [
                'success' => false,
                'message' => 'El recurso que se quiere obtener no existe.',
                'errors' => 'No se encontraron materias en la base de datos.',
                'data' => null
            ];
            return response()->json($data, 404);
        }

        $data = [
            'success' => true,
            'message' => 'La operación se realizó exitosamente, el flujo del proceso se realizó como se esperaba.',
            'errors' => '',
            'data' => ["materias" => $materias->toArray()]
        ];

        return response()->json($data, 200);
    }




}
