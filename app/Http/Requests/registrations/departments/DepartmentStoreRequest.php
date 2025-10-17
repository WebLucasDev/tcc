<?php

namespace App\Http\Requests\registrations\departments;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:departments,name',
        ];
    }

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
