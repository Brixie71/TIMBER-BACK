<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('post') ? 'required' : 'sometimes';

        return [
            'specimen_name' => [$required, 'string', 'max:255'],
            'test_type' => [$required, 'string', 'max:255'],

            'base' => [$required, 'numeric', 'min:0'],
            'height' => [$required, 'numeric', 'min:0'],
            'length' => [$required, 'numeric', 'min:0'],
            'area' => [$required, 'numeric', 'min:0'],

            'pressure_bar' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'max_force' => [$required, 'numeric', 'min:0'],
            'stress' => ['nullable', 'numeric', 'min:0'],
            'moisture_content' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'species_id' => ['nullable', 'integer'],
            'photo' => ['nullable', 'string'],
        ];
    }
}
