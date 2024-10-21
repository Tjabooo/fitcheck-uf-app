@extends('layouts.app')

@section('title', 'Email Verification - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>E-post verifikation</h2>
    @if ($verified)
        <p>Din e-post har verifierats! Du kan nu <a href="{{ route('login') }}">logga in</a>.</p>
    @else
        @if ($errors->has('verification_err'))
            <div class="error-message">{{ $errors->first('verification_err') }}</div>
        @endif
    @endif
</div>
@endsection
