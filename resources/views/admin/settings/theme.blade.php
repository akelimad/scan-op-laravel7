@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.settings.theme'))

@section('head')
{{ HTML::style('css/bootstrap-select.min.css') }}

{{ HTML::script('js/vendor/bootstrap-select.min.js') }}
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-tint fa-fw"></i> {{ Lang::get('messages.admin.settings.theme.header') }}
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

                        {{ Form::open(array('route' => 'admin.settings.theme.save', 'role' => 'form')) }}
                        <div class="form-group">
                            {{Form::label('site.theme', Lang::get('messages.admin.settings.theme.select-theme'))}}
                            <br/>
                            {{Form::select('site.theme', $themes, $options['site.theme'], array('class' => 'selectpicker', 'data-width' => 'auto', 'data-size' => 'false'))}}
                        </div>

                        <div class="form-group">
                            {{ Form::submit(Lang::get('messages.admin.settings.save'), ['class' => 'btn btn-primary']) }}
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