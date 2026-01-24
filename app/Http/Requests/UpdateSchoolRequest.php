<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
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
        $schoolId = $this->route('school')->id;
        return [
            "name"=> [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('schools', 'name')->ignore($schoolId)
            ],
            'email' => [
                'sometimes',
                'required',
                'nullable',
                'email',
            ],
            'password' => [
                'sometimes',
                'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe otro colegio con este nombre.',
            'name.required' => 'El nombre del colegio es obligatorio.',
            'email.unique' => 'Este email ya está siendo usado por otro colegio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos :min caracteres.',
            'password.letters'  => 'La contraseña debe contener letras.',
            'password.mixed'    => 'La contraseña debe tener mayúsculas y minúsculas.',
            'password.numbers'  => 'La contraseña debe incluir al menos un número.',
            'password.symbols'  => 'La contraseña debe incluir al menos un símbolo.',
            'password.uncompromised' => 'La contraseña es insegura por filtraciones previas. Por favor, cambiela.',
        ];
    }
}
