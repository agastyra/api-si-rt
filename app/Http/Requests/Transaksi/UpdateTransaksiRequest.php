<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateTransaksiRequest extends FormRequest
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
            "no_transaksi" => ["required", "string", "size:6", Rule::unique("transaksis", "no_transaksi")
                ->ignore($this->transaksis, "id")
                ->whereNull("deleted_at")],
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
