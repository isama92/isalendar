<?php

namespace App\Http\Requests;

use App\Models\Calendar;
use Illuminate\Foundation\Http\FormRequest;

class StoreProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Calendar::class);
    }

    /**
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'provider_id' => ['required', 'exists:providers,id'],
        ];
    }
}
