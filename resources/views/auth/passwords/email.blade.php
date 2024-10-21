@extends('layouts.app')

@section('title', 'Återställ lösenord - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Återställ ditt lösenord</h2>
    <p>Vänligen ange din e-postadress för att återställa ditt lösenord.</p>
    @if ($errors->any())
        <div class="error-message">{{ $errors->first() }}</div>
    @endif
    <form action="{{ route('password.email') }}" method="post">
        @csrf
        <div class="form-group @error('email') has-error @enderror">
            <input type="email" name="email" placeholder="E-post" value="{{ old('email') }}" required>
            @error('email')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <button class="main-button-design" type="submit">Skicka återställningslänk</button>
        </div>
    </form>
</div>
@endsection
