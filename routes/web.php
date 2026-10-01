<?php

use App\Http\Controllers\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ItemReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik (tidak perlu login)
|--------------------------------------------------------------------------
| Semua orang (termasuk tamu) boleh melihat & mencari daftar laporan.
*/
Route::get('/', [ItemReportController::class, 'index'])->name('home');
Route::get('/laporan', [ItemReportController::class, 'index'])->name('items.index');

/*
|--------------------------------------------------------------------------
| Autentikasi (guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Area user yang sudah login (siswa, guru, staf, admin)
|--------------------------------------------------------------------------
| PENTING: route statis seperti /laporan/buat HARUS didaftarkan SEBELUM
| route dinamis /laporan/{itemReport}, karena Laravel mencocokkan route
| sesuai urutan pendaftaran. Kalau dibalik, "/laporan/buat" akan tertangkap
| oleh {itemReport} dan dianggap mencari laporan dengan id "buat" (404).
*/
Route::middleware('auth')->group(function () {
    Route::get('/laporan/buat', [ItemReportController::class, 'create'])->name('items.create');
    Route::post('/laporan', [ItemReportController::class, 'store'])->name('items.store');
    Route::get('/laporan/{itemReport}/edit', [ItemReportController::class, 'edit'])->name('items.edit');
    Route::put('/laporan/{itemReport}', [ItemReportController::class, 'update'])->name('items.update');
    Route::delete('/laporan/{itemReport}', [ItemReportController::class, 'destroy'])->name('items.destroy');

    // Klaim (juga statis dulu baru dinamis, dengan alasan yang sama)
    Route::get('/klaim', [ClaimController::class, 'index'])->name('claims.index');
    Route::get('/laporan/{itemReport}/klaim', [ClaimController::class, 'create'])->name('claims.create');
    Route::post('/laporan/{itemReport}/klaim', [ClaimController::class, 'store'])->name('claims.store');
    Route::get('/klaim/{claim}', [ClaimController::class, 'show'])->name('claims.show');
    Route::patch('/klaim/{claim}/batal', [ClaimController::class, 'cancel'])->name('claims.cancel');
});

/*
|--------------------------------------------------------------------------
| Laporan detail (publik) - didaftarkan PALING TERAKHIR di antara rute
| "/laporan/..." supaya semua rute statis di atas ("buat", "{id}/edit",
| "{id}/klaim") sempat dicocokkan lebih dulu.
|--------------------------------------------------------------------------
*/
Route::get('/laporan/{itemReport}', [ItemReportController::class, 'show'])->name('items.show');

/*
|--------------------------------------------------------------------------
| Area admin
|--------------------------------------------------------------------------
| Middleware 'role:admin' memastikan hanya user dengan role admin yang
| bisa mengakses seluruh route di bawah prefix /admin.
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/laporan', [AdminReportController::class, 'index'])->name('reports.index');
        Route::patch('/laporan/{itemReport}/tutup', [AdminReportController::class, 'close'])->name('reports.close');

        Route::get('/klaim', [AdminClaimController::class, 'index'])->name('claims.index');
        Route::get('/klaim/{claim}', [AdminClaimController::class, 'show'])->name('claims.show');
        Route::patch('/klaim/{claim}/status', [AdminClaimController::class, 'updateStatus'])->name('claims.update-status');
        Route::post('/klaim/{claim}/serah-terima', [AdminClaimController::class, 'storeHandover'])->name('claims.handover');

        Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/pengguna/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.update-role');
    });
