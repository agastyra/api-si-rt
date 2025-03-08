<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class ReportController extends Controller
{
    public function getMonthlyBilling(Request $request)
    {
        try {
            $request->validate([
                "blok" => "required|exists:rumahs,blok",
                "periode_bulan" => "required",
                "periode_tahun" => "required"
            ], [
                "blok.required" => "Blok harus diisi",
                "blok.exists" => "Blok tidak ditemukan",
                "periode_bulan.required" => "Periode bulan harus diisi",
                "periode_tahun.required" => "Periode tahun harus diisi"
            ]);

            $blok = $request->input("blok");
            $periode_bulan = $request->input("periode_bulan");
            $periode_tahun = $request->input("periode_tahun");

            $monthlyBilling = ApiService::monthlyReport($periode_bulan, $periode_tahun, $blok);

            return response()->json([
                "message" => "Fetch data 'Monthly Billing' successfully!",
                "data" => $monthlyBilling
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => 'Failed to process',
                'status' => 422,
                'errors' => $validationException->validator->errors(),
            ], 422);
        } catch (\Exception $exception) {
            return response()->json([
                "message" => "An error occurred while fetching 'Monthly Billing' data",
                "error" => $exception->getMessage()
            ], $exception->getCode());
        }

    }

    public function getMonthlyTransaction(Request $request)
    {
        try {
            $request->validate([
                "periode_bulan" => "required",
                "periode_tahun" => "required"
            ], [
                "periode_bulan.required" => "Periode bulan harus diisi",
                "periode_tahun.required" => "Periode tahun harus diisi"
            ]);

            $periode_bulan = $request->input("periode_bulan");
            $periode_tahun = $request->input("periode_tahun");

            $monthlyTransaction = ApiService::monthlyReport($periode_bulan, $periode_tahun);

            return response()->json([
                "message" => "Fetch data 'Monthly Transaction' successfully!",
                "data" => $monthlyTransaction
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => 'Failed to process',
                'status' => 422,
                'errors' => $validationException->validator->errors(),
            ], 422);
        } catch (\Exception $exception) {
            return response()->json([
                "message" => "An error occurred while fetching 'Monthly Transaction' data",
                "error" => $exception->getMessage()
            ], 500);
        }
    }
}
