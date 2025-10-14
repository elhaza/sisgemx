<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentReceiptRequest extends FormRequest
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
            'payment_id' => ['required', 'exists:payments,id'],
            'payment_date' => ['required', 'date'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,transfer,card,check'],
            'receipt_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
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
            'payment_id.required' => 'El pago es requerido.',
            'payment_id.exists' => 'El pago seleccionado no existe.',
            'payment_date.required' => 'La fecha de pago es requerida.',
            'payment_date.date' => 'La fecha de pago debe ser una fecha válida.',
            'amount_paid.required' => 'El monto pagado es requerido.',
            'amount_paid.numeric' => 'El monto pagado debe ser un número.',
            'amount_paid.min' => 'El monto pagado debe ser mayor o igual a cero.',
            'payment_method.required' => 'El método de pago es requerido.',
            'payment_method.in' => 'El método de pago debe ser: efectivo, transferencia, tarjeta o cheque.',
            'receipt_file.required' => 'El comprobante es requerido.',
            'receipt_file.file' => 'El comprobante debe ser un archivo válido.',
            'receipt_file.mimes' => 'El comprobante debe ser un archivo PDF, JPG, JPEG o PNG.',
            'receipt_file.max' => 'El comprobante no debe superar los 5MB.',
        ];
    }
}
