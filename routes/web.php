<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EngineerActivityController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

Route::middleware(['auth', 'role:admin|engineer'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('customers', CustomerController::class);
    Route::post('/customers/quick-create', [CustomerController::class, 'quickCreate'])->name('customers.quick-create');
    Route::resource('contracts', ContractController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('responses', ResponseController::class);

    Route::get('/service-history', [ServiceController::class, 'history'])->name('service-history');

    Route::get('/search', [SearchController::class, 'index'])->name('search');

    Route::get('/export/customers', [ExportController::class, 'customers'])->name('export.customers');
    Route::get('/export/contracts', [ExportController::class, 'contracts'])->name('export.contracts');
    Route::get('/export/services', [ExportController::class, 'services'])->name('export.services');
    Route::get('/export/service-history', [ExportController::class, 'serviceHistory'])->name('export.service-history');
    Route::get('/export/responses', [ExportController::class, 'responses'])->name('export.responses');
    Route::get('/export/reports', [ExportController::class, 'reports'])->name('export.reports');

    Route::get('/data-entry', function () {
        return view('data-entry', [
            'customers' => \App\Models\Customer::orderBy('name')->get(['name', 'region', 'email', 'job_ref']),
            'engineers' => \App\Models\User::engineers()->active()->orderBy('name')->get(),
        ]);
    })->name('data-entry');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/email', [SettingsController::class, 'getEmailSettings'])->name('settings.email');
    Route::put('/settings/email/{emailType}', [SettingsController::class, 'updateEmailSetting'])->name('settings.email.update');

    Route::resource('users', UserController::class);
    Route::post('/users/quick-create', [UserController::class, 'quickCreate'])->name('users.quick-create');

    Route::get('/admin/engineer-activity', [EngineerActivityController::class, 'index'])
        ->name('admin.engineer-activity');
});

require __DIR__.'/auth.php';
