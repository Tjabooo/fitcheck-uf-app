@extends('layouts.app')

@section('title', 'Email Verification - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Email Verification</h2>
    @if (session('message'))
        <div class="success-message">{{ session('message') }}</div>
    @endif
    @if ($errors->has('resend_limit'))
        <div class="error-message">{{ $errors->first('resend_limit') }}</div>
    @endif
    <p>Please check your email to verify your account.</p>
    <p>If you didn't receive the email, you can request a new one.</p>
    <form action="{{ route('auth.verify.resend') }}" method="post">
        @csrf
        <div class="form-group">
            <button class="main-button-design" type="submit">Resend Verification Email</button>
        </div>
    </form>
</div>
@endsection
