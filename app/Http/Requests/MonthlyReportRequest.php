<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization logic can be added here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'start_date.required' => 'A data inicial é obrigatória.',
            'start_date.date_format' => 'A data inicial deve estar no formato Y-m-d.',
            'end_date.required' => 'A data final é obrigatória.',
            'end_date.date_format' => 'A data final deve estar no formato Y-m-d.',
            'end_date.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }
}
