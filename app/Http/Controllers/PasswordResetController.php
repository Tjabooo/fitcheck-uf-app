<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;

class PasswordResetController extends Controller
{
    // Show request form
    public function showRequestForm()
    {
        return view('auth.passwords.email');
    }

    // Handle reset link request
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Do not reveal that the email does not exist
            return view('auth.passwords.confirmation');
        }

        // Generate token
        $token = Str::random(64);

        // Delete existing tokens
        DB::table('password_resets')->where('email', $email)->delete();

        // Insert new token
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'expires' => Carbon::now()->addMinutes(30),
            'created_at' => Carbon::now(),
        ]);

        // Send email
        $this->sendResetEmail($email, $token);

        return view('auth.passwords.confirmation');
    }

    // Send reset email
    protected function sendResetEmail($email, $token)
    {
        $resetLink = route('password.reset', ['token' => $token]);

        Mail::to($email)
            ->send(new ResetPasswordMail($resetLink));
    }

    // Show reset form
    public function showResetForm(Request $request)
    {
        $token = $request->token;
        $email = urldecode($request->email);

        // Validate token
        $tokenData = DB::table('password_resets')
            ->where('token', $token)
            ->where('email', $email)
            ->first();

        if (!$tokenData || Carbon::parse($tokenData->expires)->isPast()) {
            return redirect()->route('errors.invalid_token')->withErrors(['invalid_request_err' => 'Ogiltig eller utgånget token.']);
        }

        return view('auth.passwords.reset')->with(['token' => $token, 'email' => $email]);
    }

    // Handle password reset
    public function reset(Request $request)
    {
        $request->merge(['email' => urldecode($request->email)]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8|max:32',
            'token' => 'required',
        ]);

        $tokenData = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('email', urldecode($request->email))
            ->first();

        if (!$tokenData || Carbon::parse($tokenData->expires)->isPast()) {
            return back()->withErrors(['invalid_request_err' => 'Ogiltig eller utgånget token.']);
        }

        // Update user's password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['invalid_request_err' => 'Uppgifterna du angav matchade inte något konto i vårt system.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Lösenord återställt. Du kan nu logga in.');
    }
}
