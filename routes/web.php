<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

/**
 * Case study pages. Project::getRouteKeyName() returns 'slug', so Laravel's
 * implicit model binding resolves /work/coolpex-water-filters automatically —
 * no manual lookup, and a 404 for anything that doesn't exist.
 */
Route::get('/work/{project}', ProjectController::class)->name('projects.show');
