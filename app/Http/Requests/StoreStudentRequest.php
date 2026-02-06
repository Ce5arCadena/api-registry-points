<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
        $userAuth = Auth::user();
        return [
            'name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'document' => [
                'required',
                'numeric',
                'digits_between:6,12',
                Rule::unique('students')->where(fn (Builder $query) => $query->where('school_id', $userAuth->school_id))
            ],
            'phone' => 'required|numeric|digits_between:10,15',
            'grade' => [
                'required',
                'numeric',
                Rule::exists('grades', 'id')->where(function (Builder $query) use ($userAuth) {
                    $query->where('school_id', $userAuth->school_id);  
                })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.max' => 'El apellido no puede tener más de 255 caracteres.',
            'document.required' => 'El documento es obligatorio.',
            'document.numeric' => 'El documento debe ser un número.',
            'document.digits_between' => 'El documento debe tener entre 6 y 12 dígitos.',
            'document.unique' => 'Ya existe un alumno con este número de documento.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.numeric' => 'El teléfono debe ser un número.',
            'phone.digits_between' => 'El teléfono debe tener entre 10 y 15 dígitos.',
            'grade.required' => 'El curso es obligatorio.',
            'grade.numeric' => 'El curso debe ser un número.',
            'grade.exists' => 'El curso especificado no existe.'
        ];
    }
}
