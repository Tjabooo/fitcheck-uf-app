@extends('layouts.app')

@section('title', 'Registreringsbekräftelse - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Registrering lyckad!</h2>
    <p>Vi har skickat ett verifieringsmail till din e-postadress. Kontrollera din inkorg. Gå till <a href="{{ route('login') }}">inloggningssidan</a> när du är klar.</p>

    <form action="{{ route('auth.verify.resend') }}" method="post">
        @csrf
        <div class="form-group">
            <button class="main-button-design" type="submit" id="resend-button" @if (!$can_resend) disabled @endif>Skicka verifierings e-post igen</button>
        </div>
    </form>

    @if (!$can_resend)
        <p>Vänta <span id="countdown">{{ intval($remaining_time) }}</span> sekunder innan du skickar igen.</p>
    @endif

    @if ($errors->has('email_err'))
        <div class="error-message">{{ $errors->first('email_err') }}</div>
    @endif

    @if (session('message'))
        <div class="success-message">{{ session('message') }}</div>
    @endif
</div>

<script>
    let remainingTime = {{ intval($remaining_time) }};
    const resendButton = document.getElementById('resend-button');
    const countdownSpan = document.getElementById('countdown');

    if (remainingTime > 0) {
        const interval = setInterval(() => {
            remainingTime--;
            countdownSpan.textContent = remainingTime;
            if (remainingTime <= 0) {
                clearInterval(interval);
                resendButton.disabled = false;
                countdownSpan.parentElement.style.display = 'none';
            }
        }, 1000);
    }
</script>
@endsection
