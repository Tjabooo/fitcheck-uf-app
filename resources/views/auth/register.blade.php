@extends('layouts.app')

@section('title', 'Registrera - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Registrera</h2>
    <form action="{{ route('register') }}" method="post">
        @csrf
        <div class="form-group @error('username') has-error @enderror">
            <input type="text" name="username" placeholder="Användarnamn" value="{{ old('username') }}" required maxlength="24">
            @error('username')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group @error('email') has-error @enderror">
            <input type="email" name="email" placeholder="E-post" value="{{ old('email') }}" required>
            @error('email')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group @error('password') has-error @enderror">
            <input type="password" name="password" placeholder="Lösenord" required maxlength="32">
            @error('password')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group @error('password_confirmation') has-error @enderror">
            <input type="password" name="password_confirmation" placeholder="Bekräfta lösenord" required>
            @error('password_confirmation')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <button class="main-button-design" type="submit">Registrera</button>
        </div>
        <p>Har du redan ett konto? <a href="{{ route('login') }}">Logga in här</a>.</p>
    </form>
</div>
@endsection
