<?php

namespace App\Http\Requests\web\registrations\collaborators;

use Illuminate\Foundation\Http\FormRequest;

class CollaboratorUpdateRequest extends FormRequest
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
        $collaboratorId = $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:collaborators,email,' . $collaboratorId . '|max:255',
            'cpf' => 'required|string|size:14|unique:collaborators,cpf,' . $collaboratorId,
            'admission_date' => 'required|date',
            'phone' => 'required|string|max:20',
            'zip_code' => 'required|string|size:9',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:10',
            'neighborhood' => 'required|string|max:100',
            'position_id' => 'required|exists:positions,id',
            'entry_time_1' => 'required|date_format:H:i',
            'return_time_1' => 'required|date_format:H:i|after:entry_time_1',
            'entry_time_2' => 'nullable|date_format:H:i',
            'return_time_2' => 'nullable|date_format:H:i|after:entry_time_2',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ter um formato válido.',
            'email.unique' => 'Este email já está em uso.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter 14 caracteres (com pontos e hífen).',
            'cpf.unique' => 'Este CPF já está cadastrado.',
            'admission_date.required' => 'A data de admissão é obrigatória.',
            'admission_date.date' => 'A data de admissão deve ser uma data válida.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.max' => 'O telefone não pode ter mais de 20 caracteres.',
            'zip_code.required' => 'O CEP é obrigatório.',
            'zip_code.size' => 'O CEP deve ter 9 caracteres (com hífen).',
            'street.required' => 'O endereço é obrigatório.',
            'street.max' => 'O endereço não pode ter mais de 255 caracteres.',
            'number.required' => 'O número é obrigatório.',
            'number.max' => 'O número não pode ter mais de 10 caracteres.',
            'neighborhood.required' => 'O bairro é obrigatório.',
            'neighborhood.max' => 'O bairro não pode ter mais de 100 caracteres.',
            'position_id.required' => 'O cargo é obrigatório.',
            'position_id.exists' => 'O cargo selecionado não existe.',
            'entry_time_1.required' => 'O horário de entrada é obrigatório.',
            'entry_time_1.date_format' => 'O horário de entrada deve ter o formato HH:MM.',
            'return_time_1.required' => 'O horário de saída é obrigatório.',
            'return_time_1.date_format' => 'O horário de saída deve ter o formato HH:MM.',
            'return_time_1.after' => 'O horário de saída deve ser posterior ao horário de entrada.',
            'entry_time_2.date_format' => 'O horário de entrada 2 deve ter o formato HH:MM.',
            'return_time_2.date_format' => 'O horário de saída 2 deve ter o formato HH:MM.',
            'return_time_2.after' => 'O horário de saída 2 deve ser posterior ao horário de entrada 2.',
        ];
    }
}
