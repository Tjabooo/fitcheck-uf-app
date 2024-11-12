<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmailVerificationToken;
use App\Mail\VerificationMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthController
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

        $remember = $request->has('remember');

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Check if the user is verified
            if (!$user->verified) {
                Auth::logout();
                return back()->withErrors(['email' => 'Vänligen verifiera din e-post innan du loggar in.']);
            }

            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Uppgifterna du angav matchade inte ett konto i vårt system.',
        ]);
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/logga-in');
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
            return back()->withErrors([
                'registration_error' => 'Du har försökt för många gånger. Försök igen senare.',
            ]);
        }

        // Record registration attempt
        \App\Models\RegistrationAttempt::create([
            'ip_address' => $ipAddress,
            'attempt_time' => Carbon::now(),
        ]);

        // Validate input
        $request->validate([
            'username' => 'required|string|max:24|unique:users,username|alpha_num',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed|min:8|max:32',
        ]);

        // Start transaction
        DB::beginTransaction();

        try {
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

            // Commit transaction
            DB::commit();

            // Send verification email
            $this->sendVerificationEmail($user->email, $token);

            // Store email in session for possible resend
            session(['email' => $user->email]);

            return redirect()->to('logga-in')->with('status', 'Du har fått ett mejl med verifieringslänk! Klicka på länken för att logga in.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['registration_error' => $e->getMessage()]);
        }
    }

    // Send verification email
    protected function sendVerificationEmail($email, $token)
    {
        $verificationLink = route('auth.verify', ['token' => $token]);

        Mail::to($email)
            ->send(new VerificationMail($verificationLink));
    }

    // Show registration confirmation message
    public function showConfirmationMessage()
    {
        $email = session('email');
        if (!$email) {
            return redirect()->route('register');
        }

        // Check if the user can resend verification email
        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('register');
        }

        $tokenData = EmailVerificationToken::where('user_id', $user->id)->first();

        if (!$tokenData) {
            return redirect()->route('register');
        }

        return view('auth.register', [
            'email_err' => session('email_err'),
            'message' => session('message'),
        ]);
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
}
