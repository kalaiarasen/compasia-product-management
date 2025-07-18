<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\ProductMasterListController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/products', [ProductMasterListController::class, 'index']);
Route::post('/products/upload', [ProductMasterListController::class, 'upload']);
// routes/api.php
Route::get('/queue/status/{jobId}',[ProductMasterListController::class,'queueStatus']);
