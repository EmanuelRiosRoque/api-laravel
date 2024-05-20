<?php

use App\Http\Controllers\Api\AcusesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExhortoController;
use App\Http\Controllers\Api\materiasController;
use App\Http\Controllers\Auth\AuthController;

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

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ConsultarMaterias', [materiasController::class, 'index']);

    Route::post('/RecibirExhorto', [ExhortoController::class, 'requestExhorto']);
    Route::get('/RecibirExhortoResponse/{id}', [ExhortoController::class,'responseExhorto']);
    Route::get('/ConsultaExhorto/{folioSeguimiento}', [ExhortoController::class,'buscarExhortoPorFolioSeguimiento']);


    Route::post('/RecibirExhortoArchivoRequest', [ExhortoController::class,'requestExhortoArchivo']);
    Route::post('/AcuseRecibido', [AcusesController::class,'acuseRecibido']);
    Route::get('/ConsultaArchivoAcuse/{exhortoOrigenId}', [AcusesController::class, 'consultarArchivo']);


    Route::post('/RecibirRespuestaExhorto', [ExhortoController::class,'recibirRespuestaExhorto']);
    Route::post('/RecibirRespuestaExhortoArchivo', [ExhortoController::class,'recibirRespuestaExhortoArchivo']);


    Route::put('/ActualizarExhortoRequest', [ExhortoController::class, 'actualizarExhortoRequest']);
});



