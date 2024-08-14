<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeTaskParentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Add any authorization logic here
    }

    public function rules()
    {
        return [
            'parent_id' => 'nullable|exists:tasks,id', // Validate parent_id as nullable and existing task ID
        ];
    }
}
