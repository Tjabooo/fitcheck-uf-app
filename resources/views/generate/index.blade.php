@extends('layouts.app')

@section('title', 'Omklädningsrum')

@section('content')
@include('partials.nav')
<section class="screen generate-screen">
    <select name="option">
        <option value="">Välj ett tema</option>
    </select>
    <input type="text" placeholder="Skriv övriga önskemål här..." />
    <div class="more-info-container">
        <img src="{{ asset('assets/icons/info-icon.png') }}" alt="Info Icon" />
        <span id="more-info-span">Mer info</span>
    </div>
    <button class="main-button-design" type="submit">Klä mig!</button>
    <button class="main-button-design" id="recent-fits">Mina fits</button>
</section>
@endsection
