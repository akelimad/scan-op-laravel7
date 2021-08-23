<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\HelperController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

/**
 * UsersController Class
 *
 * Implements actions regarding user management
 */
class UsersController extends Controller
{

    /**
     * Displays the form for account creation
     *
     * @return  Illuminate\Http\Response
     */
    public function create()
    {
        if (!Config::get('subscribe')) {
            return Redirect::to('/');
        } else {
            return view('admin.signup');
        }
    }

    /**
     * Stores new account
     *
     * @return  Illuminate\Http\Response
     */
    public function store()
    {
        if (HelperController::isValidCaptcha(request()->all())) {
            $repo = App::make('UserRepository');
            $user = $repo->signup(request()->all());

            if ($user->id) {
                // assign default role
                $roles[0]=Config::get('default_role');
                $user->roles()->sync($roles);

                if (Config::get('confide::signup_email')) {
                    Mail::queueOn(
                        Config::get('confide::email_queue'),
                        Config::get('confide::email_account_confirmation'),
                        compact('user'),
                        function ($message) use ($user) {
                            $message
                                ->to($user->email, $user->username)
                                ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                        }
                    );
                }

                return Redirect::action('UsersController@login')
                    ->with('notice', Lang::get('confide::confide.alerts.account_created'));
            } else {
                $error = $user->errors()->all(':message');

                return Redirect::action('UsersController@create')
                    ->withInput(request()->except('password'))
                    ->with('error', $error);
            }
        } else {
            return Redirect::action('UsersController@create')
                    ->withInput(request()->except('password'))
                    ->with('error', array("Invalid Captcha!"));
        }
    }

    /**
     * Displays the login form
     *
     * @return  Illuminate\Http\Response
     */
    public function login()
    {
        if (Auth::user()) {
            return Redirect::to('admin');
        } else {
            return view('admin.login');
        }
    }

    /**
     * Attempt to do login
     *
     * @return  Illuminate\Http\Response
     */
    public function doLogin()
    {
        $repo = App::make('UserRepository');
        $input = request()->all();
        
        if (HelperController::isValidCaptcha($input)) {
            if ($repo->login($input)) {
                return Redirect::to('/');
            } else {
                if ($repo->isThrottled($input)) {
                    $err_msg = Lang::get('confide::confide.alerts.too_many_attempts');
                } elseif ($repo->existsButNotConfirmed($input)) {
                    $err_msg = Lang::get('confide::confide.signup.confirmation_required');
                    // $err_msg = Lang::get('confide::confide.alerts.not_confirmed');
                } else {
                    $err_msg = Lang::get('confide::confide.alerts.wrong_credentials');
                }

                return Redirect::action('UsersController@login')
                                ->withInput(request()->except('password'))
                                ->with('error', $err_msg);
            }
        } else {
            return Redirect::action('UsersController@login')
                            ->withInput(request()->except('password'))
                            ->with('error', "Invalid Captcha!");
        }
    }

    /**
     * Attempt to confirm account with code
     *
     * @param  string $code
     *
     * @return  Illuminate\Http\Response
     */
    public function confirm($code)
    {
        if (Auth::confirm($code)) {
            $notice_msg = Lang::get('confide::confide.alerts.confirmation');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_confirmation');
            return Redirect::action('UsersController@login')
                ->with('error', $error_msg);
        }
    }

    /**
     * Displays the forgot password form
     *
     * @return  Illuminate\Http\Response
     */
    public function forgotPassword()
    {
        return view('admin.forgot_password');
    }

    /**
     * Attempt to send change password link to the given email
     *
     * @return  Illuminate\Http\Response
     */
    public function doForgotPassword()
    {
        if (HelperController::isValidCaptcha(request()->all())) {
            if (Auth::forgotPassword(request()->get('email'))) {
                $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
                return Redirect::action('UsersController@login')
                    ->with('notice', $notice_msg);
            } else {
                $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
                return Redirect::action('UsersController@doForgotPassword')
                    ->withInput()
                    ->with('error', $error_msg);
            }
        } else {
            return Redirect::action('UsersController@doForgotPassword')
                    ->withInput()
                    ->with('error', "Invalid Captcha!");
        }
    }

    /**
     * Shows the change password form with the given token
     *
     * @param  string $token
     *
     * @return  Illuminate\Http\Response
     */
    public function resetPassword($token)
    {
        return view('admin.reset_password')
                ->with('token', $token);
    }

    /**
     * Attempt change password of the user
     *
     * @return  Illuminate\Http\Response
     */
    public function doResetPassword()
    {
        $repo = App::make('UserRepository');
        $input = array(
            'token'                 =>request()->get('token'),
            'password'              =>request()->get('password'),
            'password_confirmation' =>request()->get('password_confirmation'),
        );

        // By passing an array with the token, password and confirmation
        if ($repo->resetPassword($input)) {
            $notice_msg = Lang::get('confide::confide.alerts.password_reset');
            return Redirect::action('UsersController@login')
                ->with('notice', $notice_msg);
        } else {
            $error_msg = Lang::get('confide::confide.alerts.wrong_password_reset');
            return Redirect::action('UsersController@resetPassword', array('token'=>$input['token']))
                ->withInput()
                ->with('error', $error_msg);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return  Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::logout();

        return Redirect::to('/');
    }
}
