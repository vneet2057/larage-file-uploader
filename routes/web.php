<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/upload', [FileController::class, 'uploadChunk']);
Route::post('/upload/complete', [FileController::class, 'assembleFile']);