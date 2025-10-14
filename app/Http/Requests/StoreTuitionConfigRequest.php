<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTuitionConfigRequest extends FormRequest
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
            'school_year_id' => ['required', 'exists:school_years,id'],
            'grade_level' => ['required', 'string'],
            'month' => ['required', 'integer', 'between:1,12'],
            'amount' => ['required', 'numeric', 'min:0'],
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
            'school_year_id.required' => 'El año escolar es requerido.',
            'school_year_id.exists' => 'El año escolar seleccionado no existe.',
            'grade_level.required' => 'El nivel de grado es requerido.',
            'grade_level.string' => 'El nivel de grado debe ser un texto válido.',
            'month.required' => 'El mes es requerido.',
            'month.integer' => 'El mes debe ser un número entero.',
            'month.between' => 'El mes debe estar entre 1 y 12.',
            'amount.required' => 'El monto es requerido.',
            'amount.numeric' => 'El monto debe ser un número.',
            'amount.min' => 'El monto debe ser mayor o igual a cero.',
        ];
    }
}
