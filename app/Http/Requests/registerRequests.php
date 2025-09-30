<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class registerRequests extends FormRequest
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
            'team_name' => [
                'required',
                Rule::unique('teams')->where(function ($query)  {
                    return $query->where('team_name', $this->input('team_name'))
                                 ->where('status_lomba', $this->input('status_lomba'));
                }),
            ],
            'company' => 'required',
            'fasil' => 'required',
            'category' => 'required',
            'theme' => 'required',
            // 'event' => 'required',
            'status_lomba' => 'required',
            // 'status_event' => 'required',
            'innovation_title' => 'required',
            'leader' => 'required',
            'anggota.*' => 'required',
            'abstract' => 'required',
            'problem' => 'required',
            // 'problem_impact' => 'required',
            'main_cause' => 'required',
            'solution' => 'required',
            // 'outcome' => 'required',
            // 'performance' => 'required',
            'status_inovasi' => 'required',
            'proof_idea' => 'file|mimes:jpeg,jpg,png|max:5120',
            'innovation_photo' => 'file|mimes:jpeg,jpg,png|max:5120'
        ];
    }
}
