<?php

namespace App\Http\Requests\TransaksiDetail;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateTransaksiDetailRequest extends FormRequest
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
            "transaksi_id" => ["required", "exists:transaksis,id"],
            "tipe_transaksi_id" => ["required", "exists:tipe_transaksis,id"],
            "nominal" => ["required", "numeric"],
            "periode_bulan" => ["required", "numeric", "in:1,2,3,4,5,6,7,8,9,10,11,12"],
            "periode_tahun" => ["required", Rule::date()->format("Y")],
        ];
    }

    public function messages(): array
    {
        return [
            "transaksi_id.required" => "Transaksi harus diisi",
            "transaksi_id.exists" => "Transaksi tidak ditemukan",
            "tipe_transaksi_id.required" => "Tipe transaksi harus diisi",
            "tipe_transaksi_id.exists" => "Tipe transaksi tidak ditemukan",
            "nominal.required" => "Nominal harus diisi",
            "nominal.numeric" => "Nominal harus berupa angka",
            "periode_bulan.required" => "Periode bulan harus diisi",
            "periode_bulan.numeric" => "Periode bulan tidak valid",
            "periode_bulan.in" => "Periode bulan tidak valid",
            "periode_tahun.required" => "Periode tahun harus diisi",
            "periode_tahun.date_format" => "Periode tahun tidak valid",
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
