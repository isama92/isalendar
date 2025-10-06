<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();

        return ! is_null($user->calendar) && is_null($user->calendar->external_calendar_id);
    }

    /**
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'calendar_id' => ['required', 'string', 'max:255'],
        ];
    }
}
