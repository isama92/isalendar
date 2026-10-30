<?php

namespace App\Http\Requests;

use App\Models\Calendar;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Calendar::class);
    }

    /**
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            'provider_id' => ['required', 'exists:providers,id'],
        ];
    }
}
