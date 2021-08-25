@extends('admin.layouts.auth')

@section('head')
    <title>{{ __("Reset Password") }}</title>
@stop

@section('content')
    <div class="col-md-6 col-md-offset-3 mt-5">
        <h3 class="text-center mb-5">{{ \App\Option::get('site.name') }}</h3>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center">{{ __("Reset Password") }}</h3>
            </div>
            <div class="panel-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
@stop

