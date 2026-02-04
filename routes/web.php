<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\WeaponController;
use App\Http\Controllers\ArmorController;
use App\Http\Controllers\CharmController;
use App\Http\Controllers\DecorationController;
use App\Http\Controllers\BuildEditorController;
use App\Http\Controllers\BuildApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Rutas públicas de tu proyecto
|--------------------------------------------------------------------------
*/

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

Route::get('/build-editor', [BuildEditorController::class, 'index'])->name('build.editor');

Route::view('/privacy', 'seccion.privacyPolicy')->name('privacy');
Route::view('/about', 'seccion.aboutUs')->name('about');
Route::view('/disclaimer', 'seccion.disclaimer')->name('disclaimer');
Route::view('/terms', 'seccion.termsOfUse')->name('terms');

/*
|--------------------------------------------------------------------------
| Rutas de autenticación (Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
