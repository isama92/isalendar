<?php

use App\Models\Calendar;
use App\Models\Connector;
use App\Models\User;
use App\Rules\CalendarBelongsToUser;
use Illuminate\Support\Facades\Validator;

use function Pest\Laravel\actingAs;

uses()->group('rules');

it('passes when calendar belongs to authenticated user', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);

    actingAs($user);

    $validator = Validator::make(
        ['calendar_id' => $calendar->id],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator->passes())->toBeTrue();
});

it('fails when calendar does not belong to authenticated user', function (): void {
    $user = User::factory()->create();
    $otherConnector = Connector::factory()->for(User::factory()->create())->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($otherConnector);

    actingAs($user);

    $validator = Validator::make(
        ['calendar_id' => $calendar->id],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator)
        ->fails()->toBeTrue()
        ->errors()->has('calendar_id')->toBeTrue();
});

it('fails when calendar does not exist', function ($calendarId): void {
    $user = User::factory()->create();

    actingAs($user);

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
    $user = User::factory()->create();

    actingAs($user);

    $validator = Validator::make(
        ['calendar_id' => ''],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator->passes())->toBeTrue();
});

it('fails when calendar belongs to different user', function (): void {
    $user = User::factory()->create();
    $connector2 = Connector::factory()->for(User::factory()->create())->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector2);

    actingAs($user);

    $validator = Validator::make(
        ['calendar_id' => $calendar->id],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator->fails())->toBeTrue();
});

it('fails with soft deleted calendars', function (): void {
    $user = User::factory()->create();
    $connector = Connector::factory()->for($user)->create();
    $calendar = Calendar::factory()->create();
    $calendar->connectors()->attach($connector);
    $calendar->delete();

    actingAs($user);

    $validator = Validator::make(
        ['calendar_id' => $calendar->id],
        ['calendar_id' => new CalendarBelongsToUser]
    );

    expect($validator->fails())->toBeTrue();
});
