<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnusController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome.welcome');
});


Route::get('/alumni/import/create', [AlumnusController::class, 'import_create'])->name('alumni.import.create')->middleware('auth');
Route::post('/alumni/import', [AlumnusController::class, 'import_store'])->name('alumni.import.store')->middleware('auth');
Route::post('/alumni/{alumnus}/accept', [AlumnusController::class, 'accept'])->name('alumni.accept')->middleware('auth');
Route::post('/alumni/{alumnus}/reject', [AlumnusController::class, 'reject'])->name('alumni.reject')->middleware('auth');

Route::get('/alumni/search', [AlumnusController::class, 'searchAlumni'])->name('alumni.search');

Route::resource('alumni', AlumnusController::class);

// -----------------------------------------

Auth::routes(['register' => false, 'reset' => false]);


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
