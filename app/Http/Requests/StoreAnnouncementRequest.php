<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'target_audience' => ['required', 'array'],
            'target_audience.*' => ['in:students,parents'],
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
            'title.required' => 'El título es requerido.',
            'title.string' => 'El título debe ser un texto válido.',
            'title.max' => 'El título no debe superar los 255 caracteres.',
            'content.required' => 'El contenido es requerido.',
            'content.string' => 'El contenido debe ser un texto válido.',
            'target_audience.required' => 'El público objetivo es requerido.',
            'target_audience.array' => 'El público objetivo debe ser un arreglo.',
            'target_audience.*.in' => 'El público objetivo debe ser: estudiantes o padres.',
        ];
    }
}
