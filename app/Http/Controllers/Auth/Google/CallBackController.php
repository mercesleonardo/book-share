<?php

namespace App\Http\Controllers\Auth\Google;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, DB, Hash};
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class CallBackController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', __('Failed to authenticate with Google.'));
        }

        $user = User::where('provider_name', 'google')
            ->where('provider_id', $googleUser->getId())
            ->first();

        if (!$user) {
            if ($googleUser->getEmail()) {
                $existing = User::where('email', $googleUser->getEmail())->first();

                if ($existing) {
                    // Inform the user and suggest linking accounts via login
                    return redirect('/login')->with('error', __('This email is already in use. Please log in with your existing account.'));
                }
            }
        }

        DB::transaction(function () use (&$user, $googleUser) {
            if (!$user) {
                $user = User::create([
                    'name'              => $googleUser->getName() ?? $googleUser->getNickname() ?? 'User ' . Str::random(6),
                    'email'             => $googleUser->getEmail(),
                    'password'          => Hash::make(Str::random(40)),
                    'provider_name'     => 'google',
                    'provider_id'       => $googleUser->getId(),
                    'email_verified_at' => now(),
                ]);
            } else {
                $user->update([
                    'email_verified_at' => now(),
                ]);
            }
        });

        Auth::login($user, true);

        return redirect()->intended('/');
    }
}
