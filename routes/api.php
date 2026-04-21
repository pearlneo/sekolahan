<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\MapelController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\UserController;

Route::apiResource('/users', UserController::class);
Route::apiResource('/guru', GuruController::class);
Route::apiResource('/mapel', MapelController::class);
Route::apiResource('kelas', KelasController::class)
    ->parameters(['kelas' => 'kelas']);
Route::apiResource('/siswa', SiswaController::class);
Route::apiResource('/jadwal', JadwalController::class); 


Route::get('/guru/{guru}/jadwal', [GuruController::class, 'jadwal']);
Route::get('/kelas/{kelas}/siswa', [KelasController::class, 'siswa']);
Route::get('/kelas/{kelas}/jadwal', [KelasController::class, 'jadwal']);
Route::get('/mapel/{mapel}/jadwal', [MapelController::class, 'jadwal']);