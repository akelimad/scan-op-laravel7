@extends('admin.layouts.auth')

@section('head')
<title>Administration - Sign Up</title>
@stop

@section('content')
<div class="col-md-6 col-md-offset-3">
    <div class="login-panel panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Sign Up</h3>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{{ URL::to('users') }}}" accept-charset="UTF-8">
                <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">
                <fieldset>
                    @if (Cache::remember('username_in_confide', 5, function() {
                    return Schema::hasColumn(Config::get('auth.table'), 'username');
                    }))
                    <div class="form-group">
                        <label for="username">{{{ Lang::get('confide::confide.username') }}}</label>
                        <input class="form-control" placeholder="{{{ Lang::get('confide::confide.username') }}}" type="text" name="username" id="username" value="{{{ Input::old('username') }}}">
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="email">{{{ Lang::get('confide::confide.e_mail') }}} @if(Config::get('confide::signup_email'))<small>{{ Lang::get('confide::confide.signup.confirmation_required') }}</small>@endif</label>
                        <input class="form-control" placeholder="{{{ Lang::get('confide::confide.e_mail') }}}" type="text" name="email" id="email" value="{{{ Input::old('email') }}}">
                    </div>
                    <div class="form-group">
                        <label for="password">{{{ Lang::get('confide::confide.password') }}}</label>
                        <input class="form-control" placeholder="{{{ Lang::get('confide::confide.password') }}}" type="password" name="password" id="password">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">{{{ Lang::get('confide::confide.password_confirmation') }}}</label>
                        <input class="form-control" placeholder="{{{ Lang::get('confide::confide.password_confirmation') }}}" type="password" name="password_confirmation" id="password_confirmation">
                    </div>

                    <br/>
                    <div class="form-group">
                        {{HTML::image(Captcha::url())}}
                        {{Form::text('captcha')}}
                    </div>

                    @if (Session::get('error'))
                    <div class="alert alert-error alert-danger">
                        @if (is_array(Session::get('error')))
                        {{ head(Session::get('error')) }}
                        @endif
                    </div>
                    @endif

                    @if (Session::get('notice'))
                    <div class="alert">{{ Session::get('notice') }}</div>
                    @endif

                    <div class="form-actions form-group">
                        <button type="submit" class="btn btn-primary">{{{ Lang::get('confide::confide.signup.submit') }}}</button>
                    </div>

                </fieldset>
            </form>
        </div>
    </div>
</div>
@stop