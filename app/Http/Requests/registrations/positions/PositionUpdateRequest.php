<?php

namespace App\Http\Requests\registrations\positions;

use Illuminate\Foundation\Http\FormRequest;

class PositionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $positionId = $this->route('id');

        return [
            'name' => 'required|string|max:255|unique:positions,name,'.$positionId,
            'department_id' => 'nullable|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do cargo é obrigatório.',
            'name.string' => 'O nome do cargo deve ser um texto válido.',
            'name.max' => 'O nome do cargo não pode ter mais de 255 caracteres.',
            'name.unique' => 'Já existe um cargo com este nome.',
            'department_id.exists' => 'O departamento selecionado não existe.',
        ];
    }
}
