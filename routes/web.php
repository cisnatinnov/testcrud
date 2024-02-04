<?php


use App\Http\Controllers\Controller;
use App\Http\Controllers\MahasiswaController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', Controller::class . '@home')->name('controller.home');
Route::get('/mahasiswa', MahasiswaController::class . '@index')->name('mahasiswa.index');