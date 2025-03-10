<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenghuniRumah\StorePenghuniRumahRequest;
use App\Http\Requests\PenghuniRumah\UpdatePenghuniRumahRequest;
use App\Http\Resources\PenghuniRumahResource;
use App\Models\PenghuniRumah;

class PenghuniRumahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $allPenghuniRumah = PenghuniRumah::paginate(10);
            return PenghuniRumahResource::collection($allPenghuniRumah);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while fetching 'Penghuni Rumah' data",
                "error" => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenghuniRumahRequest $request)
    {
        try {
            $data = $request->all();
            $data["created_by"] = auth()->user()->id;
            $data["updated_by"] = auth()->user()->id;

            $newpenghuniRumah = PenghuniRumah::create($data);
            return new PenghuniRumahResource($newpenghuniRumah);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while storing 'Penghuni Rumah' data",
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PenghuniRumah $penghuniRumah)
    {
        try {
            return new PenghuniRumahResource($penghuniRumah);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while fetching 'Penghuni Rumah' data",
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePenghuniRumahRequest $request, PenghuniRumah $penghuniRumah)
    {
        try {
            $data = $request->all();
            $data["updated_by"] = auth()->user()->id;

            $penghuniRumah->update($data);
            $updatedPenghuniRumah = $penghuniRumah->refresh();
            return new PenghuniRumahResource($updatedPenghuniRumah, "Update data 'Penghuni Rumah' successfully!");
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while updating 'Penghuni Rumah' data",
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PenghuniRumah $penghuniRumah)
    {
        try {
            $penghuniRumah->updated_by = auth()->user()->id;
            $penghuniRumah->delete();
            return response()->json([
                'message' => "Delete data 'Penghuni Rumah' successfully!",
            ], 204);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while deleting 'Penghuni Rumah' data",
            ], 500);
        }
    }
}
