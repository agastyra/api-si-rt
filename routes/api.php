<?php

use App\Enum\TokenAbility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticateController;

Route::get("/", fn() => response()->json(["message" => "Welcome to the API"]));

Route::post("auth/login", [AuthenticateController::class, "store"])->name("api.login");
Route::post('auth/refresh-token', [AuthenticateController::class, 'refreshToken'])
    ->middleware(['auth:sanctum', 'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value])
    ->name("api.refresh-token");

Route::middleware(['auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value])->group(function () {
    Route::delete("auth/logout", [AuthenticateController::class, "destroy"])->name("api.logout");

    Route::apiResource("penghuni", \App\Http\Controllers\PenghuniController::class);
    Route::apiResource("rumah", \App\Http\Controllers\RumahController::class);
    Route::apiResource("tipe-transaksi", \App\Http\Controllers\TipeTransaksiController::class);
    Route::apiResource("transaksi", \App\Http\Controllers\TransaksiController::class);
    Route::apiResource("penghuni-rumah", \App\Http\Controllers\PenghuniRumahController::class);
});
