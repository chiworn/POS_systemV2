<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback authentication.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Find existing user by google_id or email
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                // Update google_id and avatar if missing
                $user->update([
                    'google_id' => $user->google_id ?? $googleUser->id,
                    'avatar' => $googleUser->avatar ?? $user->avatar,
                ]);
            } else {
                // Create new cashier user with status = false (pending approval)
                $cashierRole = Role::where('name', 'Cashier')->first();

                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'role_id' => $cashierRole ? $cashierRole->id : null,
                    'password' => null,
                    'status' => false,
                ]);
            }

            // Check if account status is active
            if (!$user->status) {
                return redirect()->route('login')->with('status', 'Your Google account registration is successful! However, your account is currently pending administrator approval before you can log in.');
            }

            Auth::login($user, true);
            request()->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (Exception $e) {
            return redirect()->route('login')->with('status', 'Unable to authenticate with Google. Please try again or login with email.');
        }
    }
}
