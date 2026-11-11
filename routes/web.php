<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuiviProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthController::class, 'create'])->name('login');
	Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
	Route::get('/dashboard', [SuiviProjectController::class, 'dashboard'])->name('dashboard');

	Route::get('/instancels', [SuiviProjectController::class, 'index'])->name('instancels.index');
	Route::post('/instancels', [SuiviProjectController::class, 'store'])->name('instancels.store');
	Route::get('/instancels/{id}/edit', [SuiviProjectController::class, 'editInstancel'])->name('instancels.edit');
	Route::put('/instancels/{id}', [SuiviProjectController::class, 'updateInstancel'])->name('instancels.update');

	Route::get('/instances', [SuiviProjectController::class, 'instanceIndex'])->name('instances.index');
	Route::post('/instances', [SuiviProjectController::class, 'storeInstance'])->name('instances.store');
	Route::get('/instances/{id}/edit', [SuiviProjectController::class, 'editInstance'])->name('instances.edit');
	Route::put('/instances/{id}', [SuiviProjectController::class, 'updateInstance'])->name('instances.update');

	Route::get('/project-b2b', [SuiviProjectController::class, 'projectB2BIndex'])->name('projects-b2b.index');
	Route::post('/project-b2b', [SuiviProjectController::class, 'storeProjectB2B'])->name('projects-b2b.store');
	Route::get('/project-b2b/{id}/edit', [SuiviProjectController::class, 'editProjectB2B'])->name('projects-b2b.edit');
	Route::put('/project-b2b/{id}', [SuiviProjectController::class, 'updateProjectB2B'])->name('projects-b2b.update');

	Route::middleware('admin')->group(function () {
		Route::delete('/instancels/{id}', [SuiviProjectController::class, 'destroyInstancel'])->name('instancels.destroy');
		Route::delete('/instances/{id}', [SuiviProjectController::class, 'destroyInstance'])->name('instances.destroy');
		Route::delete('/project-b2b/{id}', [SuiviProjectController::class, 'destroyProjectB2B'])->name('projects-b2b.destroy');

		Route::prefix('admin')->name('admin.')->group(function () {
			Route::get('/', [AdminController::class, 'index'])->name('index');
			Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
			Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
			Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
			Route::post('/states', [AdminController::class, 'storeState'])->name('states.store');
			Route::put('/states/{state}', [AdminController::class, 'updateState'])->name('states.update');
			Route::patch('/states/{state}/active', [AdminController::class, 'toggleState'])->name('states.toggle');
			Route::delete('/states/{state}', [AdminController::class, 'destroyState'])->name('states.destroy');
		});
	});
});
