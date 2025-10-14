<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePaymentReceiptRequest extends FormRequest
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
            'status' => ['required', 'in:validated,rejected'],
            'rejection_reason' => ['required_if:status,rejected', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'El estado es requerido.',
            'status.in' => 'El estado debe ser: validado o rechazado.',
            'rejection_reason.required_if' => 'El motivo de rechazo es requerido cuando el estado es rechazado.',
            'rejection_reason.string' => 'El motivo de rechazo debe ser un texto válido.',
            'rejection_reason.max' => 'El motivo de rechazo no debe superar los 500 caracteres.',
        ];
    }
}
