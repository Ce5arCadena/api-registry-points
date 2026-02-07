<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;

class StorePointCategoryRequest extends FormRequest
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
        $auth = Auth::user();
        return [
            'max_points' => 'required|numeric|min:1',
            'subject' => [
                'required',
                'numeric',
                Rule::exists('subjects', 'id')->where(fn (Builder $query) => $query->where('school_id', $auth->school_id)) 
            ],
            'name' => [
                'required',
                'max:255',
                Rule::unique('point_categories')
                    ->where(fn (Builder $query) => $query->where('school_id', $auth->school_id)->where('subject_id', $this->input('subject')))
            ]
        ];
    }

    public function messages()
    {
        return [
            'max_points.required' => 'El puntaje máximo es obligatorio.',
            'max_points.numeric' => 'El puntaje máximo debe ser un número.',
            'max_points.min' => 'El puntaje máximo debe ser mayor o igual a 1.',
            'subject.required' => 'La asignatura es obligatoria.',
            'subject.exists' => 'La asignatura seleccionada no es válida.',
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',
            'name.unique' => 'Ya existe una categoría con este nombre para esta asignatura en tu institución.',
        ];
    }
}
