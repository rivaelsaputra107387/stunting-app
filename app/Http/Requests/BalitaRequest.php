<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BalitaRequest extends FormRequest
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
            'nik'            => [
                'required',
                'string',
                'size:16',
                Rule::unique('balitas')->ignore($this->route('balitum')),
            ],
            'nama'           => 'required|string|max:255',
            'jenis_kelamin'  => 'required|in:L,P',
            'tanggal_lahir'  => 'required|date|before:today',
            'nama_orangtua'  => 'required|string|max:255',
            'alamat'         => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nik'            => 'NIK',
            'nama'           => 'Nama Balita',
            'jenis_kelamin'  => 'Jenis Kelamin',
            'tanggal_lahir'  => 'Tanggal Lahir',
            'nama_orangtua'  => 'Nama Orang Tua',
            'alamat'         => 'Alamat',
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nik.size'              => 'NIK harus terdiri dari 16 digit.',
            'nik.unique'            => 'NIK sudah terdaftar.',
            'tanggal_lahir.before'  => 'Tanggal lahir harus sebelum hari ini.',
        ];
    }
}
