<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\MarginPenjualanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ReturController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::middleware('auth')->group(function (): void {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('role:administrator,cashier,warehouse')->group(function (): void {
        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
        Route::get('/barang/{id}', [BarangController::class, 'show'])
            ->whereNumber('id')
            ->name('barang.show');
        Route::get('/satuan', [SatuanController::class, 'index'])->name('satuan.index');
        Route::get('/satuan/{id}', [SatuanController::class, 'show'])
            ->whereNumber('id')
            ->name('satuan.show');
    });

    Route::middleware('role:administrator')->group(function (): void {
        Route::get('/barang/tambah', [BarangController::class, 'add'])->name('barang.create');
        Route::post('/barang/tambah', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/vendor/tambah', [VendorController::class, 'create'])->name('vendor.create');
        Route::post('/vendor', [VendorController::class, 'store'])->name('vendor.store');
        Route::get('/margin/tambah', [MarginPenjualanController::class, 'create'])->name('margin.create');
        Route::post('/margin', [MarginPenjualanController::class, 'store'])->name('margin.store');
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
        Route::get('/role', [RoleController::class, 'index'])->name('role.index');
        Route::get('/role/tambah', [RoleController::class, 'create'])->name('role.create');
        Route::post('/role', [RoleController::class, 'store'])->name('role.store');
        Route::get('/role/{id}', [RoleController::class, 'show'])
            ->whereNumber('id')
            ->name('role.show');

        Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])
            ->whereNumber('id')
            ->name('barang.edit');
        Route::put('/barang/{id}', [BarangController::class, 'update'])
            ->whereNumber('id')
            ->name('barang.update');
        Route::delete('/barang/{id}', [BarangController::class, 'destroy'])
            ->whereNumber('id')
            ->name('barang.destroy');

        Route::get('/satuan/{id}/edit', [SatuanController::class, 'edit'])
            ->whereNumber('id')
            ->name('satuan.edit');
        Route::put('/satuan/{id}', [SatuanController::class, 'update'])
            ->whereNumber('id')
            ->name('satuan.update');
        Route::delete('/satuan/{id}', [SatuanController::class, 'destroy'])
            ->whereNumber('id')
            ->name('satuan.destroy');

        Route::get('/vendor/{id}/edit', [VendorController::class, 'edit'])
            ->whereNumber('id')
            ->name('vendor.edit');
        Route::put('/vendor/{id}', [VendorController::class, 'update'])
            ->whereNumber('id')
            ->name('vendor.update');
        Route::delete('/vendor/{id}', [VendorController::class, 'destroy'])
            ->whereNumber('id')
            ->name('vendor.destroy');

        Route::get('/margin/{id}/edit', [MarginPenjualanController::class, 'edit'])
            ->whereNumber('id')
            ->name('margin.edit');
        Route::put('/margin/{id}', [MarginPenjualanController::class, 'update'])
            ->whereNumber('id')
            ->name('margin.update');
        Route::delete('/margin/{id}', [MarginPenjualanController::class, 'destroy'])
            ->whereNumber('id')
            ->name('margin.destroy');

        Route::get('/user/{id}/edit', [UserController::class, 'edit'])
            ->whereNumber('id')
            ->name('user.edit');
        Route::put('/user/{id}', [UserController::class, 'update'])
            ->whereNumber('id')
            ->name('user.update');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])
            ->whereNumber('id')
            ->name('user.destroy');

        Route::get('/role/{id}/edit', [RoleController::class, 'edit'])
            ->whereNumber('id')
            ->name('role.edit');
        Route::put('/role/{id}', [RoleController::class, 'update'])
            ->whereNumber('id')
            ->name('role.update');
        Route::delete('/role/{id}', [RoleController::class, 'destroy'])
            ->whereNumber('id')
            ->name('role.destroy');

        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    });

    Route::middleware('role:administrator,warehouse')->group(function (): void {
        Route::get('/vendor', [VendorController::class, 'index'])->name('vendor.index');

        Route::get('/pengadaan', [PengadaanController::class, 'index'])->name('pengadaan.index');
        Route::get('/pengadaan/create', [PengadaanController::class, 'create'])->name('pengadaan.create');
        Route::post('/pengadaan', [PengadaanController::class, 'store'])->name('pengadaan.store');
        Route::get('/pengadaan/{id}', [PengadaanController::class, 'show'])
            ->whereNumber('id')
            ->name('pengadaan.show');

        Route::get('/penerimaan', [PenerimaanController::class, 'index'])->name('penerimaan.index');
        Route::get('/penerimaan/riwayat', [PenerimaanController::class, 'riwayat'])
            ->name('penerimaan.riwayat');
        Route::get('/penerimaan/riwayat/{id}', [PenerimaanController::class, 'detailRiwayat'])
            ->whereNumber('id')
            ->name('penerimaan.riwayat.detail');
        Route::get('/penerimaan/{id}/create', [PenerimaanController::class, 'create'])
            ->name('penerimaan.create');
        Route::post('/penerimaan/{id}', [PenerimaanController::class, 'store'])
            ->name('penerimaan.store');

        Route::get('/retur', [ReturController::class, 'index'])
            ->name('retur.index');

        Route::get('/retur/create', [ReturController::class, 'create'])
            ->name('retur.create');

        Route::post('/retur', [ReturController::class, 'store'])
            ->name('retur.store');

        Route::get('/retur/riwayat/{id}', [ReturController::class, 'show'])
            ->whereNumber('id')
            ->name('retur.show');
    });

    Route::middleware('role:administrator,cashier')->group(function (): void {
        Route::get('/margin', [MarginPenjualanController::class, 'index'])->name('margin.index');
        Route::get('/margin/{id}', [MarginPenjualanController::class, 'show'])
            ->whereNumber('id')
            ->name('margin.show');

        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');
        Route::post('/penjualan', [PenjualanController::class, 'store'])->name('penjualan.store');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])
            ->whereNumber('id')
            ->name('penjualan.show');
    });
});
