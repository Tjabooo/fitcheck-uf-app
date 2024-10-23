@extends('layouts.app')

@section('title', 'Inställningar')

@section('content')
<section class="screen settings-page">
    <h2>Inställningar</h2>
    <div class="auth-container">
        <div class="delete-account">
            <h3>Radera ditt konto</h3>
            <p>Varning: Den här åtgärden kan inte ångras. All din data kommer bli permanent raderad.</p>
            <form action="{{ route('account.destroy') }}" method="POST" onsubmit="return confirm('Är du säker på att du vill radera ditt konto?');">
                @csrf
                @method('DELETE')
                <button id="delete-account-button" class="main-button-design" style="background-color: #e74c3c;">Radera konto</button>
                <div id="delete-error" class="error-message" style="display: none;"></div>
                <div id="delete-status" class="success-message" style="display: none;"></div>
            </form>
        </div>
    </div>
</section>
@endsection
