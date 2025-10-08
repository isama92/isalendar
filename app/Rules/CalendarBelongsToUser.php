<?php

namespace App\Rules;

use App\Models\Calendar;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Gate;

class CalendarBelongsToUser implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $calendar = Calendar::find($value);

        if (! $calendar instanceof Calendar) {
            $fail(__('validation.not_valid'));

            return;
        }

        if (Gate::denies('view', $calendar)) {
            $fail(__('validation.not_valid'));
        }
    }
}
