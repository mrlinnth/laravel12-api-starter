<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect /readme to /readme/index.html
Route::get('/readme', fn () => redirect('/readme/index.html'));
