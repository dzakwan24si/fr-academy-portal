<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\Administrasi\VraController;
use App\Http\Controllers\Administrasi\BiayaRekruitController;
use App\Http\Controllers\Administrasi\CapexController;
use App\Http\Controllers\Administrasi\CostController;
use App\Http\Controllers\Administrasi\CutiController;
use App\Http\Controllers\Administrasi\MppController;

Route::get('/', function () {
    return redirect()->route('kalender-training.index');
})->name('home');

Route::get('/administrasi/kalender-training', [ProgramController::class, 'index'])->name('kalender-training.index');
Route::get('/dashboard', [ProgramController::class, 'dashboard'])->name('dashboard');

// Administrasi - MPP Jabatan Routes
Route::prefix('administrasi/mpp')->name('administrasi.mpp.')->group(function () {
    Route::get('/', [MppController::class, 'index'])->name('index');
    Route::post('/', [MppController::class, 'store'])->name('store');
    Route::post('/year', [MppController::class, 'storeYear'])->name('year');
    Route::get('/export/report', [MppController::class, 'export'])->name('export');
    Route::get('/get-by-jabatan', [MppController::class, 'showByJabatan'])->name('showByJabatan');
    Route::put('/update-jabatan', [MppController::class, 'updateJabatan'])->name('updateJabatan');
    Route::delete('/destroy-jabatan', [MppController::class, 'destroyJabatan'])->name('destroyJabatan');
});

// Administrasi - Cuti (Employee Leave Management) Routes
Route::prefix('administrasi/cuti')->name('administrasi.cuti.')->group(function () {
    Route::get('/', [CutiController::class, 'index'])->name('index');
    Route::post('/', [CutiController::class, 'store'])->name('store');
    Route::post('/year', [CutiController::class, 'storeYear'])->name('year');
    Route::get('/export/report', [CutiController::class, 'export'])->name('export');
    Route::get('/{id}', [CutiController::class, 'show'])->name('show');
    Route::put('/{id}', [CutiController::class, 'update'])->name('update');
    Route::delete('/{id}', [CutiController::class, 'destroy'])->name('destroy');
});

// Administrasi - VRA (Vehicle Running Account) Routes
Route::prefix('administrasi/vra')->name('administrasi.vra.')->group(function () {
    Route::get('/', [VraController::class, 'index'])->name('index');
    Route::post('/', [VraController::class, 'store'])->name('store');
    Route::post('/year', [VraController::class, 'storeYear'])->name('year');
    Route::post('/import', [VraController::class, 'importCsv'])->name('import');
    Route::get('/template/download', [VraController::class, 'downloadTemplate'])->name('template');
    Route::get('/export/report', [VraController::class, 'export'])->name('export');
    Route::get('/{id}', [VraController::class, 'show'])->name('show');
    Route::put('/{id}', [VraController::class, 'update'])->name('update');
    Route::delete('/{id}', [VraController::class, 'destroy'])->name('destroy');
});

// Administrasi - Biaya Rekruit Routes
Route::prefix('administrasi/biaya-rekruit')->name('administrasi.biaya-rekruit.')->group(function () {
    Route::get('/', [BiayaRekruitController::class, 'index'])->name('index');
    Route::post('/', [BiayaRekruitController::class, 'store'])->name('store');
    Route::post('/year', [BiayaRekruitController::class, 'storeYear'])->name('year');
    Route::get('/export/report', [BiayaRekruitController::class, 'export'])->name('export');
    Route::get('/{id}', [BiayaRekruitController::class, 'show'])->name('show');
    Route::put('/{id}', [BiayaRekruitController::class, 'update'])->name('update');
    Route::delete('/{id}', [BiayaRekruitController::class, 'destroy'])->name('destroy');
});

// Administrasi - CAPEX (Capital Expenditure) Routes
Route::prefix('administrasi/capex')->name('administrasi.capex.')->group(function () {
    Route::get('/', [CapexController::class, 'index'])->name('index');
    Route::post('/', [CapexController::class, 'store'])->name('store');
    Route::post('/year/add', [CapexController::class, 'storeYear'])->name('year');
    Route::get('/export/report', [CapexController::class, 'export'])->name('export');
    Route::get('/{id}', [CapexController::class, 'show'])->name('show');
    Route::put('/{id}', [CapexController::class, 'update'])->name('update');
    Route::delete('/{id}', [CapexController::class, 'destroy'])->name('destroy');
});

// Administrasi - Cost Routes
Route::prefix('administrasi/cost')->name('administrasi.cost.')->group(function () {
    Route::get('/', [CostController::class, 'index'])->name('index');
    Route::post('/', [CostController::class, 'store'])->name('store');
    Route::get('/export/report', [CostController::class, 'export'])->name('export');
    Route::get('/{id}', [CostController::class, 'show'])->name('show');
    Route::put('/{id}', [CostController::class, 'update'])->name('update');
    Route::delete('/{id}', [CostController::class, 'destroy'])->name('destroy');
});

// Administrasi - Inventaris Routes
use App\Http\Controllers\Administrasi\InventarisGudangController;
use App\Http\Controllers\Administrasi\InventarisKantorController;
use App\Http\Controllers\Administrasi\InventarisMessController;
use App\Http\Controllers\Administrasi\InventarisSummaryController;

Route::prefix('administrasi/inventaris')->name('administrasi.inventaris.')->group(function () {
    // Summary Overview Page (Default landing for Inventaris)
    Route::get('/', [InventarisSummaryController::class, 'index'])->name('index');
    Route::get('/summary', [InventarisSummaryController::class, 'index'])->name('summary');

    // Gudang Subsystem
    Route::prefix('gudang')->name('gudang.')->group(function () {
        // Tab 1: Barang
        Route::get('/barang', [InventarisGudangController::class, 'barangIndex'])->name('barang');
        Route::post('/barang', [InventarisGudangController::class, 'barangStore'])->name('barang.store');
        Route::put('/barang/{id}', [InventarisGudangController::class, 'barangUpdate'])->name('barang.update');
        Route::delete('/barang/{id}', [InventarisGudangController::class, 'barangDestroy'])->name('barang.destroy');

        // Tab 2: Pembelian
        Route::get('/pembelian', [InventarisGudangController::class, 'pembelianIndex'])->name('pembelian');
        Route::post('/pembelian', [InventarisGudangController::class, 'pembelianStore'])->name('pembelian.store');
        Route::put('/pembelian/{id}', [InventarisGudangController::class, 'pembelianUpdate'])->name('pembelian.update');
        Route::delete('/pembelian/{id}', [InventarisGudangController::class, 'pembelianDestroy'])->name('pembelian.destroy');

        // Tab 3: Stock Opname Sessions
        Route::get('/stock-opname', [InventarisGudangController::class, 'stockOpnameIndex'])->name('stock-opname');
        Route::post('/stock-opname/session', [InventarisGudangController::class, 'stockOpnameSessionStore'])->name('stock-opname.session.store');
        Route::put('/stock-opname/session/{date}', [InventarisGudangController::class, 'stockOpnameSessionUpdate'])->name('stock-opname.session.update');
        Route::delete('/stock-opname/session/{date}', [InventarisGudangController::class, 'stockOpnameSessionDestroy'])->name('stock-opname.session.destroy');

        // Tab 4: Stock Opname Detail
        Route::get('/stock-opname/{date}', [InventarisGudangController::class, 'stockOpnameDetail'])->name('stock-opname.detail');
        Route::get('/stock-opname/{date}/export', [InventarisGudangController::class, 'stockOpnameExport'])->name('stock-opname.export');
        Route::post('/stock-opname/{date}/item', [InventarisGudangController::class, 'stockOpnameItemStore'])->name('stock-opname.item.store');
        Route::put('/stock-opname/item/{id}', [InventarisGudangController::class, 'stockOpnameItemUpdate'])->name('stock-opname.item.update');
        Route::delete('/stock-opname/item/{id}', [InventarisGudangController::class, 'stockOpnameItemDestroy'])->name('stock-opname.item.destroy');
    });

    // Kantor Subsystem
    Route::prefix('kantor')->name('kantor.')->group(function () {
        // Tab 1: Barang
        Route::get('/barang', [InventarisKantorController::class, 'barangIndex'])->name('barang');
        Route::post('/barang', [InventarisKantorController::class, 'barangStore'])->name('barang.store');
        Route::put('/barang/{id}', [InventarisKantorController::class, 'barangUpdate'])->name('barang.update');
        Route::delete('/barang/{id}', [InventarisKantorController::class, 'barangDestroy'])->name('barang.destroy');

        // Tab 2: Pembelian
        Route::get('/pembelian', [InventarisKantorController::class, 'pembelianIndex'])->name('pembelian');
        Route::post('/pembelian', [InventarisKantorController::class, 'pembelianStore'])->name('pembelian.store');
        Route::put('/pembelian/{id}', [InventarisKantorController::class, 'pembelianUpdate'])->name('pembelian.update');
        Route::delete('/pembelian/{id}', [InventarisKantorController::class, 'pembelianDestroy'])->name('pembelian.destroy');

        // Tab 3: Stock Opname Sessions
        Route::get('/stock-opname', [InventarisKantorController::class, 'stockOpnameIndex'])->name('stock-opname');
        Route::post('/stock-opname/session', [InventarisKantorController::class, 'stockOpnameSessionStore'])->name('stock-opname.session.store');
        Route::put('/stock-opname/session/{date}', [InventarisKantorController::class, 'stockOpnameSessionUpdate'])->name('stock-opname.session.update');
        Route::delete('/stock-opname/session/{date}', [InventarisKantorController::class, 'stockOpnameSessionDestroy'])->name('stock-opname.session.destroy');

        // Tab 4: Stock Opname Detail
        Route::get('/stock-opname/{date}', [InventarisKantorController::class, 'stockOpnameDetail'])->name('stock-opname.detail');
        Route::get('/stock-opname/{date}/export', [InventarisKantorController::class, 'stockOpnameExport'])->name('stock-opname.export');
        Route::post('/stock-opname/{date}/item', [InventarisKantorController::class, 'stockOpnameItemStore'])->name('stock-opname.item.store');
        Route::put('/stock-opname/item/{id}', [InventarisKantorController::class, 'stockOpnameItemUpdate'])->name('stock-opname.item.update');
        Route::delete('/stock-opname/item/{id}', [InventarisKantorController::class, 'stockOpnameItemDestroy'])->name('stock-opname.item.destroy');
    });

    // Mess Subsystem (Read-Only)
    Route::prefix('mess')->name('mess.')->group(function () {
        // Tab 1: Barang
        Route::get('/barang', [InventarisMessController::class, 'barangIndex'])->name('barang');

        // Tab 2: Pembelian
        Route::get('/pembelian', [InventarisMessController::class, 'pembelianIndex'])->name('pembelian');

        // Tab 3: Stock Opname Sessions
        Route::get('/stock-opname', [InventarisMessController::class, 'stockOpnameIndex'])->name('stock-opname');

        // Tab 4: Stock Opname Detail
        Route::get('/stock-opname/{date}', [InventarisMessController::class, 'stockOpnameDetail'])->name('stock-opname.detail');
        Route::get('/stock-opname/{date}/export', [InventarisMessController::class, 'stockOpnameExport'])->name('stock-opname.export');
    });
});

// Top Management Module (React SPA) Routes
Route::get('/Top_Management/{any?}', function () {
    return view('Top_Management.index');
})->where('any', '.*')->name('Top_Management.index');

Route::get('/top-management/{any?}', function () {
    return view('Top_Management.index');
})->where('any', '.*')->name('top-management');

// Administrasi - Monitoring Karyawan (React SPA) Routes
Route::get('/administrasi/monitoring/{any?}', function () {
    return view('Administrasi.Pages.Monitoring.index');
})->where('any', '.*')->name('administrasi.monitoring.index');

Route::get('/Administrasi/Monitoring/{any?}', function () {
    return view('Administrasi.Pages.Monitoring.index');
})->where('any', '.*');

