<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('event'));
    }

    protected function prepareForValidation(): void
    {
        if ($this->boolean('all_day')) {
            $merge = ['ends_at' => null];

            if ($this->filled('starts_at')) {
                $merge['starts_at'] = Carbon::parse($this->input('starts_at'))->startOfDay()->toDateTimeString();
            }

            $this->merge($merge);
        }
    }

    /**
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'all_day' => ['boolean'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'required_if:all_day,false', 'date', 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string'],
        ];
    }
}
