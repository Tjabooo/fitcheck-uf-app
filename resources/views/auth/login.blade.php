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
            <div class="form-group @error('identifier') has-error @enderror">
                <input type="text" name="identifier" placeholder="E-post eller användarnamn" value="{{ old('identifier') }}" required>
                @error('identifier')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group @error('password') has-error @enderror">
                <input type="password" name="password" placeholder="Lösenord" required>
                @error('password')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group checkbox-group">
                <label>
                    <input class="check" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="check-label">Kom ihåg mig</span>
                </label>
            </div>
            <div class="form-group">
                <button class="main-button-design" type="submit">Logga in</button>
            </div>
            <p><a href="{{ route('password.request') }}">Glömt lösenord?</a></p>
            <p>Har du inget konto? <a href="{{ route('register.index') }}">Registrera dig här</a>.</p>
        </form>
    </div>
@endsection
