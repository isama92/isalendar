<?php

namespace App\Http\Requests;

use App\Concerns\SanitizesHtml;
use App\Rules\CalendarBelongsToUser;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventTemplateRequest extends FormRequest
{
    use SanitizesHtml;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('event_template'));
    }

    protected function prepareForValidation(): void
    {
        $allDay = $this->boolean('event_all_day');
        $tz = $allDay ? null : $this->input('event_timezone');

        $this->merge([
            'event_timezone' => $tz,
            'event_description' => $this->sanitizeHtml($this->input('event_description')),
        ]);
    }

    /**
     * @return array<string, array<mixed>|\Illuminate\Contracts\Validation\ValidationRule|string>
     */
    public function rules(): array
    {
        return [
            'calendar_id' => ['required', 'exists:calendars,id', new CalendarBelongsToUser],
            'name' => ['required', 'string', 'max:255'],
            'event_title' => ['required', 'string', 'max:255'],
            'event_all_day' => ['boolean'],
            'event_timezone' => ['nullable', 'required_if:event_all_day,false', 'prohibited_if:event_all_day,true', 'string', 'timezone:all'],
            'event_description' => ['nullable', 'string'],
        ];
    }
}
