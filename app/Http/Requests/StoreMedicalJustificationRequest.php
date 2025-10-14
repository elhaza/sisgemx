<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalJustificationRequest extends FormRequest
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
            'student_id' => ['required', 'exists:students,id'],
            'absence_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:500'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
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
            'student_id.required' => 'El estudiante es requerido.',
            'student_id.exists' => 'El estudiante seleccionado no existe.',
            'absence_date.required' => 'La fecha de ausencia es requerida.',
            'absence_date.date' => 'La fecha de ausencia debe ser una fecha válida.',
            'reason.required' => 'El motivo es requerido.',
            'reason.string' => 'El motivo debe ser un texto válido.',
            'reason.max' => 'El motivo no debe superar los 500 caracteres.',
            'document_file.file' => 'El documento debe ser un archivo válido.',
            'document_file.mimes' => 'El documento debe ser un archivo PDF, JPG, JPEG o PNG.',
            'document_file.max' => 'El documento no debe superar los 5MB.',
        ];
    }
}
