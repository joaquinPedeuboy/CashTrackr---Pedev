@extends('layouts.auth')
@section('title')
    Confirma tu Cuenta
@endsection
@section('auth-contents')
    <p>Tu cuenta fue creada con exito, Ahora solo debes confirmar tu cuenta, revisa tu email</p>

    <form method="POST" action="{{ route('verification.send') }}">
        <input
            type="submit"
            class="bg-amber-500 w-full text-center mt-5 px-5 py-2 uppercase font-bold cursor-pointer"
            value="Reenviar Correo de Confirmación"
        />
    </form>
@endsection