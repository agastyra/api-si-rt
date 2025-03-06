<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        "message" => "Sorry you are forbidden to access this API."
    ], 403);
})->name("login");
