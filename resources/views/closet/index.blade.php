@extends('layouts.app')

@section('title', 'Din garderob')

@section('content')
@include('partials.nav')
<section class="screen closet-screen">
    <div class="add-new-clothes-container" onclick="openCameraModal()">
        <img id="add-new-clothes" src="{{ asset('assets/icons/add-icon.png') }}" alt="Add Icon">
        <span>Lägg till klädesplagg</span>
    </div>
    <h2 class="closet-title">Din garderob</h2>
    <div class="grid-container">
        @isset ($clothingArticles)
            @forelse ($clothingArticles as $article)
                <div class="grid-item">
                    <img src="{{ asset($article->image_path) }}" alt="{{ $article->article }}">
                    <p>{{ $article->article }}</p>
                    <form action="{{ route('closet.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-button">Remove</button>
                    </form>
                </div>
            @empty
                <p>Du har inga kläder i din garderob</p>
            @endforelse
        @else
            <p>Du har inga kläder i din garderob</p>
        @endisset
    </div>
</section>

<!-- Include Camera Modal -->
@include('closet.add-clothing')

@endsection
