<?php

namespace App\Http\Requests;

use App\Models\Calendar;
use App\Models\Connector;
use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarRequest extends FormRequest
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
        $connector = ensure($this->connector, Connector::class);
        $calendarIds = $connector->calendars()->pluck('external_id')->join(',');

        return [
            'external_id' => ['required', 'string', 'max:255', 'not_in:'.$calendarIds],
        ];
    }
}
