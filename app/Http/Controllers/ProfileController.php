<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Display the user's profile
    public function index()
    {
        $user = Auth::user();
        return view('profile.index')->with(['user' => $user]);
    }

    // Upload and update profile picture
    public function updateProfilePicture(Request $request)
    {
        $user = $request->user();

        // Validate request
        if (!$request->hasFile('profile_picture')) {
            return response()->json(['success' => false, 'error' => 'No file uploaded'], 400);
        }

        $file = $request->file('profile_picture');

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png|max:2048', // Only allow jpg and png, max size 2MB
        ]);

        // Determine file extension
        $extension = $file->getClientOriginalExtension();
        $filename = $user->username . '.' . $extension;

        $target_directory = 'assets/profile-pictures/';
        $path = $target_directory . $filename;

        // Delete old profile picture if it exists
        foreach (['jpg', 'jpeg', 'png'] as $ext) {
            $old_file = $target_directory . $user->username . '.' . $ext;
            if (file_exists(public_path($old_file)) && $old_file !== $path) {
                unlink(public_path($old_file));
            }
        }

        // Store the new file in the public directory
        $file->move(public_path($target_directory), $filename);

        // Update the user's profile with the new image path
        $user->profile_picture_path = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'profile_picture' => asset($path),
        ]);
    }
}
