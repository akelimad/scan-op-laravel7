@extends('admin.layouts.auth')

@section('head')
<title>Administration - Forgot Password</title>
@stop

@section('content')
<div class="col-md-6 col-md-offset-3">
    <div class="login-panel panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Forgot Password</h3>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ URL::to('/users/forgot_password') }}" accept-charset="UTF-8">
                <input type="hidden" name="_token" value="{{{ Session::getToken() }}}">

                <div class="form-group">
                    <label for="email">{{{ Lang::get('confide::confide.e_mail') }}}</label>
                    <div class="input-append input-group">
                        <input class="form-control" placeholder="{{{ Lang::get('confide::confide.e_mail') }}}" type="email" name="email" id="email" value="{{{ Input::old('email') }}}">
                        <span class="input-group-btn">
                            <input class="btn btn-default" type="submit" value="{{{ Lang::get('confide::confide.forgot.submit') }}}">
                        </span>
                    </div>
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
            </form>
        </div>
    </div>
</div>
@stop
