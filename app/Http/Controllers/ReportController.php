<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class ReportController extends Controller
{
    public function getMonthlyBillingPerHouse(Request $request)
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

            $monthlyBilling = ApiService::generateTransactionReport($periode_bulan, $periode_tahun, $blok, null, "Pemasukan");

            return response()->json([
                "message" => "Fetch data 'Monthly Billing' successfully!",
                "data" => $monthlyBilling
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => 'Failed to process',
                'errors' => $validationException->validator->errors(),
            ], 422);
        } catch (\Exception $exception) {
            return response()->json([
                "message" => "An error occurred while fetching 'Monthly Billing' data",
                "error" => $exception->getMessage()
            ], $exception->getCode());
        }

    }

    public function getTransactionReport(Request $request)
    {
        try {
            $request->validate([
                "periode_bulan" => "in:1,2,3,4,5,6,7,8,9,10,11,12",
                "periode_tahun" => "required"
            ], [
                "periode_tahun.required" => "Periode tahun harus diisi",
                "periode_bulan.in" => "Periode bulan tidak valid"
            ]);

            $periode_bulan = $request->input("periode_bulan");
            $periode_tahun = $request->input("periode_tahun");
            $tipe_transaksi_id = $request->input("tipe_transaksi");
            $jenis_transaksi = $request->input("jenis_transaksi");

            $transactionReport = ApiService::generateTransactionReport($periode_bulan, $periode_tahun, null,
                $tipe_transaksi_id, $jenis_transaksi);

            return response()->json([
                "message" => "Fetch data 'Transaction Report' successfully!",
                "data" => $transactionReport
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => 'Failed to process',
                'errors' => $validationException->validator->errors(),
            ], 422);
        } catch (\Exception $exception) {
            return response()->json([
                "message" => "An error occurred while fetching 'Transaction Report' data",
                "error" => $exception->getMessage()
            ], 500);
        }
    }

    public function getBalanceSummary(Request $request)
    {
        try {
            $request->validate([
                "periode_tahun" => "required"
            ], [
                "periode_tahun.required" => "Periode tahun harus diisi",
            ]);

            $periode_tahun = $request->input("periode_tahun");

            $balanceSummary = ApiService::generateBalanceSummary($periode_tahun);

            return response()->json([
                "message" => "Fetch data 'Balance Summary' successfully!",
                "data" => $balanceSummary
            ]);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => 'Failed to process',
                'errors' => $validationException->validator->errors(),
            ], 422);
        } catch (\Exception $exception) {
            return response()->json([
                "message" => "An error occurred while fetching 'Balance Summary' data",
                "error" => $exception->getMessage()
            ], 500);
        }
    }
}
