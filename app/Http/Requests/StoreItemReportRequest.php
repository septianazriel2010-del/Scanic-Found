<?php

namespace App\Http\Requests;

use App\Models\ItemReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Controller memastikan user login lewat middleware 'auth',
        // jadi di sini cukup true. Otorisasi detail ada di ItemReportPolicy.
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:lost,found'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'category' => ['required', 'string', Rule::in(ItemReport::CATEGORIES)],
            'location' => ['required', 'string', Rule::in(array_merge(...array_values(ItemReport::LOCATION_GROUPS)))],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            // Validasi upload foto: hanya gambar, maksimal 2MB.
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
