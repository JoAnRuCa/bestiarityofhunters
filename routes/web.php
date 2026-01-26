<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\WeaponController;
use App\Http\Controllers\ArmorController;
use App\Http\Controllers\CharmController;
use App\Http\Controllers\DecorationController;
use App\Http\Controllers\BuildEditorController;


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

Route::get('/weapons', [WeaponController::class, 'index'])->name('weapons.index');
Route::get('/weapons/{slug}', [WeaponController::class, 'show'])->name('weapons.show');

Route::get('/armors', [ArmorController::class, 'index'])->name('armors.index');
Route::get('/armors/{slug}', [ArmorController::class, 'show'])->name('armors.show');

Route::get('/charms', [CharmController::class, 'index'])->name('charms.index');
Route::get('/charms/{slug}', [CharmController::class, 'show'])->name('charms.show');

Route::get('/decorations', [DecorationController::class, 'index'])->name('decorations.index');
Route::get('/decorations/{slug}', [DecorationController::class, 'show'])->name('decorations.show');

Route::get('/build-editor', [BuildEditorController::class, 'index']);