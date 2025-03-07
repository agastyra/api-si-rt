<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipeTransaksi\StoreTipeTransaksiRequest;
use App\Http\Requests\TipeTransaksi\UpdateTipeTransaksiRequest;
use App\Http\Resources\TipeTransaksiCollection;
use App\Models\TipeTransaksi;
use Illuminate\Http\Exceptions\HttpResponseException;

class TipeTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $allTipeTransaksi = TipeTransaksi::orderBy("nama")
                ->get();
            return new TipeTransaksiCollection("Fetch data 'Tipe Transaksi' successfully!", $allTipeTransaksi);
        } catch (HttpResponseException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], $exception->getResponse()->getStatusCode());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while fetching 'Tipe Transaksi' data",
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTipeTransaksiRequest $request)
    {
        try {
            $data = $request->all();
            $data["created_by"] = auth()->user()->id;
            $data["updated_by"] = auth()->user()->id;

            $newTipeTransaksi = TipeTransaksi::create($data)->toArray();
            return new TipeTransaksiCollection("Store data 'Tipe Transaksi' successfully!", $newTipeTransaksi);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while storing 'Tipe Transaksi' data",
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TipeTransaksi $tipeTransaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipeTransaksiRequest $request, TipeTransaksi $tipeTransaksi)
    {
        try {
            $data = $request->all();

            $tipeTransaksi->update($data);
            $updatedTipeTransaksi = $tipeTransaksi->refresh()->toArray();
            return new TipeTransaksiCollection("Update data 'Tipe Transaksi' successfully!", $updatedTipeTransaksi);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while updating 'Tipe Transaksi' data",
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipeTransaksi $tipeTransaksi)
    {
        try {
            $tipeTransaksi->updated_by = auth()->user()->id;
            $tipeTransaksi->delete();
            return response()->json([
                'message' => "Delete data 'Tipe Transaksi' successfully!",
            ], 204);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while deleting 'Tipe Transaksi' data",
            ], 500);
        }
    }
}
