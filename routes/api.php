<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\MapelController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);

// GET (public)
Route::apiResource('/users', UserController::class)->only(['index', 'show']);
Route::apiResource('/guru', GuruController::class)->only(['index', 'show']);
Route::apiResource('/mapel', MapelController::class)->only(['index', 'show']);
Route::apiResource('kelas', KelasController::class)
    ->parameters(['kelas' => 'kelas'])
    ->only(['index', 'show']);
Route::apiResource('/siswa', SiswaController::class)->only(['index', 'show']);
Route::apiResource('/jadwal', JadwalController::class)->only(['index', 'show']);

Route::get('/guru/{guru}/jadwal', [GuruController::class, 'jadwal']);
Route::get('/kelas/{kelas}/siswa', [KelasController::class, 'siswa']);
Route::get('/kelas/{kelas}/jadwal', [KelasController::class, 'jadwal']);
Route::get('/mapel/{mapel}/jadwal', [MapelController::class, 'jadwal']);

// PROTECTED 
Route::middleware('auth:api')->group(function () {
    Route::get('cek-token', function () {
        return response()->json([
            'status' => true,
            'message' => 'Token valid.',
            'data' => auth()->user()
        ]);
    });
    Route::apiResource('/users', UserController::class)->except(['index', 'show']);
    Route::apiResource('/guru', GuruController::class)->except(['index', 'show']);
    Route::apiResource('/mapel', MapelController::class)->except(['index', 'show']);
    Route::apiResource('kelas', KelasController::class)
        ->parameters(['kelas' => 'kelas'])
        ->except(['index', 'show']);
    Route::apiResource('/siswa', SiswaController::class)->except(['index', 'show']);
    Route::apiResource('/jadwal', JadwalController::class)->except(['index', 'show']);
});