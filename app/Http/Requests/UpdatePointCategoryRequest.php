<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePointCategoryRequest extends FormRequest
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
            'max_points' => 'sometimes|required|numeric|min:1',
            'subject' => [
                'sometimes',
                'required',
                'numeric',
                Rule::exists('subjects', 'id')->where(fn (Builder $query) => $query->where('school_id', $auth->school_id)) 
            ],
            'name' => [
                'sometimes',
                'required',
                'max:255',
                Rule::unique('point_categories')
                    ->where(fn (Builder $query) => $query->where('school_id', $auth->school_id)->where('subject_id', $this->input('subject')))
            ]
        ];
    }
}
