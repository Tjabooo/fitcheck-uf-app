@extends('layouts.app')

@section('title', 'Login - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Login</h2>
    <form action="{{ route('login') }}" method="post">
        @csrf
        <div class="form-group @error('email') has-error @enderror">
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            @error('email')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group @error('password') has-error @enderror">
            <input type="password" name="password" placeholder="Password" required>
            @error('password')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <button class="main-button-design" type="submit">Login</button>
        </div>
        <p><a href="{{ route('password.request') }}">Forgot Password?</a></p>
        <p>Don't have an account? <a href="{{ route('register') }}">Register here</a>.</p>
    </form>
</div>
@endsection
