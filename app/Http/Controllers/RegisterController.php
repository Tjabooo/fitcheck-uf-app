<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmailVerificationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegisterController extends Controller
{
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
            return back()->withErrors(['rate_limit_err' => 'Too many registration attempts. Please try again later.']);
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

            return redirect()->route('registration.confirmation');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['registration_error' => $e->getMessage()]);
        }
    }

    // Send verification email
    protected function sendVerificationEmail($email, $token)
    {
        $verificationLink = route('auth.verify', ['token' => $token, 'email' => urlencode($email)]);

        Mail::send('emails.verify_email', ['verificationLink' => $verificationLink], function ($message) use ($email) {
            $message->to($email)
                ->subject('Email Verification - FitCheck UF');
        });
    }

    // Show registration confirmation page
    public function showConfirmationPage()
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

        $canResend = true;
        $remainingTime = 0;

        if ($tokenData && Carbon::now()->diffInSeconds($tokenData->last_sent) < 60) {
            $canResend = false;
            $remainingTime = 60 - Carbon::now()->diffInSeconds($tokenData->last_sent);
        }

        return view('auth.registration_confirmation', [
            'can_resend' => $canResend,
            'remaining_time' => $remainingTime,
            'email_err' => session('email_err'),
            'message' => session('message'),
        ]);
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
