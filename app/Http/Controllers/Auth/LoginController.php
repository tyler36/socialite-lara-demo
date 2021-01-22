<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        $socialiteUser = Socialite::driver($provider)->user();

        $user = User::firstOrCreate(
            [
                'provider_id' => $socialiteUser->getId(),
                'provider' => $provider,
            ],
            [
                'email' => $socialiteUser->getEmail(),
                'name' => $socialiteUser->getName() ?: $socialiteUser->getNickname(),
                'provider_id' => $socialiteUser->getId(),
                'provider' => 'github',
            ]
        );

        auth()->login($user, true);

        return redirect('dashboard');
    }
}
