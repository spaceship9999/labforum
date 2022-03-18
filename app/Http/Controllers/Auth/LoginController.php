<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\PermissionServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
    use ThrottlesLogins;

    // The initial decay minutes is ten. This shouldn't be hardcoded and should be returned from database settings.
    protected $maxAttempts = 3;
    protected $defaultDecayMinutes = 10;
    protected $decayMinutes =  30;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }else {
            if ($this->attemptLogin($request)) {

                //The login is successful, we create token and store it in db.
                $user = $request->user();
                $user_token = $user->createToken('Personal Access Token');


                if ($request->get('remember')) {
                    $user_token->token->expires_at = Carbon::now()->addWeeks(2);
                }

                $user_token->token->save();

                return response()->json([
                    'username' => $request->user()->username,
                    'access_token' => $user_token->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at' => Carbon::parse(
                        $user_token->token->expires_at
                    )->toDateTimeString()
                ]);


            }else {
                $this->incrementLoginAttempts($request);
                return $this->sendFailedLoginResponse($request);
            }
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success' => true,
        ]);
    }

    public function getIncorrectAttempts(Request $request) {
        $key = $this->throttleKey($request);
        return $this->limiter()->attempts($key);
    }

    public function getAttemptDetails(Request $request) {
        return array(
            'attempts' => $this->getIncorrectAttempts($request),
            'remaining' => ceil($this->limiter()->availableIn($this->throttleKey($request)) /60),
        );
    }

    protected function credentials(Request $request)
    {
        //Check whether the credentials are valid.
        if (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            return  ['email' => $request->get('email'), 'password' => $request->get('password')];
        }
        else if (str_starts_with($request->get('email'), '@')) {
            //Remove the @ from the front.
            return ['username' => ltrim($request->get('email'), '@'), 'password' => $request->get('password')];
        }
        else {
            return ['username' => $request->get('email'), 'password' => $request->get('password')];
        }
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'string|required',
            'password' => 'string|required',
        ]);
    }

    protected function throttleKey(Request $request)
    {
        return $request->ip();
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return response(array(
            'error' => 'incorrect_username_or_password',
        ), 401);
    }
    protected function sendLockoutResponse(Request $request)
    {

        return response(array(
            'error' => 'too_many_attempts',
            'decay' => $this->decayMinutes,
        ), 403);
    }
}
