@extends('layouts.app')

@section('title', 'Registreringsbekräftelse - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Registrering lyckad!</h2>
    <p>Vi har skickat ett verifieringsmail till din e-postadress. Kontrollera din inkorg. Gå till <a href="{{ route('login') }}">inloggningssidan</a> när du är klar.</p>

    <form action="{{ route('auth.verify.resend') }}" method="post">
        @csrf
        <div class="form-group">
            <button class="main-button-design" type="submit" id="resend-button">Skicka verifierings e-post igen</button>
        </div>
    </form>

    @if ($errors->has('email_err'))
        <div class="error-message">{{ $errors->first('email_err') }}</div>
    @endif

    @if (session('message'))
        <div class="success-message">{{ session('message') }}</div>
    @endif
</div>
@endsection
