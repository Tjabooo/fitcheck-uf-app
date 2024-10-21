@extends('layouts.app')

@section('title', 'Reset Password - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Återställ ditt lösenord</h2>
    @if ($errors->has('invalid_request_err'))
        <div class="error-message">{{ $errors->first('invalid_request_err') }}</div>
    @endif
    <form action="{{ route('password.update') }}" method="post">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="form-group @error('password') has-error @enderror">
                <input type="password" name="password" placeholder="Nytt lösenord" required>
                @error('password')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
        <div class="form-group @error('password_confirmation') has-error @enderror">
            <input type="password" name="password_confirmation" placeholder="Bekräfta nytt lösenord" required>
            @error('password_confirmation')
                <span class="help-block">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <button class="main-button-design" type="submit">Reset Password</button>
        </div>
    </form>
</div>
@endsection
