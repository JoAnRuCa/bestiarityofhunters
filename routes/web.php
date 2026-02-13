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
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\GuideEditorController;
use App\Http\Controllers\GuideListController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SavedItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BuildListController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Rutas públicas de Enciclopedia
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

/*
|--------------------------------------------------------------------------
| Listados y Visualización (Público)
|--------------------------------------------------------------------------
*/

Route::get('/guides', [GuideListController::class, 'index'])->name('guides.index');
Route::get('/guides/{slug}', [GuideListController::class, 'show'])->name('guides.show');

Route::get('/builds', [BuildListController::class, 'index'])->name('builds.index');
Route::get('/builds/{slug}', [BuildListController::class, 'show'])->name('builds.show');

/*
|--------------------------------------------------------------------------
| Build Editor API & Store
|--------------------------------------------------------------------------
*/

Route::get('/build-editor', [BuildEditorController::class, 'index'])->name('build.editor');
Route::get('/api/build-data', [BuildApiController::class, 'getBuildData']);
Route::post('/save-build', [BuildEditorController::class, 'store'])->name('builds.store');

/*
|--------------------------------------------------------------------------
| Páginas Estáticas y Contacto
|--------------------------------------------------------------------------
*/

Route::view('/privacy', 'seccion.privacyPolicy')->name('privacy');
Route::view('/about', 'seccion.aboutUs')->name('about');
Route::view('/disclaimer', 'seccion.disclaimer')->name('disclaimer');
Route::view('/terms', 'seccion.termsOfUse')->name('terms');

Route::get('/contact', [ContactUsController::class, 'index'])->name('contact');
Route::post('/contact', [ContactUsController::class, 'store'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Login)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Votos y Comentarios
    Route::post('/votar', [VoteController::class, 'votar'])->name('votar');
    Route::post('/comments/store', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('/comments/{id}/delete', [CommentController::class, 'softDelete'])->name('comments.soft-delete');
    Route::put('/comments/{id}/update', [CommentController::class, 'update'])->name('comments.update');

    // Gestión de Perfil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Guardados (Favoritos)
    Route::get('/saved-guides', [SavedItemController::class, 'indexGuides'])->name('saved.guides');
    Route::get('/saved-builds', [SavedItemController::class, 'indexBuilds'])->name('saved.builds');
    Route::post('/saved/toggle/{type}/{id}', [SavedItemController::class, 'toggle'])->name('saved.toggle');

    // --- MI LIBRERÍA (BUILD MANAGEMENT) ---
    Route::get('/my-builds', [BuildListController::class, 'myBuilds'])->name('my.builds');
    Route::get('/build-editor/{slug}', [BuildEditorController::class, 'show'])->name('build-editor.show');
    
    // AQUÍ ESTÁN LAS RUTAS QUE TE FALTABAN PARA LAS BUILDS:
    Route::get('/builds/{slug}/edit', [BuildListController::class, 'edit'])->name('builds.edit');
    Route::put('/builds/{slug}', [BuildListController::class, 'update'])->name('builds.update');
    Route::delete('/builds/{slug}', [BuildListController::class, 'destroy'])->name('builds.destroy');

    // --- MI LIBRERÍA (GUIDE MANAGEMENT) ---
    Route::get('/my-guides', [GuideListController::class, 'myGuides'])->name('my.guides');
    Route::get('/guide-editor', [GuideEditorController::class, 'index'])->name('guide.editor');
    Route::post('/guide-editor/store', [GuideEditorController::class, 'store'])->name('guide.editor.store');
    Route::get('/guides/{slug}/edit', [GuideListController::class, 'edit'])->name('guides.edit');
    Route::put('/guides/{slug}', [GuideListController::class, 'update'])->name('guides.update');
    Route::delete('/guides/{slug}', [GuideListController::class, 'destroy'])->name('guides.destroy');

});

require __DIR__ . '/auth.php';