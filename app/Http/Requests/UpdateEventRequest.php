<?php

namespace App\Http\Requests;

use App\Concerns\NormalizesEventTimestamps;
use App\Rules\CalendarBelongsToUser;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    use NormalizesEventTimestamps;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('event'));
    }

    protected function prepareForValidation(): void
    {
        // TODO: get timezone from UI, let the user select it
        $tz = 'Europe/Amsterdam';
        $allDay = $this->boolean('all_day');

        $this->merge([
            'starts_at' => $this->normalizeEventTimestamp($this->input('starts_at'), $tz, $allDay),
            'ends_at' => $this->normalizeEventTimestamp($this->input('ends_at'), $tz, $allDay),
        ]);
    }

    /**
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'calendar_id' => ['required', 'exists:calendars,id', new CalendarBelongsToUser],
            'title' => ['required', 'string', 'max:255'],
            'all_day' => ['boolean'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'required', 'date', 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string'],
        ];
    }
}
