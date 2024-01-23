<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DailyactivityController;
use App\Http\Controllers\ActivitiesController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
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

Route::get('/', [ActivitiesController::class, 'index']);

Route::get('/logout', [AuthenticatedSessionController::class, 'destroy']);

Route::get('act/filterMonthYear', [ActivitiesController::class, 'filterMonthYear'])->name('act.filterMonthYear');
Route::get('act/selftable', [ActivitiesController::class, 'selftable'])->name('act.selftable');
Route::resource('act', ActivitiesController::class);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('generateDocx', [ActivitiesController::class, 'generateDocx'])->name('act.generateDocx');