@extends('layouts.app')

@section('title', 'Logga in - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Logga in</h2>
    <form action="{{ route('login') }}" method="post">
        @csrf
        @if (session('status'))
            <div class="success-message">
                {{ session('status') }}
            </div>
        @endif
        <div class="form-group @error('email') has-error @enderror">
            <input type="email" name="email" placeholder="E-post" value="{{ old('email') }}" required>
            @error('email')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group @error('password') has-error @enderror">
            <input type="password" name="password" placeholder="Lösenord" required>
            @error('password')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <button class="main-button-design" type="submit">Logga in</button>
        </div>
        <p><a href="{{ route('password.request') }}">Glömt lösenord?</a></p>
        <p>Har du inget konto? <a href="{{ route('register') }}">Registrera dig här</a>.</p>
    </form>
</div>
@endsection
