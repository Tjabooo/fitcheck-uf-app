<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SettingsController extends Controller
{
    // Display profile settings
    public function index()
    {
        $user = Auth::user();
        return view('profile.settings')->with(['user' => $user]);
    }

    // Delete the user's account
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Delete the user's profile picture if it exists
        if ($user->profile_picture_path != 'assets/profile-pictures/default-picture.png' && file_exists(public_path($user->profile_picture_path))) {
            unlink(public_path($user->profile_picture_path));
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Ditt konto har raderats.');
    }
}
