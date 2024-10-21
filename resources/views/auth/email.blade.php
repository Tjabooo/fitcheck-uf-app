@extends('layouts.app')

@section('title', 'Reset Password - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Reset Your Password</h2>
    <p>Please enter your email address to reset your password.</p>
    @if ($errors->any())
        <div class="error-message">{{ $errors->first() }}</div>
    @endif
    <form action="{{ route('password.email') }}" method="post">
        @csrf
        <div class="form-group @error('email') has-error @enderror">
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            @error('email')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <button class="main-button-design" type="submit">Send Reset Link</button>
        </div>
    </form>
</div>
@endsection
