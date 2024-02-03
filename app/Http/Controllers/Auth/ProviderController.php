<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class ProviderController extends Controller
{

    public function redirectUrl($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callbackUrl($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('email','=',$socialUser->getEmail())
                ->where('authId','=',$socialUser->getId())
                ->where('authProvider','=',$provider)
                ->first();

            if ($user) {
                 \Auth::login($user);
                return redirect()->route('dashboard');
            }
            else {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'userName' => User::generateUserName($socialUser->getNickname()),
                    'authProvider' => $provider,
                    'authId' => $socialUser->getId(),
                    'providerToken' => $socialUser->token,
                    'email_verified_at' => now()
                ]);

                if ($user) {
                    \Auth::login($user);
                    return redirect()->route('dashboard');
                }

//                return redirect()->route('login')->withErrors(['email'=>'Email is already registered.']);
            }

        } catch (\Exception $e) {
            return redirect()->route('login');
        }
    }
}
