<?php

use App\Api\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticateController;

Route::post("auth/login", [AuthenticateController::class, "store"])->name("api.login");
Route::post('auth/refresh-token', [AuthenticateController::class, 'refreshToken'])
    ->middleware(['auth:sanctum', 'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value])
    ->name("api.refresh-token");

Route::middleware(['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value])->group(function () {
    Route::delete("auth/logout", [AuthenticateController::class, "destroy"])->name("api.logout");
});
