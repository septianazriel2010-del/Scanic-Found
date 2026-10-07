<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ItemReport;

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
            'category' => ['required', 'string', Rule::in(array_merge(ItemReport::CATEGORIES, [$this->route('itemReport')->category]))],
            'location' => ['required', 'string', Rule::in(array_merge(array_merge(...array_values(ItemReport::LOCATION_GROUPS)), [$this->route('itemReport')->location]))],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', 'in:open,claimed,returned,closed'],
        ];
    }
}
