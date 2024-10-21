<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    // Show request form
    public function showRequestForm()
    {
        return view('auth.email');
    }

    // Handle reset link request
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return view('auth.reset-password-confirmation');
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

        return view('auth.reset-password-confirmation');
    }

    // Send reset email
    protected function sendResetEmail($email, $token)
    {
        $resetLink = route('reset-password', ['token' => $token, 'email' => urlencode($email)]);

        Mail::send('emails.reset_password', ['resetLink' => $resetLink], function ($message) use ($email) {
            $message->to($email)
                ->subject('Reset Password - FitCheck UF');
        });
    }

    // Show reset form
    public function showResetForm(Request $request)
    {
        $token = $request->token;
        $email = $request->email;

        // Validate token
        $tokenData = DB::table('password_resets')
            ->where('token', $token)
            ->where('email', $email)
            ->first();

        if (!$tokenData || Carbon::parse($tokenData->expires)->isPast()) {
            return view('auth.reset-password');
        }

        return view('auth.passwords.reset', compact('token', 'email'));
    }

    // Handle password reset
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8|max:32',
            'token' => 'required',
        ]);

        $tokenData = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$tokenData || Carbon::parse($tokenData->expires)->isPast()) {
            return back()->withErrors(['invalid_request_err' => 'Invalid or expired token.']);
        }

        // Update user's password
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['invalid_request_err' => 'No account found with that email.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password reset successful. You can now log in.');
    }
}
