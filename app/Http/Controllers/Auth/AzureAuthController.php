<?php


namespace App\Http\Controllers\Auth;


use Laravel\Socialite\Facades\Socialite;
use Metrogistics\AzureSocialite\AuthController;

class AzureAuthController extends AuthController
{
    public function handleOauthResponse()
    {

        $user = Socialite::driver('azure-oauth')->user();

        $authUser = $this->findOrCreateUser($user);

        auth()->login($authUser, true);

        return redirect()->intended(
            config('azure-oath.redirect_on_login')
        );
    }
}
