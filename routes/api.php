<?php

use App\Http\Controllers\Api\AcusesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExhortoController;
use App\Http\Controllers\Api\materiasController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/ConsultarMaterias', [materiasController::class, 'index']);

Route::post('/RecibirExhorto', [ExhortoController::class, 'requestExhorto']);
Route::get('/RecibirExhortoResponse/{id}', [ExhortoController::class,'responseExhorto']);
Route::get('/ConsultaExhorto/{id}', [ExhortoController::class,'buscarAcusePorFolioSeguimiento']);


Route::post('/RecibirExhortoArchivoRequest', [ExhortoController::class,'requestExhortoArchivo']);
Route::post('/AcuseRecibido', [AcusesController::class,'acuseRecibido']);
Route::get('/ConsultaArchivoAcuse/{exhortoOrigenId}', [AcusesController::class, 'consultarArchivo']);


Route::post('/RecibirRespuestaExhorto', [ExhortoController::class,'recibirRespuestaExhorto']);



Route::post('/empleados', function() {
    return 'Creando empleado';
});

Route::put('/empleados/{id}', function() {
    return 'Actualizando empleado';
});

Route::delete('/empleados/{id}', function() {
    return 'Eliminando empleado';
});
