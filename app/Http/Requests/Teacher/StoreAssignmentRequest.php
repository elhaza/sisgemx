<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
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
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after_or_equal:today',
            'max_points' => 'nullable|numeric|min:0',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'subject_id.required' => 'Debe seleccionar una materia',
            'subject_id.exists' => 'La materia seleccionada no existe',
            'title.required' => 'El título es requerido',
            'title.max' => 'El título no puede exceder 255 caracteres',
            'description.required' => 'La descripción es requerida',
            'due_date.required' => 'La fecha de vencimiento es requerida',
            'due_date.after_or_equal' => 'La fecha de vencimiento debe ser hoy o en el futuro',
            'attachment.file' => 'El archivo debe ser un archivo válido',
            'attachment.max' => 'El archivo no puede exceder 10MB',
        ];
    }
}
