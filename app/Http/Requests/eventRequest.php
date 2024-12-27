<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class eventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'event_name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'year' => 'required|integer',
            'company_code' => 'required|array',
            'company_code.*' => 'required|string',
            'type' => 'required|in:AP,internal,group,national,international',
        ];
    }
}
