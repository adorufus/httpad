<?php

use App\Http\Controllers\ApiRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::post('/send-request', [ApiRequestController::class, 'sendRequest']);
    Route::post('/save-request', [ApiRequestController::class, 'saveRequest']);
    Route::get('/requests', [ApiRequestController::class, 'getRequests']);
});
