<?php

namespace App\Http\Controllers;

use App\Http\Requests\Penghuni\StorePenghuniRequest;
use App\Http\Requests\Penghuni\UpdatePenghuniRequest;
use App\Http\Resources\PenghuniCollection;
use App\Models\Penghuni;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenghuniController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $allPenghuni = Penghuni::orderBy("nama_lengkap");
            $allPenghuni = $request->has("all") ? $allPenghuni->get() : $allPenghuni->paginate(10);
            return new PenghuniCollection("Fetch data 'Penghuni' successfully!", $allPenghuni);
        } catch (HttpResponseException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], $exception->getResponse()->getStatusCode());
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while fetching 'Penghuni' data",
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePenghuniRequest $request)
    {
        try {
            $data = $request->all();
            $data["created_by"] = auth()->user()->id;
            $data["updated_by"] = auth()->user()->id;

            if ($request->hasFile("foto_ktp")) {
                $data["foto_ktp"] = $request->file("foto_ktp")->store('ktp');
            }

            $newPenghuni = Penghuni::create($data)->toArray();
            return new PenghuniCollection("Store data 'Penghuni' successfully!", $newPenghuni);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while storing 'Penghuni' data",
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penghuni $penghuni)
    {
        try {
            return new PenghuniCollection("Fetch data 'Penghuni' successfully!", collect($penghuni));
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while fetching 'Penghuni' data",
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePenghuniRequest $request, Penghuni $penghuni)
    {
        try {
            $data = $request->except("_method");
            $data["updated_by"] = auth()->user()->id;
            $ktp = explode("storage/", $penghuni->foto_ktp);
            $ktp = $ktp[1] ?? null;
            $data["foto_ktp"] = $ktp;

            if ($request->hasFile("foto_ktp")) {
                Storage::delete($ktp);
                $data["foto_ktp"] = $request->file("foto_ktp")->store('ktp');
            }

            $penghuni->update($data);
            $updatedPenghuni = $penghuni->refresh()->toArray();
            return new PenghuniCollection("Update data 'Penghuni' successfully!", $updatedPenghuni);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while updating 'Penghuni' data",
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penghuni $penghuni)
    {
        try {
            $ktp = explode("storage/", $penghuni->foto_ktp);
            $ktp = $ktp[1] ?? null;
            Storage::delete($ktp);
            $penghuni->updated_by = auth()->user()->id;
            $penghuni->delete();
            return response()->json([
                'message' => "Delete data 'Penghuni' successfully!",
            ], 204);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => "An error occurred while deleting 'Penghuni' data",
            ], 500);
        }
    }
}
