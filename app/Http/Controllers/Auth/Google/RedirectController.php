<?php

namespace App\Http\Controllers\Auth\Google;

use App\Http\Controllers\Controller;
use Illuminate\Http\{RedirectResponse, Request};
use Laravel\Socialite\Facades\Socialite;

class RedirectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }
}
