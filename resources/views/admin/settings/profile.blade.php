@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.settings.profile'))

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-user fa-fw"></i> {{ Lang::get('messages.admin.settings.profile.header') }}
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        @if (Session::has('updateSuccess'))
                        <div class="alert text-center alert-info ">
                            {{ Session::get('updateSuccess') }}
                        </div>
                        @endif

                        {{ Form::open(array('route' => 'admin.settings.profile.save', 'role' => 'form')) }}
                        <div class="form-group">
                            {{ Form::label('name', Lang::get('messages.admin.settings.profile.name')) }}
                            {{ Form::text('name', $user->name, array('class' => 'form-control')) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('username', Lang::get('messages.admin.settings.profile.username')) }}
                            {{ Form::text('username', $user->username, array('class' => 'form-control')) }}
                            {{ $errors->first('username', '<label class="error" for="username">:message</label>') }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('password', Lang::get('messages.admin.settings.profile.pwd')) }}
                            {{ Form::password('password', array('class' => 'form-control')) }}
                            {{ $errors->first('password', '<label class="error" for="password">:message</label>') }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('email', Lang::get('messages.admin.settings.profile.email')) }}
                            {{ Form::text('email', $user->email, ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::submit(Lang::get('messages.admin.settings.update'), ['class' => 'btn btn-primary']) }}
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
@stop