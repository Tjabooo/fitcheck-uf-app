@extends('layouts.app')

@section('title', 'Add New Clothing Article')

@section('content')
<div class="auth-container">
    <h2>Add New Clothing Article</h2>
    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('closet.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="article">Article Name</label>
            <input type="text" name="article" id="article" required>
        </div>
        <div class="form-group">
            <label for="image">Upload Image</label>
            <input type="file" name="image" id="image" accept="image/*" required>
        </div>
        <div class="form-group">
            <button type="submit" class="main-button-design">Add to Closet</button>
        </div>
    </form>
</div>
@endsection
