<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DailyactivityController;
use App\Http\Controllers\ActivitiesController;
use App\Http\Controllers\LicensedappController;
use App\Http\Controllers\Exports_CKP;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\test;
use App\Http\Controllers\SuggestController;
use App\Http\Controllers\TempController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\KegiatanutamaController;
use App\Http\Controllers\AssigntimController;
use App\Http\Controllers\TimkerjaController;
use App\Http\Controllers\NotificationController;
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


Route::post('act/storebyteam', [ActivitiesController::class, 'storebyteam'])->name('act.storebyteam');
Route::get('act/createdbyteam', [ActivitiesController::class, 'createdbyteam'])->name('act.createdbyteam');
Route::get('act/indexkhi/export-to-excel/{bulan}/{tahun}', [ActivitiesController::class, 'indexkhiexportToExcel'])->name('indeks.khi.export.excel');
Route::get('act/filterMonthYear', [ActivitiesController::class, 'filterMonthYear'])->name('act.filterMonthYear');
Route::get('act/selftable', [ActivitiesController::class, 'selftable'])->name('act.selftable');
Route::get('act/monitoring', [ActivitiesController::class, 'monitoring'])->name('act.monitoring');
Route::get('act/allactivity', [ActivitiesController::class, 'allActivity'])->name('act.allactivity');
Route::get('act/filterMonthYear2', [ActivitiesController::class, 'filterMonthYear2'])->name('act.filterMonthYear2');
Route::get('act/{id}/duplicate', [ActivitiesController::class, 'duplicate'])->name('act.duplicate');
Route::resource('act', ActivitiesController::class);


Route::get('/api/active-notifications', [NotificationController::class, 'getActiveNotifications'])->name('notifications.active');


//soon to be deleted
Route::get('temp/getKegiatanutama/{project_id}', [TempController::class, 'getKegiatanutama'])->name('temp.getKegiatanutama');
Route::get('temp/getProject/{tim_kerja_id}', [TempController::class, 'getProject'])->name('temp.getProject');
Route::resource('temp', TempController::class);
//end of soon to be deleted

Route::resource('timkerja', TimkerjaController::class);


Route::get('assigntim/export-alokasi-tim', [AssigntimController::class, 'exportToExcel'])->name('assigntim.export.excel');
Route::get('assigntim/getKegiatanutama/{project_id}', [AssigntimController::class, 'getKegiatanutama'])->name('kegiatanutama.getKegiatanutama');
Route::resource('assigntim', AssigntimController::class);

Route::resource('project', ProjectController::class);

Route::get('kegiatanutama/getProject/{tim_kerja_id}', [KegiatanutamaController::class, 'getProject'])->name('kegiatanutama.getProject');
Route::resource('kegiatanutama', KegiatanutamaController::class);

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('generateDocx', [ActivitiesController::class, 'generateDocx'])->name('act.generateDocx');

Route::get('export-to-excel/{tahun}/{bulan}', [Exports_CKP::class, 'exportToExcel'])->name('export.activities');

// Route::get('test', [test::class,'index']);

Route::resource('licensedapp', LicensedappController::class);

//Suggestion
Route::get('/autocomplete/search', [SuggestController::class, 'search'])->name('autocomplete.search');

Route::resource('notif', NotificationController::class);


