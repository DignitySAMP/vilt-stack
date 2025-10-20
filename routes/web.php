<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function (): mixed {
    return Inertia::render('Welcome');
});
