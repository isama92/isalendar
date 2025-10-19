<?php

use App\Models\User;
use App\Rules\CalendarBelongsToUser;
use Illuminate\Support\Facades\Validator;

use function Tests\Helpers\calendarFactory;
use function Tests\Helpers\connectorFactory;

uses()->group('rules');

it('passes when calendar belongs to authenticated user', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();

    $validator = Validator::make(
        ['calendar_id' => $calendar->id],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator->passes())->toBeTrue();
});

it('fails when calendar does not belong to authenticated user', function (): void {
    actingAsUser();
    $otherConnector = connectorFactory()->for(User::factory())->create();
    $otherCalendar = calendarFactory(connector: $otherConnector)->create();

    $validator = Validator::make(
        ['calendar_id' => $otherCalendar->id],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator)
        ->fails()->toBeTrue()
        ->errors()->has('calendar_id')->toBeTrue();
});

it('fails when calendar does not exist', function ($calendarId): void {
    actingAsUser();

    $validator = Validator::make(
        ['calendar_id' => $calendarId],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator)
        ->fails()->toBeTrue()
        ->errors()->has('calendar_id')->toBeTrue()
        ->errors()->first('calendar_id')
        ->toBe(__('validation.not_valid', ['attribute' => __('validation.attributes.calendar_id')]));
})->with([
    ['calendarId' => 99999],
    ['calendarId' => null],
    ['calendarId' => 'string'],
]);

it('passes with empty string as validation is not triggered', function (): void {
    actingAsUser();

    $validator = Validator::make(
        ['calendar_id' => ''],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator->passes())->toBeTrue();
});

it('fails with soft deleted calendars', function (): void {
    actingAsUser();
    $calendar = calendarFactory()->create();
    $calendar->delete();

    $validator = Validator::make(
        ['calendar_id' => $calendar->id],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator->fails())->toBeTrue();
});
