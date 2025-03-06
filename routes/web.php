<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    abort(403, "Sorry you are forbidden to access this API.");
})->name("login");
