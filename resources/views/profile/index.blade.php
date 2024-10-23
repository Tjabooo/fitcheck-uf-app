@extends('layouts.app')

@section('title', 'Spegel')

@section('content')
@include('partials.nav')
<section class="screen profile-screen">
    <h2>Din profil</h2>
    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif
    <div class="profile-info">
        <div class="profile-picture-container">
            <img src="{{ $user->profile_picture_path ? asset($user->profile_picture_path) : asset('assets/profile-pictures/default-profile.png') }}" alt="Profile Picture" id="profile-picture" class="profile-picture" />
            <input hidden=true type="file" id="profile-picture-input" name="profile_picture" accept="image/*" required />
            <button id="edit-profile-picture" class="edit-icon" title="Edit Profile Picture">
                <img src="{{ asset('assets/icons/edit-icon.png') }}" alt="Edit" />
            </button>
        </div>
        @csrf
        <button type="submit" id="apply-profile-picture" class="apply-button" style="display:none;">Spara ändringar</button>
        <div id="upload-status" class="success-message" style="display: none;"></div>
        <div id="upload-error" class="error-message" style="display: none;"></div>
        <p><strong>{{ $user->username }}</strong></p>
        <p>{{ $user->email }}</p>
    </div>
<section>
@endsection
