<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BuildApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/build-data', [BuildApiController::class, 'getBuildData']);
Route::get('/items/{slot}', [BuildApiController::class, 'getItemsBySlot']);
Route::post('/save-build', [BuildApiController::class, 'saveBuild']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
