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
            'claimant_full_name' => ['required', 'string', 'max:255'],
            'claimant_class_position' => ['required', 'string', 'max:120'],
            'proof_details' => ['required', 'string', 'min:20', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'claimant_full_name.required' => 'Nama lengkap wajib diisi.',
            'claimant_class_position.required' => 'Kelas atau jabatan wajib diisi.',
            'proof_details.min' => 'Jelaskan bukti kepemilikan minimal 20 karakter agar admin bisa memverifikasi dengan yakin.',
        ];
    }
}
