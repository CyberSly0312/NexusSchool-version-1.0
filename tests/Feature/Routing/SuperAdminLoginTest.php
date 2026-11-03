<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Super Admin login page
|--------------------------------------------------------------------------
|
| The central Super Admin panel uses Filament's stock login page. These tests
| lock down the contract that the regression relied on:
|
| - the page is served on the central domain and renders the Filament form;
| - the password field stays masked by default and is revealable;
| - the Livewire bindings needed to submit the password are present;
| - the Filament scripts that boot Alpine are referenced.
|
| The password is never rendered or logged by this page.
|
*/

uses(TestCase::class, RefreshDatabase::class);

test('the central super-admin login page renders the stock Filament login form', function (): void {
    $this->get('http://localhost/super-admin/login')
        ->assertOk()
        ->assertSee('fi-simple-layout', false)
        ->assertSee('wire:submit="authenticate"', false)
        ->assertSee('wire:model="data.email"', false)
        ->assertSee('wire:model="data.password"', false);
});

test('the super-admin password field is masked by default and revealable', function (): void {
    $this->get('http://localhost/super-admin/login')
        ->assertOk()
        ->assertSee('fi-revealable', false)
        ->assertSee('autocomplete="current-password"', false)
        ->assertSee('x-data="{ isPasswordRevealed: false }"', false)
        ->assertSee("x-bind:type=\"isPasswordRevealed ? 'text' : 'password'\"", false)
        ->assertSee('x-on:click="isPasswordRevealed = true"', false)
        ->assertSee('x-on:click="isPasswordRevealed = false"', false)
        // The hide control is hidden until the password is revealed.
        ->assertSee('x-cloak="x-cloak"', false);
});

test('the super-admin login page loads the Filament scripts that boot Alpine', function (): void {
    $this->get('http://localhost/super-admin/login')
        ->assertOk()
        ->assertSee('js/filament/support/support.js', false)
        ->assertSee('js/filament/schemas/schemas.js', false);
});

test('the super-admin login page never renders a password value', function (): void {
    $response = $this->get('http://localhost/super-admin/login');

    $response->assertOk();

    // A password input must never be pre-filled with a value, and no known
    // password literal should ever appear in the HTML.
    expect($response->getContent())
        ->not->toMatch('/id="form\.password"[^>]*\bvalue=/')
        ->not->toContain('ProbePassword123!');
});
