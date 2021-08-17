@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.settings.cache'))

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-tint fa-fw"></i> {{ Lang::get('messages.admin.settings.cache') }}
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

                        {{ Form::open(array('route' => 'admin.settings.cache.clear', 'role' => 'form')) }}
                        <div class="form-group">
                            {{ Form::submit(Lang::get('messages.admin.settings.cache.clear'), ['class' => 'btn btn-danger submit']) }}
                        </div>
                        {{ Form::close() }}

                        {{ Form::open(array('route' => 'admin.settings.cache.save', 'role' => 'form')) }}
                        <div class="form-group">
                            {{Form::label('site.cache[reader]', Lang::get('messages.admin.settings.cache.reader'))}}
                            {{Form::number('site.cache[reader]', isset($cache->reader)?$cache->reader:60, ['min' => '0', 'aria-describedby' => 'helpReader', 'class' => 'form-control'])}}
                            <span id="helpReader" class="help-block">{{Lang::get('messages.admin.settings.cache.reader.help')}}</span>
                        </div>
                        <div class="form-group">
                            {{ Form::submit(Lang::get('messages.admin.settings.save'), ['class' => 'btn btn-primary submit']) }}
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