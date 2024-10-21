@extends('layouts.app')

@section('title', 'E-post verifikation - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>E-post verifikation</h2>
    @if (session('message'))
        <div class="success-message">{{ session('message') }}</div>
    @endif
    @if ($errors->has('resend_limit'))
        <div class="error-message">{{ $errors->first('resend_limit') }}</div>
    @endif
    <p>Kontrollera din e-post för att verifiera ditt konto.</p>
    <p>Om du inte har fått e-postmeddelandet kan du begära ett nytt.</p>
    <form action="{{ route('auth.verify.resend') }}" method="post">
        @csrf
        <div class="form-group">
            <button class="main-button-design" type="submit">Skicka verifierings e-post igen</button>
        </div>
    </form>
</div>
@endsection
