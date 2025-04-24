<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BallisticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('ballistics.home');
})->name('home');

Route::get('/calculator', [BallisticsController::class, 'calculator'])->name('calculator');
Route::get('/load-data', [BallisticsController::class, 'loadData'])->name('load-data');

// Authentication routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['verified'])
        ->name('dashboard');
    
    // Image Upload
    Route::get('/upload', [CaseController::class, 'showUploadForm'])->name('upload.form');
    Route::post('/upload', [CaseController::class, 'upload'])->name('upload.process');
    
    // Case Management
    Route::get('/case/{id}', [CaseController::class, 'show'])->name('case.show');
    Route::post('/case/{id}/analyze', [CaseController::class, 'runAnalysis'])->name('case.analyze');
    Route::get('/case/{id}/export', [CaseController::class, 'exportReport'])->name('case.export');
    
    // Analysis Results
    Route::get('/analysis/{id}', [AnalysisController::class, 'show'])->name('analysis.show');
    Route::post('/analysis/{id}/adjust', [AnalysisController::class, 'adjustThreshold'])->name('analysis.adjust');
    Route::post('/analysis/{id}/flag', [AnalysisController::class, 'flagMatch'])->name('analysis.flag');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/reports/{id}/share', [ReportController::class, 'share'])->name('reports.share');
    
    // Admin routes with additional admin middleware
    Route::middleware(['admin'])->group(function () {
        // User Management
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::post('/admin/users/{id}/role', [AdminController::class, 'updateRole'])->name('admin.users.role');
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        
        // Audit Logs
        Route::get('/admin/audit', [AdminController::class, 'auditLogs'])->name('admin.audit');
        Route::get('/admin/audit/export', [AdminController::class, 'exportAuditLogs'])->name('admin.audit.export');
        Route::post('/admin/audit/verify', [AdminController::class, 'verifyIntegrity'])->name('admin.audit.verify');
    });
    
    // Help & Documentation
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::get('/help/api', [HelpController::class, 'api'])->name('help.api');
    Route::get('/help/tutorials', [HelpController::class, 'tutorials'])->name('help.tutorials');
    Route::post('/help/request-feature', [HelpController::class, 'requestFeature'])->name('help.request');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
