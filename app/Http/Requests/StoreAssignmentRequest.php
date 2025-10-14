<?php

namespace App\Http\Requests;

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
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'due_date' => ['required', 'date', 'after:today'],
            'max_points' => ['required', 'integer', 'min:1'],
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
            'subject_id.required' => 'La materia es requerida.',
            'subject_id.exists' => 'La materia seleccionada no existe.',
            'title.required' => 'El título es requerido.',
            'title.string' => 'El título debe ser un texto válido.',
            'title.max' => 'El título no debe superar los 255 caracteres.',
            'description.required' => 'La descripción es requerida.',
            'description.string' => 'La descripción debe ser un texto válido.',
            'due_date.required' => 'La fecha de entrega es requerida.',
            'due_date.date' => 'La fecha de entrega debe ser una fecha válida.',
            'due_date.after' => 'La fecha de entrega debe ser posterior a hoy.',
            'max_points.required' => 'Los puntos máximos son requeridos.',
            'max_points.integer' => 'Los puntos máximos deben ser un número entero.',
            'max_points.min' => 'Los puntos máximos deben ser al menos 1.',
        ];
    }
}
