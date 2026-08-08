<?php

use App\Http\Controllers\{AdminController, AdminMediaController, AdminPageController, AdminPageCustomizerController, AdminPropertyController, AuthController, PageController, StaticFrontendController};
use Illuminate\Support\Facades\Route;

Route::get('/', [StaticFrontendController::class, 'home'])->name('home');
Route::get('/rent', [StaticFrontendController::class, 'rent'])->name('rent');
Route::get('/rent/london/{area}/{type?}', [StaticFrontendController::class, 'rent'])->name('rent.area');
Route::get('/buy', [StaticFrontendController::class, 'sale'])->name('buy');
Route::get('/buy/london/{area}/{type?}', [StaticFrontendController::class, 'sale'])->name('buy.area');
Route::get('/sell', fn () => redirect()->route('buy'))->name('sell');
Route::get('/commercial', [StaticFrontendController::class, 'commercial'])->name('commercial');
Route::get('/commercial/london/{area}/{rentPeriod?}', [StaticFrontendController::class, 'commercial'])->name('commercial.area');
Route::get('/properties', fn () => redirect()->route('rent'))->name('properties');
Route::get('/property/{slug}', [StaticFrontendController::class, 'property'])->name('property.show');
Route::get('/landlords', [PageController::class, 'landlords'])->name('landlords');
Route::get('/about-us', [StaticFrontendController::class, 'about'])->name('about');
Route::get('/contact', [StaticFrontendController::class, 'contact'])->name('contact');
Route::get('/login', [AuthController::class,'show'])->name('login');
Route::post('/login', [AuthController::class,'login'])->name('login.store');
Route::post('/logout', [AuthController::class,'logout'])->name('logout');
Route::prefix('dashboard')->name('admin.')->middleware(['auth','admin'])->group(function () {
    Route::get('/', [AdminController::class,'dashboard'])->name('dashboard');
    Route::resource('properties', AdminPropertyController::class)->except('show');
    Route::get('/media', [AdminMediaController::class,'index'])->name('media');
    Route::post('/media', [AdminMediaController::class,'store'])->name('media.store');
    Route::put('/media/{media}', [AdminMediaController::class,'update'])->name('media.update');
    Route::delete('/media/{media}', [AdminMediaController::class,'destroy'])->name('media.destroy');
    Route::get('/pages', [AdminPageController::class,'index'])->name('pages');
    Route::get('/pages/create', [AdminPageController::class,'create'])->name('pages.create');
    Route::post('/pages', [AdminPageController::class,'store'])->name('pages.store');
    Route::get('/pages/{page}/edit', [AdminPageController::class,'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [AdminPageController::class,'update'])->name('pages.update');
    Route::delete('/pages/{page}', [AdminPageController::class,'destroy'])->name('pages.destroy');
    Route::get('/pages/{page}/customizer', [AdminPageCustomizerController::class,'edit'])->name('pages.customizer');
    Route::get('/pages/{page}/customizer/schema', [AdminPageCustomizerController::class,'schema'])->name('pages.customizer.schema');
    Route::get('/pages/{page}/customizer/template', [AdminPageCustomizerController::class,'template'])->name('pages.customizer.template');
    Route::post('/pages/{page}/customizer/template', [AdminPageCustomizerController::class,'save'])->name('pages.customizer.save');
    Route::post('/pages/{page}/customizer/publish', [AdminPageCustomizerController::class,'publish'])->name('pages.customizer.publish');
    Route::get('/pages/{page}/customizer/revisions', [AdminPageCustomizerController::class,'revisions'])->name('pages.customizer.revisions');
    Route::post('/pages/{page}/customizer/revisions/restore', [AdminPageCustomizerController::class,'restore'])->name('pages.customizer.restore');
    Route::delete('/pages/{page}/customizer/draft', [AdminPageCustomizerController::class,'discard'])->name('pages.customizer.discard');
    Route::post('/pages/{page}/customizer/upload', [AdminPageCustomizerController::class,'upload'])->name('pages.customizer.upload');
    Route::get('/pages/{page}/customizer/preview', [AdminPageCustomizerController::class,'preview'])->name('pages.customizer.preview');
    Route::get('/settings', [AdminController::class,'settings'])->name('settings');
});
Route::get('/{page:slug}', [PageController::class, 'show'])->name('pages.show');
