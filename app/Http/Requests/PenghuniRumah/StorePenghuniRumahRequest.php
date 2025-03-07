<?php

namespace App\Http\Requests\PenghuniRumah;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StorePenghuniRumahRequest extends FormRequest
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
            "rumah_id" => ["required", "exists:rumahs,id"],
            "penghuni_id" => ["required", "exists:penghunis,id"],
            "periode_bulan_mulai" => ["nullable", "numeric", "in:1,2,3,4,5,6,7,8,9,10,11,12"],
            "periode_bulan_selesai" => ["nullable", "numeric", "in:1,2,3,4,5,6,7,8,9,10,11,12"],
            "periode_tahun_mulai" => ["nullable", "digits:4", "integer", "min:2023", "max:" . date('Y')],
            "periode_tahun_selesai" => ["nullable", "digits:4", "integer", "min:2023", "max:" . date('Y')],
        ];
    }

    public function messages(): array
    {
        return [
            "rumah_id.required" => "Rumah harus diisi",
            "rumah_id.exists" => "Rumah tidak ditemukan",
            "penghuni_id.required" => "Penghuni harus diisi",
            "penghuni_id.exists" => "Penghuni tidak ditemukan",
            "periode_bulan_mulai.numeric" => "Periode bulan mulai tidak valid",
            "periode_bulan_mulai.in" => "Periode bulan mulai tidak valid",
            "periode_bulan_selesai.numeric" => "Periode bulan selesai tidak valid",
            "periode_bulan_selesai.in" => "Periode bulan selesai tidak valid",
            "periode_tahun_mulai.digits" => "Periode tahun tidak valid",
            "periode_tahun_selesai.digits" => "Periode tahun tidak valid",
            "periode_tahun_mulai.integer" => "Periode tahun tidak valid",
            "periode_tahun_selesai.integer" => "Periode tahun tidak valid",
            "periode_tahun_mulai.min" => "Periode tahun melebihi 2023",
            "periode_tahun_selesai.min" => "Periode tahun melebihi 2023",
            "periode_tahun_mulai.max" => "Periode tahun melebihi " . date('Y'),
            "periode_tahun_selesai.max" => "Periode tahun melebihi " . date('Y'),
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
