<?php

namespace App\Http\Controllers\Api;

use App\Models\Empleados;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class empleadosController extends Controller
{
    public function index()
    {
        $empleados = Empleados::all();

        dd($empleados);
        exit;
        if ($empleados->isEmpty()) {
            $data = [
                'message' => 'No se encontraron empleados',
                'status' => 404
            ];
            return response()->json($data, 404);
        }


        return response()->json($empleados, 200);
    }
}
