<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClaimStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang boleh sampai ke sini; dicek juga di
        // Admin\ClaimController lewat $this->authorize('review', $claim).
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:approved,rejected'],
            'review_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
