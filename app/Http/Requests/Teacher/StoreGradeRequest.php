<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role->value === 'teacher' || auth()->user()->role->value === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'period' => 'required|string|max:50',
            'grade' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'student_id.required' => 'Debe seleccionar un estudiante',
            'student_id.exists' => 'El estudiante seleccionado no existe',
            'subject_id.required' => 'Debe seleccionar una materia',
            'subject_id.exists' => 'La materia seleccionada no existe',
            'period.required' => 'Debe especificar el período',
            'grade.required' => 'Debe ingresar la calificación',
            'grade.numeric' => 'La calificación debe ser un número',
            'grade.min' => 'La calificación no puede ser menor a 0',
            'grade.max' => 'La calificación no puede ser mayor a 100',
            'comments.max' => 'Los comentarios no pueden exceder 500 caracteres',
        ];
    }
}
