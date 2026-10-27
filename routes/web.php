<?php

declare(strict_types=1);

use App\DTOs\PageMeta;
use Illuminate\Support\Facades\Route;
use Inertia\Response;

Route::get('/', fn (): Response => vue('Home', metaProps: new PageMeta(title: 'Home')))->name('home');

Route::middleware(['auth', 'verified', 'can:accessAdministration'])
    ->prefix('/administration')->as('administration.')
    ->group(function () {
        Route::redirect('/', 'administration/dashboard');
        Route::get('/dashboard', fn (): Response => vue('administration/Dashboard', metaProps: new PageMeta(title: 'Dashboard')))
            ->name('dashboard');
    });

require __DIR__ . '/account.php';
