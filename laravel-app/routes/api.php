<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AgeController;
use App\Http\Controllers\DistanceController;
use App\Http\Controllers\ShowSerialPasoController;
use App\Http\Controllers\StudentController;
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

Route::prefix('accounts')->group(function () {
    Route::get('/', [AccountController::class, 'index']); // Lấy danh sách
    Route::get('/{id}', [AccountController::class, 'show']); // Lấy một bản ghi
    Route::post('/', [AccountController::class, 'store']); // Tạo mới
    Route::put('/{id}', [AccountController::class, 'update']); // Cập nhật
    Route::delete('/{id}', [AccountController::class, 'destroy']); // Xóa
});

Route::get('/ageDifference', [AgeController::class, 'calcAgeDifference']);

Route::get('/getDistanceFurthestAway', [DistanceController::class, 'getDistanceFurthestAway']);

Route::get('/getStudentOutOfAverageAge', [StudentController::class, 'getStudentOutOfAverageAge']);

Route::post('/showSerialPaso', [ShowSerialPasoController::class, 'showSerialPaso']);