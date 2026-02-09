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

Route::get('/guide-editor', [GuideEditorController::class, 'index']) ->name('guide.editor'); 
Route::post('/guide-editor/store', [GuideEditorController::class, 'store']) ->name('guide.editor.store'); 
Route::get('/contact', [ContactUsController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactUsController::class, 'store'])->name('contact.store');

Route::get('/guides', [GuideListController::class, 'index'])->name('guides.index');
Route::get('/guides/{slug}', [GuideListController::class, 'show'])->name('guides.show');

Route::post('/votar', [VoteController::class, 'votar'])
    ->middleware('auth')
    ->name('votar');

Route::post('/comments/store', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

Route::post('/save/{type}/{id}', [SavedItemController::class, 'toggle'])
    ->name('item.save')
    ->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Rutas de autenticación (Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
