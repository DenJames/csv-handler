<?php

use App\Http\Controllers\CsvController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/import', [CsvController::class, 'import']);
Route::post('/export', [CsvController::class, 'export']);
