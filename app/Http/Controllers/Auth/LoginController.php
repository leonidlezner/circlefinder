<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use Auth;
use \App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redirectTo = config('auth.login_redirect');

        $this->middleware('guest')->except('logout');
    }

    public function redirectToFacebook()
    {
        return Socialite::with('facebook')->redirect();
    }

    public function getFacebookCallback()
    {
        $data = Socialite::driver('facebook')->user();

        $user = User::where('email', $data->getEmail())->first();

        if (!is_null($user)) {
            Auth::login($user);
            
            $user->name = $data->getName();
            $user->provider_id = $data->getId();

            $user->save();
        } else {
            $user = User::where('provider_id', $data->getId())->first();

            if (is_null($user)) {
                $user = new User();
                
                $user->name = $data->getName();
                $user->email = $data->getEmail();
                $user->provider_id = $data->getId();
                $user->timezone = null;
                $user->password = Hash::make(str_random(25));
                $user->no_password = true;

                $user->save();
            }

            Auth::login($user);
        }

        return redirect(route('home'))->with('success', 'Successfully logged in!');
    }
}
