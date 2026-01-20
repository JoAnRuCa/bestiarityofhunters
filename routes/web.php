<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SeccionController;
use App\Http\Controllers\SkillController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
{nombre?} variable que puede ser opcional hay que inicianizarla a null
{nombre} variable que existe siempre
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/skills', [SkillController::class, 'index'])->name('skills.index');
Route::get('/skills/{slug}', [SkillController::class, 'show'])->name('skills.show');
