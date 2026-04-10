<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherStatesRequest extends FormRequest
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
            'ids' => "required|array|min:1",
            'ids.*' => "required|integer",
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required'   => 'Los ids son requeridos.',
            'ids.array'      => 'Debe ser un array con los ids.',
            'ids.min'        => 'Debe enviar al menos un id.',
            'ids.*.required' => 'Cada id es requerido.',
            'ids.*.integer'  => 'Cada id debe ser un número entero.',
        ];
    }
}
