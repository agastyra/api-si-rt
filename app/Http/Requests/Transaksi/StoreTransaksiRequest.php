<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreTransaksiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "rumah_id" => ["nullable", "exists:rumahs,id"],
            "tanggal_transaksi" => ["required", "date"],
            "no_transaksi" => ["required", "string", "size:6",
                Rule::unique("transaksis", "no_transaksi")
                    ->ignore($this->transaksi)
                    ->whereNull("deleted_at")],
            "transaksi_detail" => ["required", "array"],
            "transaksi_detail.*.tipe_transaksi_id" => ["required", "exists:tipe_transaksis,id"],
            "transaksi_detail.*.nominal" => ["required", "numeric"],
            "transaksi_detail.*.periode_bulan" => ["required", "numeric", "in:1,2,3,4,5,6,7,8,9,10,11,12"],
            "transaksi_detail.*.periode_tahun" => ["required", "digits:4", "integer", "min:2023", "max:" . date('Y')],
        ];
    }

    public function messages(): array
    {
        return [
            "rumah_id.exists" => "Rumah tidak ditemukan",
            "tanggal_transaksi.required" => "Tanggal transaksi harus diisi",
            "tanggal_transaksi.date" => "Tanggal transaksi harus berupa tanggal",
            "no_transaksi.required" => "No transaksi harus diisi",
            "no_transaksi.string" => "No transaksi harus berupa string",
            "no_transaksi.unique" => "No transaksi sudah digunakan",
            "transaksi_detail.required" => "Detail transaksi harus diisi",
            "transaksi_detail.array" => "Detail transaksi harus berupa array",
            "transaksi_detail.*.transaksi_id.required" => "Transaksi harus diisi",
            "transaksi_detail.*.transaksi_id.exists" => "Transaksi tidak ditemukan",
            "transaksi_detail.*.nominal.required" => "Nominal harus diisi",
            "transaksi_detail.*.nominal.numeric" => "Nominal harus berupa angka",
            "transaksi_detail.*.periode_bulan.required" => "Periode bulan harus diisi",
            "transaksi_detail.*.periode_bulan.numeric" => "Periode bulan tidak valid",
            "transaksi_detail.*.periode_bulan.in" => "Periode bulan tidak valid",
            "transaksi_detail.*.periode_tahun.required" => "Periode tahun harus diisi",
            "transaki_detail.*.periode_tahun.digits" => "Periode tahun tidak valid",
            "transaksi_detail.*.periode_tahun.integer" => "Periode tahun tidak valid",
            "transaksi_detail.*.periode_tahun.min" => "Periode tahun melebihi 2023",
            "transaksi_detail.*.periode_tahun.max" => "Periode tahun melebihi " . date('Y'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(
            ['message' => 'Validation error', 'status' => 422, 'errors' => $validator->errors()],
            422
        ));
    }
}
