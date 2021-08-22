
@extends('admin.layouts.auth')

@section('head')
    <title>{{ __("Administration - Login Page") }}</title>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4 col-md-offset-4 mt-5">
            <h3 class="text-center mb-5">{{ \App\Option::findByKey('site.name') }}</h3>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">{{ __("Sign in to start your session") }}</h3>
                </div>
                <div class="panel-body">
                    <form role="form" method="POST" action="{{ route('login') }}">
                        @csrf
                        <fieldset>
                            <div class="form-group">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __("Email") }}">

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __("Password") }}">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link pl-0" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                            <div class="checkbox">
                                <label for="remember">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> {{ __("Remember me") }}
                                </label>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    {{ __("Log in") }}
                                </button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

