<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmailVerificationToken;
use App\Mail\VerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        // Validate email and password
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if the user is verified
            if (!$user->verified) {
                Auth::logout();
                return back()->withErrors(['email' => 'Please verify your email before logging in.']);
            }

            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The credentials you provided did not match an account in our system.',
        ]);
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Show registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        // Check registration attempts
        $ipAddress = $request->ip();
        $maxAttempts = 5;
        $attemptWindow = 3600; // 1 hour

        $recentAttempts = \App\Models\RegistrationAttempt::where('ip_address', $ipAddress)
            ->where('attempt_time', '>=', Carbon::now()->subSeconds($attemptWindow))
            ->count();

        if ($recentAttempts >= $maxAttempts) {
            return back()->withErrors(['rate_limit' => 'Too many registration attempts. Please try again later.']);
        }

        // Record registration attempt
        \App\Models\RegistrationAttempt::create([
            'ip_address' => $ipAddress,
            'attempt_time' => Carbon::now(),
        ]);

        // Validate input
        $request->validate([
            'username' => 'required|string|max:24|unique:users|alpha_num',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8|max:32',
        ]);

        // Create user
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate email verification token
        $token = bin2hex(random_bytes(32));

        EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires' => Carbon::now()->addDay(),
            'last_sent' => Carbon::now(),
        ]);

        // Send verification email
        $this->sendVerificationEmail($user->email, $token);

        // Store email in session for possible resend
        session(['email' => $user->email]);

        return redirect()->route('  .notice');
    }

    // Send verification email
    protected function sendVerificationEmail($email, $token)
    {
        $verificationLink = route('auth.verify', ['token' => $token, 'email' => urlencode($email)]);

        Mail::to($email)
            ->send(new VerificationMail($verificationLink));
    }

    // Show verification notice
    public function showVerificationNotice()
    {
        return view('auth.verification_notice');
    }

    // Verify email
    public function verifyEmail(Request $request)
    {
        $email = $request->email;
        $token = $request->token;

        $verificationToken = EmailVerificationToken::where('token', $token)->first();


        if (!$verificationToken || $verificationToken->expires < Carbon::now()) {
            return view('auth.emails.verify_email');
        }

        $user = $verificationToken->user;

        $user->verified = true;
        $user->email_verified_at = Carbon::now();
        $user->save();


        // Delete the token
        $verificationToken->delete();

        return view('auth.emails.verify_email', ['verified' => true]);
    }

    // Resend verification email
    public function resendVerificationEmail(Request $request)
    {
        $email = session('email');

        if (!$email) {
            return redirect()->route('register');
        }

        $user = User::where('email', $email)->first();

        if (!$user || $user->verified) {
            return redirect()->route('login');
        }

        $tokenData = EmailVerificationToken::where('user_id', $user->id)->first();

        // Rate limit: Allow resend every 60 seconds
        if ($tokenData && Carbon::now()->diffInSeconds($tokenData->last_sent) < 60) {
            $remainingTime = 60 - Carbon::now()->diffInSeconds($tokenData->last_sent);
            return back()->withErrors(['resend_limit' => "Please wait $remainingTime seconds before resending."]);
        }

        // Generate new token
        $token = bin2hex(random_bytes(32));

        // Delete old token and create new one
        if ($tokenData) {
            $tokenData->delete();
        }

        EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires' => Carbon::now()->addDay(),
            'last_sent' => Carbon::now(),
        ]);

        // Send verification email
        $this->sendVerificationEmail($user->email, $token);

        return back()->with('message', 'A new verification email has been sent.');
    }
}
