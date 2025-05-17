<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckRoutePermissionMiddleware;
use App\Http\Middleware\SetCurrentTenantPermissionMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/home');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => Inertia::render('Auth/Login'))->name('login');
    Route::post('/login', LoginController::class)->name('login.attempt');
    Route::get('/registrar', fn () => Inertia::render('Auth/Register'))->name('register');
    Route::post('/registrar', RegisterController::class)->name('register.attempt');
});

Route::post('/logout', LogoutController::class)->name('logout')->middleware('auth');

Route::middleware(['auth', SetCurrentTenantPermissionMiddleware::class, CheckRoutePermissionMiddleware::class])->group(function () {
    Route::prefix('/api')->as('api.')->group(function () {
        Route::get('/roles/search', [RoleController::class, 'search'])->name('roles.search');
        Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    });

    Route::get('/home', fn () => Inertia::render('Home/Index'))->name('home.index');

    Route::controller(UserController::class)->prefix('usuarios')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/criar', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{user}/editar', 'edit')->name('edit');
        Route::put('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

    Route::controller(RoleController::class)->prefix('papeis')->name('roles.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/criar', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{role}/editar', 'edit')->name('edit');
        Route::put('/{role}', 'update')->name('update');
        Route::delete('/{role}', 'destroy')->name('destroy');
    });
});
