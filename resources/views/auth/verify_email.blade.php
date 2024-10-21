@extends('layouts.app')

@section('title', 'Email Verification - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Email Verification</h2>
    @if (isset($verified))
        <p>Your email has been successfully verified! You can now <a href="{{ route('login') }}">log in</a>.</p>
    @else
        @if ($errors->has('verification_err'))
            <div class="error-message">{{ $errors->first('verification_err') }}</div>
        @endif
    @endif
</div>
@endsection
