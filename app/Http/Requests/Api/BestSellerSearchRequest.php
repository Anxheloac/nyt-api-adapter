<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BestSellerSearchRequest extends FormRequest
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
            'author' => 'nullable|string',
            'isbn' => 'nullable|array',
            'isbn.*' => 'string',
            'title' => 'nullable|string',
            'offset' => 'nullable|integer|multiple_of:20'
        ];
    }

    /**
     * @return mixed
     */
    public function filledFields()
    {
        return array_filter($this->validated());
    }
}
