<?php

namespace App\Http\Requests\web\registrations\workHours;

use Illuminate\Foundation\Http\FormRequest;

class WorkHoursStoreRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255|unique:work_hours,name',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:ativo,inativo',
        ];

        // Adicionar regras para cada dia da semana
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($days as $day) {
            $rules[$day . '_active'] = 'sometimes|boolean';
            $rules[$day . '_entry_1'] = 'nullable|date_format:H:i|required_with:' . $day . '_exit_1';
            $rules[$day . '_exit_1'] = 'nullable|date_format:H:i|required_with:' . $day . '_entry_1|after:' . $day . '_entry_1';
            $rules[$day . '_entry_2'] = 'nullable|date_format:H:i|required_with:' . $day . '_exit_2';
            $rules[$day . '_exit_2'] = 'nullable|date_format:H:i|required_with:' . $day . '_entry_2|after:' . $day . '_entry_2';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome da jornada é obrigatório.',
            'name.unique' => 'Já existe uma jornada com este nome.',
            'name.max' => 'O nome da jornada não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'O status deve ser "ativo" ou "inativo".',

            // Mensagens para horários
            '*.date_format' => 'O horário deve estar no formato HH:MM.',
            '*.required_with' => 'Este campo é obrigatório quando o horário correspondente é informado.',
            '*.after' => 'O horário de saída deve ser posterior ao horário de entrada.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        $attributes = [
            'name' => 'nome da jornada',
            'description' => 'descrição',
            'status' => 'status',
        ];

        $days = [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo'
        ];

        foreach ($days as $day => $dayName) {
            $attributes[$day . '_active'] = $dayName . ' ativo';
            $attributes[$day . '_entry_1'] = $dayName . ' - Entrada 1';
            $attributes[$day . '_exit_1'] = $dayName . ' - Saída 1';
            $attributes[$day . '_entry_2'] = $dayName . ' - Entrada 2';
            $attributes[$day . '_exit_2'] = $dayName . ' - Saída 2';
        }

        return $attributes;
    }
}
