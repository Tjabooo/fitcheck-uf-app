@extends('layouts.app')

@section('title', 'Inställningar')

@section('content')
@include('partials.nav')

<section class="settings-page screen">
    <div class="settings-section">
        <h2>Konto</h2>

        <!-- User Info Section -->
        <div class="settings-item">
            <div class="settings-label">Användarnamn</div>
            <div class="settings-value">{{ $user->username }}</div>
        </div>
        <div class="settings-item">
            <div class="settings-label">E-post</div>
            <div class="settings-value">{{ $user->email }}</div>
        </div>
        <div class="settings-item">
            <div class="settings-label">Lösenord</div>
            <div class="settings-value">********</div>
        </div>
        <!-- Logout Section -->
        <div class="settings-item clickable">
            <form action="{{ route('logout') }}" method="POST" id="logout-form" onsubmit="return confirm('Är du säker på att du vill logga ut?');">
                @csrf
                <button type="submit" class="settings-button main-button-design">
                    <img src="{{ asset('assets/icons/logout-icon.png') }}" alt="Logout" />
                    Logga ut
                </button>
            </form>
        </div>
    </div>

    <div class="settings-section">
        <h2>Danger Zone</h2>

        <!-- Delete Account Section -->
        <div class="settings-item clickable">
            <form action="{{ route('account.destroy') }}" method="POST" id="delete-form" onsubmit="return confirm('Är du säker på att du vill radera ditt konto?\nDet här går inte att ångra.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="settings-button delete-button">
                    <img src="{{ asset('assets/icons/delete-icon.png') }}" alt="Delete"/>
                    Radera konto
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
