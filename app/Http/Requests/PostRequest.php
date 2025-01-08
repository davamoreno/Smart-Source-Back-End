<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->exists && auth()->user()->faculty()->exists;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'paper_type_id' => 'nullable|exists:paper_types,id',
            'file' => 'nullable|file|mimes:pdf,docx|max:2048',
            'status' => 'nullable|string|in:pending'
        ];
    }
}
