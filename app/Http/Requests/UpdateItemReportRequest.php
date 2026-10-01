<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi kepemilikan sudah dicek via $this->authorize() di controller
        // (ItemReportPolicy@update), jadi di sini fokus ke validasi data saja.
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'category' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:150'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:open,claimed,returned,closed'],
        ];
    }
}
