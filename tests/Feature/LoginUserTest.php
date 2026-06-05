<?php

use App\Models\User;

it('shows the login screen', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertStatus(200);
});

it('logs in a verified user successfully', function () {
    User::factory()->create([
        'email' => 'juan@juan.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);

    $response = $this->post(route('login.store'),[
        'email' => 'juan@juan.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
});

it('does not log in invalid credentials', function () {
    User::factory()->create([
        'email' => 'juan2@juan.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->from(route('login'))->post(route('login.store'),[
        'email' => 'juan2@juan.com',
        'password' => 'incorrect-password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas(
        'error',
        'Credenciales Incorrectas'
    );

    $this->assertGuest();
});

it('prevents unverified users from accesing the dashboard', function () {
    User::factory()->unverified()->create([
        'email' => 'juan3@juan.com',
        'password' => bcrypt('password')
    ]);

    $response = $this->post(route('login.store'),[
        'email' => 'juan3@juan.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $dashboardResponse = $this->get(route('dashboard'));
    $dashboardResponse->assertRedirect(route('verification.notice'));
});

it('does not allow access to dashboard if email is not verified', function () {
    $user = User::factory()->create([
        'email_verified_at' => null
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
});

it('allow access to dashboard if email is verified', function () {
    $user = User::factory()->create([
        'email_verified_at' => now()
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});

it('fails login if user does not exist', function () {
    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'noexiste@dominio.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors([
        'email' => 'No encontramos una cuenta con ese email. Por favor, verifica tu email.'
    ]);

    $this->assertGuest();
});