<?php

namespace App\Http\Requests\timeManagement\timeTracking;

use Illuminate\Foundation\Http\FormRequest;

class TimeTrackingUpdateRequest extends FormRequest
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
        return [
            'tracking_id' => 'required|exists:time_tracking,id',
            'time_slot_type' => 'required|in:entry_time_1,return_time_1,entry_time_2,return_time_2',
            'time' => 'required|date_format:H:i',
            'observation' => 'nullable|string|max:30'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tracking_id' => 'ID do registro',
            'time_slot_type' => 'tipo de horário',
            'time' => 'horário',
            'observation' => 'observação'
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tracking_id.required' => 'O campo :attribute é obrigatório.',
            'tracking_id.exists' => 'O :attribute não existe.',
            'time_slot_type.required' => 'O campo :attribute é obrigatório.',
            'time_slot_type.in' => 'O :attribute deve ser um dos tipos válidos.',
            'time.required' => 'O campo :attribute é obrigatório.',
            'time.date_format' => 'O :attribute deve estar no formato HH:MM.',
            'observation.string' => 'O campo :attribute deve ser um texto.',
            'observation.max' => 'O campo :attribute não pode ter mais de :max caracteres.'
        ];
    }
}
