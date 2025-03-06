<?php

namespace App\Http\Requests\Rumah;

use App\Enum\StatusRumah;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreRumahRequest extends FormRequest
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
            "blok" => ["required", "string", "max:5", "min:3", "unique:rumahs,blok"],
            "status_rumah" => ["required", "string", Rule::enum(StatusRumah::class)],
        ];
    }

    public function messages(): array
    {
        return [
            "blok.required" => "Blok harus diisi",
            "blok.string" => "Blok harus berupa string",
            "blok.max" => "Blok maksimal 5 karakter",
            "blok.min" => "Blok minimal 3 karakter",
            "blok.unique" => "Blok sudah terdaftar",
            "status_rumah.required" => "Status rumah harus diisi",
            "status_rumah.string" => "Status rumah harus berupa string",
            "status_rumah.enum" => "Status rumah tidak valid",
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
