<?php

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('shows the registration screen', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertStatus(200);
    $response->assertSee('Crear Cuenta');
    $response->assertSeeInOrder([
        'Crear Cuenta',
        'Registrarme'
    ]);

});

it('registers a new user as unverified and dispatches the registered event', function () {

    Event::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $response->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'john@example.com')->first();
    expect($user)->not()->toBeNull();
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
    expect($user->hasVerifiedEmail())->toBeFalse();
    
    Event::assertDispatched(Registered::class);
});

it('should validate required fields when the request body is empty', function () {
    $response = $this->post(route('register.store'), []);

    $response->assertSessionHasErrors(['name', 'email', 'password']);

    $response->assertSessionHasErrors([
        'name'=> 'El nombre es obligatorio', 
        'email' => 'El email es obligatorio', 
        'password' => 'La contraseña es obligatoria'
    ]);
});

it('prevents duplicate email addresses', function () {

    User::factory()->create([
        'email' => 'john@example.com'
    ]);

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors([
        'email' => 'El email ya está registrado', 
    ]);
});

it('sends the verification email notification after registration', function () {
    Notification::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
    ]);

    $user = User::where('email', 'john@example.com')->first();
    
    Notification::assertSentTo($user, VerifyEmail::class);
});

it('Verifies the user email from a signed verification link', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email)
        ]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertRedirect(route('dashboard'));

    expect($user->hasVerifiedEmail())->toBeTrue();
});

it('does not allow an unverified user to access the dashboard', function() {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
});

it('allows a verified user to access the dashboard', function() {
    $user = User::factory()->create([
        'email_verified_at' => now()
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});

