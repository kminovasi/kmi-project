<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaperRequest extends FormRequest
{
    public function authorize()
    {
        return true; // or implement your authorization logic
    }

    public function rules()
    {
        return [
            'inovasi_lokasi' => 'required|string',
            'full_paper' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'abstract' => 'required|string',
            'problem' => 'required|string',
            'main_cause' => 'required|string',
            'solution' => 'required|string',
            'innovation_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'proof_idea' => 'nullable|file|mimes:mimes:jpeg,png,jpg,gif|max:2048',
            'status_inovasi' => 'required|in:Not Implemented,Progress,Implemented',
            'potensi_replikasi' => 'required|in:Bisa Direplikasi,Tidak Bisa Direplikasi',
        ];
    }
}
