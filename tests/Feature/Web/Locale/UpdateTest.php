<?php

use function Pest\Laravel\post;

uses()->group('web', 'locale');

it('updates locale to english', function (): void {
    post(route('locale.update'), ['locale' => 'en'])
        ->assertRedirect()
        ->assertSessionHas('locale', 'en');

    expect(session('locale'))->toBe('en');
});

it('updates locale to italian', function (): void {
    post(route('locale.update'), ['locale' => 'it'])
        ->assertRedirect()
        ->assertSessionHas('locale', 'it');

    expect(session('locale'))->toBe('it');
});

it('redirects back to previous page', function (): void {
    $this->from(route('dashboard'));

    post(route('locale.update'), ['locale' => 'en'])
        ->assertRedirect(route('dashboard'));
});

it('defaults to english for invalid locale', function (string $locale): void {
    post(route('locale.update'), ['locale' => $locale])
        ->assertRedirect()
        ->assertSessionHas('locale', 'en');

    expect(session('locale'))->toBe('en');
})->with([
    ['locale', 'nl'],
    ['locale', ''],
    ['locale', null],
    ['locale', 123],
    ['locale', ['en', 'it']],
    ['locale', 'IT'],
]);

it('defaults to english for missing locale parameter', function (): void {
    post(route('locale.update'))
        ->assertRedirect()
        ->assertSessionHas('locale', 'en');

    expect(session('locale'))->toBe('en');
});

it('overwrites previous locale setting', function (): void {
    session(['locale' => 'it']);

    expect(session('locale'))->toBe('it');

    post(route('locale.update'), ['locale' => 'en'])
        ->assertSessionHas('locale', 'en');

    expect(session('locale'))->toBe('en');
});

it('handles locale with whitespace', function (): void {
    post(route('locale.update'), ['locale' => ' it '])
        ->assertRedirect()
        ->assertSessionHas('locale', 'it');

    expect(session('locale'))->toBe('it');
});

it('does not require authentication', function (): void {
    post(route('locale.update'), ['locale' => 'it'])
        ->assertRedirect()
        ->assertSessionHas('locale', 'it');
});
