<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'school_name' => ['nullable', 'string', 'max:255'],
            'school_logo' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:2048'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'school_name.max' => 'El nombre de la escuela no debe exceder 255 caracteres.',
            'school_logo.image' => 'El archivo debe ser una imagen.',
            'school_logo.mimes' => 'La imagen debe estar en formato JPEG, PNG, GIF o WEBP.',
            'school_logo.max' => 'La imagen no debe exceder 2MB.',
            'logo.image' => 'El archivo debe ser una imagen.',
            'logo.mimes' => 'La imagen debe estar en formato JPEG, PNG, GIF o WEBP.',
            'logo.max' => 'La imagen no debe exceder 2MB.',
        ];
    }
}
