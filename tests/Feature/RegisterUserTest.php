<?php

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

