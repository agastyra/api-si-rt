<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rumah\StoreRumahRequest;
use App\Http\Requests\Rumah\UpdateRumahRequest;
use App\Http\Resources\RumahCollection;
use App\Models\Rumah;
use Illuminate\Http\Exceptions\HttpResponseException;

class RumahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $allRumah = Rumah::orderBy("blok")
                ->get();
            return new RumahCollection("Fetch data 'Rumah' successfully!", $allRumah);
        } catch (HttpResponseException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], $exception->getResponse()->getStatusCode());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while fetching 'Rumah' data",
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRumahRequest $request)
    {
        try {
            $data = $request->all();
            $data["created_by"] = auth()->user()->id;
            $data["updated_by"] = auth()->user()->id;

            $newRumah = Rumah::create($data)->toArray();
            return new RumahCollection("Store data 'Rumah' successfully!", $newRumah);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while storing 'Rumah' data",
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Rumah $rumah)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRumahRequest $request, Rumah $rumah)
    {
        try {
            $data = $request->all();

            $rumah->update($data);
            $updatedRumah = $rumah->refresh()->toArray();
            return new RumahCollection("Update data 'Rumah' successfully!", $updatedRumah);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while updating 'Rumah' data",
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rumah $rumah)
    {
        try {
            $rumah->updated_by = auth()->user()->id;
            $rumah->delete();
            return response()->json([
                'message' => "Delete data 'Rumah' successfully!",
            ], 204);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while deleting 'Rumah' data",
            ], 500);
        }
    }
}
