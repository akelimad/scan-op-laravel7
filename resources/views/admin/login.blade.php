@extends('admin.layouts.auth')

@section('head')
<title>Administration - Login Page</title>
@stop

@section('content')
<div class="col-md-4 col-md-offset-4">
    <div class="login-panel panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Please Sign In</h3>
        </div>
        <div class="panel-body">
            <form role="form" method="POST" action="{{{ URL::to('/users/login') }}}" accept-charset="UTF-8">
                <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
                <fieldset>
                    <div class="form-group">
                        <input class="form-control" tabindex="1" placeholder="{{{ Lang::get('confide::confide.username_e_mail') }}}" type="text" name="email" id="email" value="{{{ Input::old('email') }}}">
                    </div>
                    <div class="form-group">
                        <input class="form-control" tabindex="2" placeholder="{{{ Lang::get('confide::confide.password') }}}" type="password" name="password" id="password">
                        <p class="help-block">
                            <a href="{{{ URL::to('/users/forgot_password') }}}">{{{ Lang::get('confide::confide.login.forgot_password') }}}</a>
                        </p>
                    </div>
                    <div class="checkbox">
                        <label for="remember">
                            <input tabindex="4" type="checkbox" name="remember" id="remember" value="1"> {{{ Lang::get('confide::confide.login.remember') }}}
                        </label>
                    </div>
                    <br/>
                    <div class="form-group">
                    {{HTML::image(Captcha::url())}}
                    {{Form::text('captcha')}}
                    </div>
        
                    @if (Session::get('error'))
                    <div class="alert alert-error alert-danger">{{{ Session::get('error') }}}</div>
                    @endif

                    @if (Session::get('notice'))
                    <div class="alert">{{{ Session::get('notice') }}}</div>
                    @endif
                    <div class="form-group">
                        <button tabindex="3" type="submit" class="btn btn-lg btn-success btn-block">{{{ Lang::get('confide::confide.login.submit') }}}</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
@stop
