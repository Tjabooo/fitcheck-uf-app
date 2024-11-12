<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClothingArticle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClosetController
{
    use AuthorizesRequests;

    // Display the user's closet
    public function index()
    {
        $user = Auth::user();
        $clothingArticles = $user->clothingArticles;

        return view('closet.index')->with(['clothingArticles' => $clothingArticles]);
    }

    // Show the form for adding a new clothing article
    public function create()
    {
        return view('closet.create');
    }

    // Store a new clothing article
    public function store(Request $request)
    {
        $request->validate([
            'article' => 'required|string|max:255',
            'image' => 'required|image|max:2048', // Max 2MB
        ]);

        $user = Auth::user();

        // Handle file upload
        $imagePath = $request->file('image')->store("public_html/user_images/{$user->id}");
        $imagePath = str_replace('public_html/', 'storage/', $imagePath);

        // Create the clothing article
        ClothingArticle::create([
            'user_id' => $user->id,
            'article' => $request->article,
            'type' => $request->type,
            'color' => $request->color,
            'size' => $request->size,
            'design_print' => $request->design_print,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('closet.index')->with('success', 'Clothing article added to your closet.');
    }

    // Delete a clothing article
    public function destroy(ClothingArticle $clothingArticle)
    {
        $this->authorize('delete', $clothingArticle);

        // Delete the image file
        $imagePath = str_replace('storage/', 'public/', $clothingArticle->image_path);
        Storage::delete($imagePath);

        // Delete the clothing article
        $clothingArticle->delete();

        return redirect()->route('closet.index')->with('success', 'Clothing article removed from your closet.');
    }
}
