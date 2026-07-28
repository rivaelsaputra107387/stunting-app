<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PemeriksaanRequest extends FormRequest
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
            'balita_id'           => 'required|exists:balitas,id',
            'tanggal_pemeriksaan' => 'required|date|before_or_equal:today',
            'tinggi_badan'        => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $balita = \App\Models\Balita::find($this->balita_id);
                    if (!$balita) return;
                    $umur = $balita->umur_bulan;

                    if ($umur <= 6 && ($value < 40 || $value > 80)) {
                        $fail("Tinggi badan untuk bayi 0-6 bulan harus di antara 40 - 80 cm.");
                    } elseif ($umur > 6 && $umur <= 12 && ($value < 55 || $value > 90)) {
                        $fail("Tinggi badan untuk bayi 7-12 bulan harus di antara 55 - 90 cm.");
                    } elseif ($umur > 12 && $umur <= 24 && ($value < 65 || $value > 105)) {
                        $fail("Tinggi badan untuk anak 1-2 tahun harus di antara 65 - 105 cm.");
                    } elseif ($umur > 24 && $umur <= 36 && ($value < 75 || $value > 115)) {
                        $fail("Tinggi badan untuk anak 2-3 tahun harus di antara 75 - 115 cm.");
                    } elseif ($umur > 36 && ($value < 80 || $value > 130)) {
                        $fail("Tinggi badan untuk anak 3-5 tahun harus di antara 80 - 130 cm.");
                    }
                }
            ],
            'berat_badan'         => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $balita = \App\Models\Balita::find($this->balita_id);
                    if (!$balita) return;
                    $umur = $balita->umur_bulan;

                    if ($umur <= 6 && ($value < 1.5 || $value > 12.0)) {
                        $fail("Berat badan untuk bayi 0-6 bulan harus di antara 1.5 - 12 kg.");
                    } elseif ($umur > 6 && $umur <= 12 && ($value < 4.0 || $value > 15.0)) {
                        $fail("Berat badan untuk bayi 7-12 bulan harus di antara 4.0 - 15 kg. Data '{$value} kg' dianggap tidak valid (potensi salah ketik).");
                    } elseif ($umur > 12 && $umur <= 24 && ($value < 6.0 || $value > 20.0)) {
                        $fail("Berat badan untuk anak 1-2 tahun harus di antara 6.0 - 20 kg.");
                    } elseif ($umur > 24 && $umur <= 36 && ($value < 8.0 || $value > 25.0)) {
                        $fail("Berat badan untuk anak 2-3 tahun harus di antara 8.0 - 25 kg.");
                    } elseif ($umur > 36 && ($value < 10.0 || $value > 35.0)) {
                        $fail("Berat badan untuk anak 3-5 tahun harus di antara 10.0 - 35 kg.");
                    }
                }
            ],
            'catatan'             => 'nullable|string|max:1000',
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
            'balita_id'           => 'Balita',
            'tanggal_pemeriksaan' => 'Tanggal Pemeriksaan',
            'tinggi_badan'        => 'Tinggi Badan',
            'berat_badan'         => 'Berat Badan',
            'catatan'             => 'Catatan',
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }
}
