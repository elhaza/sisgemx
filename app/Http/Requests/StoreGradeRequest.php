<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
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
            'subject_id' => ['required', 'exists:subjects,id'],
            'period' => ['required', 'string'],
            'grade' => ['required', 'numeric', 'between:0,100'],
            'comments' => ['nullable', 'string', 'max:500'],
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
            'subject_id.required' => 'La materia es requerida.',
            'subject_id.exists' => 'La materia seleccionada no existe.',
            'period.required' => 'El período es requerido.',
            'period.string' => 'El período debe ser un texto válido.',
            'grade.required' => 'La calificación es requerida.',
            'grade.numeric' => 'La calificación debe ser un número.',
            'grade.between' => 'La calificación debe estar entre 0 y 100.',
            'comments.string' => 'Los comentarios deben ser un texto válido.',
            'comments.max' => 'Los comentarios no deben superar los 500 caracteres.',
        ];
    }
}
