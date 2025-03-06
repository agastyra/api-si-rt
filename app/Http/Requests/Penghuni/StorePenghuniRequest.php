<?php

namespace App\Http\Requests\Penghuni;

use App\Enum\StatusPenghuni;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StorePenghuniRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {

        return [
            "nama_lengkap" => ["required", "string", "max:100", "min:3"],
            "foto_ktp" => ["required", "image", "mimes:jpg,jpeg,png", "max:2048"],
            "status_penghuni" => ["required", "string", Rule::enum(StatusPenghuni::class)],
            "nomor_telepon" => [
                "required", "string",
                Rule::unique('penghunis', 'nomor_telepon')
                    ->ignore($this->penghuni)
                    ->whereNull("deleted_at"),
                "max:13"],
            "jenis_kelamin" => ["required", "string", "in:Laki-laki,Perempuan"],
            "menikah" => ["required", "boolean"],
        ];
    }

    public function messages(): array
    {
        return [
            "nama_lengkap.required" => "Nama lengkap harus diisi",
            "nama_lengkap.string" => "Nama lengkap harus berupa string",
            "nama_lengkap.max" => "Nama lengkap maksimal 100 karakter",
            "nama_lengkap.min" => "Nama lengkap minimal 3 karakter",
            "foto_ktp.required" => "Foto KTP harus diupload",
            "foto_ktp.image" => "Foto KTP harus berupa gambar",
            "foto_ktp.mimes" => "Foto KTP harus berupa jpg, jpeg, atau png",
            "foto_ktp.max" => "Foto KTP maksimal 2MB",
            "nomor_telepon.required" => "Nomor telepon harus diisi",
            "nomor_telepon.unique" => "Nomor telepon sudah terdaftar",
            "nomor_telepon.max" => "Nomor telepon maksimal 13 digit",
            "jenis_kelamin.required" => "Jenis kelamin harus diisi",
            "jenis_kelamin.in" => "Jenis kelamin tidak valid",
            "menikah.required" => "Status menikah harus diisi",
            "menikah.boolean" => "Status menikah harus berupa boolean",
            "status_penghuni.required" => "Status penghuni harus diisi",
            "status_penghuni.string" => "Status penghuni harus berupa string",
            "status_penghuni.enum" => "Status penghuni tidak valid",
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
