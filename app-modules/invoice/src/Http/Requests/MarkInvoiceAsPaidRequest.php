<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarkInvoiceAsPaidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'payment_method.string' => 'The payment method must be a valid text.',
            'payment_method.max' => 'The payment method must not exceed 50 characters.',
            'notes.string' => 'The notes must be valid text.',
            'notes.max' => 'The notes must not exceed 1000 characters.',
        ];
    }
}
