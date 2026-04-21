<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// RUTAS PÚBLICAS: Cualquiera puede ver los datos para el editor
Route::get('/build-data', [BuildApiController::class, 'getBuildData']);
Route::get('/items/{slot}', [BuildApiController::class, 'getItemsBySlot']);

// RUTAS PROTEGIDAS: Solo usuarios logueados
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/save-build', [BuildApiController::class, 'saveBuild']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});