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
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\BuildController;

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
    Route::put('/guides/{guide}', [GuideListController::class, 'update'])->name('guides.update');
    Route::get('/guides/{guide}/edit', [GuideListController::class, 'edit'])->name('guides.edit');
    Route::delete('/guides/{guide}', [GuideListController::class, 'destroy'])->name('guides.destroy');

});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/guides', [GuideController::class, 'index'])->name('admin.guides.index');
    Route::get('/guides/create', [GuideController::class, 'create'])->name('admin.guides.create');
    Route::post('/guides', [GuideController::class, 'store'])->name('admin.guides.store');
    Route::get('/guides/{guide}/edit', [GuideController::class, 'edit'])->name('admin.guides.edit');
    Route::put('/guides/{guide}', [GuideController::class, 'update'])->name('admin.guides.update');
    Route::delete('/guides/{guide}', [GuideController::class, 'destroy'])->name('admin.guides.destroy');

    Route::get('/builds', [BuildController::class, 'index'])->name('admin.builds.index');
    Route::get('/builds/create', [BuildController::class, 'create'])->name('admin.builds.create');
    Route::post('/builds', [BuildController::class, 'store'])->name('admin.builds.store');
    Route::get('/builds/{build}/edit', [BuildController::class, 'edit'])->name('admin.builds.edit');
    Route::put('/builds/{build}', [BuildController::class, 'update'])->name('admin.builds.update');
    Route::delete('/builds/{build}', [BuildController::class, 'destroy'])->name('admin.builds.destroy');

    Route::get('/tags', [TagController::class, 'index'])->name('admin.tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('admin.tags.store');
    Route::get('/tags/{tag}/edit', [TagController::class, 'edit'])->name('admin.tags.edit');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('admin.tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('admin.tags.destroy');
});

require __DIR__ . '/auth.php';