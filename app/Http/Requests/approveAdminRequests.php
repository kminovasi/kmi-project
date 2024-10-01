<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class approveAdminRequests extends FormRequest
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
            "evaluatedBy" => "required|in:innovation admin",
            "status"    => "required|in:accept,reject,replicate,not complete",
        ];
    }
}
