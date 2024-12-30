<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;


// Route::post('/proseslog', [AuthController::class, 'proseslog']);

Route::middleware(['guest:karyawan'])->group(function() {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login'); 
    Route::post('/proseslog', [AuthController::class, 'login']);
});

Route::middleware(['auth:karyawan'])->group(function() {
    Route::get('/dashboard',[DashboardController::class, 'index']);
    Route::get('/logout', [AuthController::class, 'proseslogout']);

    // presensi
    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);

    // edit profile
    Route::get('/editprofile', [PresensiController::class,'editprofile']);
    Route::post('/presensi/{nik}/updateprofile', [PresensiController::class,'updateprofile']);

    // history
    Route::get('/presensi/history', [PresensiController::class,'history']);
    Route::post('/gethistory', [PresensiController::class,'gethistory']);

    // izin
    Route::get('/presensi/izin', [PresensiController::class,'izin']);
    Route::get('/presensi/utkizin', [PresensiController::class,'utkizin']);
    Route::post('/presensi/storizin', [PresensiController::class,'storizin']);
});

Route::middleware(['guest:user'])->group(function() {
    Route::get('/admin', function () {
        return view('auth.loginadmin');
    })->name('loginadmin'); 
    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

Route::middleware(['auth:user'])->group(function() {
    Route::get('/admin/dashboardadmin', [DashboardController::class, 'dashboardadmin']);
    Route::get('/logoutadmin', [AuthController::class, 'proseslogoutadmin']);

    // karyawan
    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::post('/karyawan/store', [KaryawanController::class, 'store']);
    Route::post('/karyawan/edit', [KaryawanController::class, 'edit']);
    Route::post('/karyawan/{nik}/update', [KaryawanController::class, 'update']);
    Route::post('/karyawan/{nik}/delete', [KaryawanController::class, 'delete']);

    // presensi
    Route::get('/presensi/monitoring', [PresensiController::class, 'monitoring']);
    Route::post('/getpresensi', [PresensiController::class, 'getpresensi']);
    Route::get('/presensi/laporan', [PresensiController::class, 'laporan']);
    Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan']);
    Route::get('/presensi/izinsakit', [PresensiController::class, 'izinsakit']);
    Route::post('/presensi/accizinsakit', [PresensiController::class, 'accizinsakit']);
    Route::get('/presensi/{id}/cancelapprove', [PresensiController::class, 'cancelapprove']);
});

Route::redirect('/', '/login', 301);

