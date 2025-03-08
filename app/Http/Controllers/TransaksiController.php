<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaksi\StoreTransaksiRequest;
use App\Http\Requests\Transaksi\UpdateTransaksiRequest;
use App\Http\Requests\TransaksiDetail\StoreTransaksiDetailRequest;
use App\Http\Resources\TransaksiCollection;
use App\Models\Rumah;
use App\Models\Transaksi;
use App\Services\ApiService;
use Illuminate\Support\Str;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $allTransaksi = Transaksi::all();
            return new TransaksiCollection("Fetch data 'Transaksi' successfully!", $allTransaksi);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while fetching 'Transaksi' data",
                "error" => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransaksiRequest $request)
    {
        try {
            if ($request->has("rumah_id")) {
                $rumah = Rumah::find($request->rumah_id);
                ApiService::checkStatusForRumah($rumah);
            }

            $data = $request->except("transaksi_detail");
            $data["created_by"] = auth()->user()->id;
            $data["updated_by"] = auth()->user()->id;

            $newTransaksi = Transaksi::create($data);
            $allTransaksiDetail = collect($request->transaksi_detail)->map(function ($detail) {
                return [
                    "tipe_transaksi_id" => $detail["tipe_transaksi_id"],
                    "periode_bulan" => $detail["periode_bulan"],
                    "periode_tahun" => $detail["periode_tahun"],
                    "nominal" => $detail["nominal"],
                    "created_by" => auth()->user()->id,
                    "updated_by" => auth()->user()->id
                ];
            });

            $newTransaksi->transaksi_detail()->createMany($allTransaksiDetail->toArray());
            $newTransaksi = Transaksi::where('id', $newTransaksi->id)->get();
            return new TransaksiCollection("Fetch data 'Transaksi' successfully!", $newTransaksi);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function storeTransaksiDetail(StoreTransaksiDetailRequest $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransaksiRequest $request, Transaksi $transaksi)
    {
        try {
            if ($request->has("rumah_id")) {
                $rumah = Rumah::find($request->rumah_id);
                ApiService::checkStatusForRumah($rumah);
            }

            $data = $request->except('transaksi_detail');
            $data['updated_by'] = auth()->user()->id;
            $transaksi->update($data);

            $allTransaksiDetail = collect($request->transaksi_detail)->map(function ($detail) {
                return [
                    'tipe_transaksi_id' => $detail['tipe_transaksi_id'],
                    'periode_bulan' => $detail['periode_bulan'],
                    'periode_tahun' => $detail['periode_tahun'],
                    'nominal' => $detail['nominal'],
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ];
            });

            $transaksi->transaksi_detail()->update(['deletion_token' => Str::uuid()]);
            $transaksi->transaksi_detail()->delete();
            $transaksi->transaksi_detail()->createMany($allTransaksiDetail->toArray());

            $updatedTransaksi = Transaksi::find($transaksi->id);

            return new TransaksiCollection("Update data 'Transaksi' successfully!", collect([$updatedTransaksi]));
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while updating 'Transaksi' data",
                'error' => $exception->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $transaksi)
    {
        try {
            $transaksi->updated_by = auth()->user()->id;
            $transaksi->delete();
            return response()->json([
                'message' => "Delete data 'Transaksi' successfully!",
            ], 204);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while deleting 'Transaksi' data",
            ], 500);
        }
    }
}
