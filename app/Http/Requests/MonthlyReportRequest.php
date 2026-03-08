<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'data_inicial' => 'required|date_format:Y-m-d',
            'data_final' => 'required|date_format:Y-m-d|after_or_equal:data_inicial',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'data_inicial.required' => 'A data inicial é obrigatória.',
            'data_inicial.date_format' => 'A data inicial deve estar no formato YYYY-MM-DD.',
            'data_final.required' => 'A data final é obrigatória.',
            'data_final.date_format' => 'A data final deve estar no formato YYYY-MM-DD.',
            'data_final.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }
}
