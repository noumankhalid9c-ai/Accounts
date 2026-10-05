<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'reference_type' => 'nullable|string|in:Direct,Vendor',
            'vendor_name' => 'required_if:reference_type,Vendor|string|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:Active,Prospect,Inactive,Suspended',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'vendor_name.required_if' => 'You must select a vendor when the reference source is Vendor.',
        ];
    }
}
