<?php

use App\Http\Controllers\AnalyseController;
use App\Http\Controllers\OrdonnanceController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("/ordonnance/{id}",[OrdonnanceController::class,"imprimer"]);
Route::get("/analyse/{id}",[AnalyseController::class,"imprimer"]);
Route::get("/cnss/{id}",[ConsultationController::class,"cnss"]);
Route::get("/certificat/{type}/{id}/{docteur}",[PrintController::class,"certificat"]);


