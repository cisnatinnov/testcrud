<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MahasiswaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/mahasiswa', MahasiswaController::class . '@lists')->name('mahasiswa.lists');
Route::get('/mahasiswa/{id}', MahasiswaController::class . '@show')->name('mahasiswa.show');
Route::post('/mahasiswa', MahasiswaController::class . '@store')->name('mahasiswa.store');
Route::put('/mahasiswa/{id}', MahasiswaController::class . '@update')->name('mahasiswa.update');
Route::delete('/mahasiswa/{id}', MahasiswaController::class . '@destroy')->name('mahasiswa.destroy');
