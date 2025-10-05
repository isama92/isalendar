<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Event::class);
    }

    /**
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'all_day' => ['boolean'],
            'starts_at' => ['prohibited_if:all_day,true', 'required_if:all_day,false', 'date'],
            'ends_at' => ['prohibited_if:all_day,true', 'required_if:all_day,false', 'date', 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string'],
        ];
    }
}
