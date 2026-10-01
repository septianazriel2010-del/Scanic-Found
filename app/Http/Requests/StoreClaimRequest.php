<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proof_details' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_details.min' => 'Jelaskan bukti kepemilikan minimal 20 karakter agar admin bisa memverifikasi dengan yakin.',
        ];
    }
}
