<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard\DashboardIndex::class)->name('dashboard.index');

/**
 * Routes for your shared application pages. This DOES NOT mean shared as in
 * "every app has this route" shared means it only uses the shared DB.
 *
 * Think: super-admin stuff
 */
