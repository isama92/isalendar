<?php

namespace App\Http\Requests;

use App\Concerns\NormalizesEventTimestamps;
use App\Models\Event;
use App\Rules\CalendarBelongsToUser;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class StoreBatchEventRequest extends FormRequest
{
    use NormalizesEventTimestamps;

    public function authorize(): bool
    {
        return $this->user()->can('create', Event::class);
    }

    protected function prepareForValidation(): void
    {
        $allDay = $this->boolean('all_day');
        $tz = $allDay ? null : $this->input('timezone');
        $description = $this->input('description');

        /** @var array<int, mixed> $dates */
        $dates = $this->input('dates', []);
        $normalizedDates = collect($dates)
            ->map(fn ($dateEntry): array => [
                'starts_at' => $this->normalizeEventTimestamp($dateEntry['starts_at'] ?? null, $allDay, $tz),
                'ends_at' => $this->normalizeEventTimestamp($dateEntry['ends_at'] ?? null, $allDay, $tz),
            ])
            ->toArray();

        $this->merge([
            'timezone' => $tz,
            'dates' => $normalizedDates,
            'description' => is_string($description) ? strip_tags($description, '<p><br><a><strong><em><ul><ol><li><b><i><u>') : $description,
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
            'timezone' => ['nullable', 'required_if:all_day,false', 'prohibited_if:all_day,true', 'string', 'timezone:all'],
            'description' => ['nullable', 'string'],
            'dates' => ['required', 'array', 'min:1'],
            'dates.*.starts_at' => ['required', 'date'],
            'dates.*.ends_at' => ['nullable', 'date', 'after_or_equal:dates.*.starts_at'],
        ];
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    public function attributes(): array
    {
        return [
            'dates.*.starts_at' => __('events.starts_at'),
            'dates.*.ends_at' => __('events.ends_at'),
        ];
    }
}
