<?php

namespace App\Http\Requests\timeManagement\timeTracking;

use Illuminate\Foundation\Http\FormRequest;

class TimeTrackingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'collaborator_id' => 'required|exists:collaborators,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'time_observation' => 'nullable|string|max:30',
        ];
    }

    public function attributes(): array
    {
        return [
            'collaborator_id' => 'colaborador',
            'date' => 'data',
            'time' => 'horário',
            'time_observation' => 'observação',
        ];
    }

    public function messages(): array
    {
        return [
            'collaborator_id.required' => 'O campo :attribute é obrigatório.',
            'collaborator_id.exists' => 'O :attribute selecionado não existe.',
            'date.required' => 'O campo :attribute é obrigatório.',
            'date.date' => 'O campo :attribute deve ser uma data válida.',
            'time.required' => 'O campo :attribute é obrigatório.',
            'time.date_format' => 'O :attribute deve estar no formato HH:MM.',
            'time_observation.string' => 'O campo :attribute deve ser um texto.',
            'time_observation.max' => 'O campo :attribute não pode ter mais de :max caracteres.',
        ];
    }
}
