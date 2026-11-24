<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKlaimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'laporan_id' => ['required', 'integer', 'exists:laporans,id'],
            'bukti_kepemilikan' => ['required', 'string', 'min:10', 'max:5000'],
            'foto_bukti' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'extensions:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
