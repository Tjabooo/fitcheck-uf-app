@extends('layouts.app')

@section('title', 'Register - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Register</h2>
    @if ($errors->has('rate_limit'))
        <div class="error-message">{{ $errors->first('rate_limit') }}</div>
    @endif
    <form action="{{ route('register') }}" method="post">
        @csrf
        <div class="form-group @error('username') has-error @enderror">
            <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required maxlength="24">
            @error('username')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group @error('email') has-error @enderror">
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            @error('email')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group @error('password') has-error @enderror">
            <input type="password" name="password" placeholder="Password" required maxlength="32">
            @error('password')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group @error('password_confirmation') has-error @enderror">
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            @error('password_confirmation')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <button class="main-button-design" type="submit">Register</button>
        </div>
        <p>Already have an account? <a href="{{ route('login') }}">Login here</a>.</p>
    </form>
</div>
@endsection
