<?php

declare(strict_types=1);

use App\DTOs\PageMeta;
use App\Http\Controllers\Administration\DashboardController;
use App\Http\Controllers\Administration\Settings\GeneralSettingsController;
use App\Http\Controllers\Administration\UserController;
use App\Http\Controllers\Administration\UserRoleController;
use App\Http\Controllers\Administration\UserVerificationController;
use Illuminate\Support\Facades\Route;
use Inertia\Response;

Route::get('/', fn (): Response => vue('Home', metaProps: new PageMeta(title: 'Home')))->name('home');

Route::middleware(['auth', 'verified', 'can:accessAdministration'])
    ->prefix('/administration')->as('administration.')
    ->group(function () {
        Route::redirect('/', 'administration/dashboard');
        Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{user}/role', [UserRoleController::class, 'update'])->name('users.role.update');
        Route::post('/users/{user}/verification-notification', [UserVerificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('users.verification.store');

        Route::middleware('can:manageSettings')
            ->prefix('/settings')->as('settings.')
            ->group(function () {
                Route::redirect('/', '/administration/settings/general');
                Route::get('/general', [GeneralSettingsController::class, 'edit'])->name('general.edit');
                Route::put('/general', [GeneralSettingsController::class, 'update'])->name('general.update');
            });
    });

require __DIR__ . '/account.php';
