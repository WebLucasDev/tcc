<?php

namespace App\Http\Requests\web\registrations\positions;

use Illuminate\Foundation\Http\FormRequest;

class PositionUpdateRequest extends FormRequest
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
        $positionId = $this->route('id');

        return [
            'name' => 'required|string|max:255|unique:positions,name,' . $positionId,
            'department_id' => 'nullable|exists:departments,id',
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
            'name.required' => 'O nome do cargo é obrigatório.',
            'name.string' => 'O nome do cargo deve ser um texto válido.',
            'name.max' => 'O nome do cargo não pode ter mais de 255 caracteres.',
            'name.unique' => 'Já existe um cargo com este nome.',
            'department_id.exists' => 'O departamento selecionado não existe.',
        ];
    }
}
