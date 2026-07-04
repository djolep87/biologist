<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDokumentiController;
use App\Http\Controllers\Admin\AdminEvidencijeController;
use App\Http\Controllers\Admin\AdminExportController;
use App\Http\Controllers\Admin\AdminGio1Controller;
use App\Http\Controllers\Admin\AdminKlijentRadController;
use App\Http\Controllers\Admin\AdminKlijentiController;
use App\Http\Controllers\Admin\AdminOperateriController;
use App\Http\Controllers\Admin\AdminWastePlanController;
use App\Http\Controllers\Admin\AdminZahteviController;
use App\Http\Controllers\DokoController;
use App\Http\Controllers\EvidencijaOtpadaController;
use App\Http\Controllers\Gio1Controller;
use App\Http\Controllers\OperaterApiController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/api/operateri/search', [OperaterApiController::class, 'search'])->name('api.operateri.search');
    Route::get('/api/operateri/{operater}', [OperaterApiController::class, 'show'])->name('api.operateri.show');

    Route::get('/doko/{dokument}/download-doko', [DokoController::class, 'downloadDoko'])->name('doko.download');
    Route::get('/doko/{dokument}/download-deo1', [DokoController::class, 'downloadDeo1'])->name('doko.deo1');

    Route::get('/pdf/deo1/mesecni/{team}', [PdfController::class, 'deo1Mesecni'])->name('pdf.deo1.mesecni');
    Route::get('/pdf/deo1/{evidencija}', [PdfController::class, 'deo1'])->name('pdf.deo1');

    Route::get('/gio1/{izvestaj}/download', [Gio1Controller::class, 'download'])->name('gio1.download');
});

Route::prefix('admin')
    ->middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
        'super.admin',
    ])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard-stats', [AdminDashboardController::class, 'stats'])->name('dashboard-stats');
        Route::get('/operateri', [AdminOperateriController::class, 'index'])->name('operateri.index');
        Route::get('/klijenti', [AdminKlijentiController::class, 'index'])->name('klijenti.index');
        Route::get('/klijenti/{team}', [AdminKlijentiController::class, 'show'])->name('klijenti.show');
        Route::get('/klijenti/{team}/rad', [AdminKlijentiController::class, 'radiSa'])->name('klijenti.radi');
        Route::get('/evidencije', [AdminEvidencijeController::class, 'index'])->name('evidencije.index');
        Route::get('/klijent-rad', [AdminKlijentRadController::class, 'index'])->name('klijent-rad');
        Route::get('/export/deo1', [AdminExportController::class, 'deo1'])->name('export.deo1');
        Route::get('/export/deo1/{evidencija}', [AdminExportController::class, 'deo1ByRecord'])->name('export.deo1-record');
        Route::get('/dokumenti', [AdminDokumentiController::class, 'index'])->name('dokumenti.index');
        Route::get('/zahtevi', [AdminZahteviController::class, 'index'])->name('zahtevi.index');
        Route::get('/zahtevi/{zahtev}', [AdminZahteviController::class, 'show'])->name('zahtevi.show');
        Route::post('/zahtevi/{zahtev}/odbij', [AdminZahteviController::class, 'odbij'])->name('zahtevi.odbij');
        Route::get('/gio1', [AdminGio1Controller::class, 'index'])->name('gio1.index');
        Route::get('/waste-plans', [AdminWastePlanController::class, 'index'])->name('waste-plans.index');
        Route::post('/api/admin/generate-waste-plan', [AdminWastePlanController::class, 'generate'])->name('waste-plans.generate');
        Route::get('/waste-plans/{wastePlan}/pdf', [AdminWastePlanController::class, 'pdf'])->name('waste-plans.pdf');
        Route::delete('/waste-plans/{wastePlan}', [AdminWastePlanController::class, 'destroy'])->name('waste-plans.destroy');
    });

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'ensure.has.team',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/evidencija', [EvidencijaOtpadaController::class, 'index'])->name('evidencija.index');
    Route::get('/evidencija/export', [EvidencijaOtpadaController::class, 'export'])->name('evidencija.export');
    Route::get('/evidencija/export/{evidencija}', [EvidencijaOtpadaController::class, 'exportByRecord'])->name('evidencija.export.record');
    Route::get('/evidencija/print', [EvidencijaOtpadaController::class, 'print'])->name('evidencija.print');
});
