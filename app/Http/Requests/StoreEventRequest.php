<?php

namespace App\Http\Requests;

use App\Concerns\NormalizesEventTimestamps;
use App\Concerns\SanitizesHtml;
use App\Models\Event;
use App\Rules\CalendarBelongsToUser;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    use NormalizesEventTimestamps;
    use SanitizesHtml;

    public function authorize(): bool
    {
        return $this->user()->can('create', Event::class);
    }

    protected function prepareForValidation(): void
    {
        $allDay = $this->boolean('all_day');
        $tz = $allDay ? null : $this->input('timezone');

        $this->merge([
            'timezone' => $tz,
            'starts_at' => $this->normalizeEventTimestamp($this->input('starts_at'), $allDay, $tz),
            'ends_at' => $this->normalizeEventTimestamp($this->input('ends_at'), $allDay, $tz),
            'description' => $this->sanitizeHtml($this->input('description')),
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
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'timezone' => ['nullable', 'required_if:all_day,false', 'prohibited_if:all_day,true', 'string', 'timezone:all'],
            'description' => ['nullable', 'string'],
        ];
    }
}
