<?php

namespace App\Http\Requests\TipeTransaksi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreTipeTransaksiRequest extends FormRequest
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
            "nama" => ["required", "string", "max:100", "min:3", Rule::unique("tipe_transaksis", "nama")
                ->ignore($this->tipe_transaksi)
                ->whereNull("deleted_at")
            ],
            "jenis" => ["required", "string", "in:Pemasukan,Pengeluaran"],
        ];
    }

    public function messages(): array
    {
        return [
            "nama.required" => "Nama harus diisi",
            "nama.string" => "Nama harus berupa string",
            "nama.max" => "Nama maksimal 100 karakter",
            "nama.min" => "Nama minimal 3 karakter",
            "nama.unique" => "Nama sudah terdaftar",
            "jenis.required" => "Jenis harus diisi",
            "jenis.string" => "Jenis harus berupa string",
            "jenis.in" => "Jenis tidak valid",
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
