<?php

namespace App\Http\Requests\web\registrations\departments;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentUpdateRequest extends FormRequest
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
        $departmentId = $this->route('id');

        return [
            'name' => 'required|string|max:255|unique:departments,name,' . $departmentId,
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do departamento é obrigatório.',
            'name.string' => 'O nome do departamento deve ser um texto válido.',
            'name.max' => 'O nome do departamento não pode ter mais de 255 caracteres.',
            'name.unique' => 'Já existe um departamento com este nome.',
        ];
    }
}
