@extends('layouts.app')

@section('title', 'Registration Confirmation - FitCheck UF')

@section('content')
<div class="auth-container">
    <h2>Registration Successful!</h2>
    <p>We've sent a verification email to your email address. Please check your inbox. Head to the <a href="{{ route('login') }}">login</a> page once you're done.</p>

    <!-- Handle resending verification email -->
    <form action="{{ route('auth.verify.resend') }}" method="post">
        @csrf
        <div class="form-group">
            <button class="main-button-design" type="submit" id="resend-button" @if (!$can_resend) disabled @endif>Resend Verification Email</button>
        </div>
    </form>

    @if (!$can_resend)
        <p>Please wait <span id="countdown">{{ intval($remaining_time) }}</span> seconds before resending.</p>
    @endif

    <!-- Display error or success messages -->
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
