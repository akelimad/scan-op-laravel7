@extends('admin.layouts.default')

@section('head')
{{ HTML::style('css/bootstrap-select.min.css') }}

{{ HTML::script('js/vendor/bootstrap-select.min.js') }}
@stop

@section('breadcrumbs', Breadcrumbs::render())

@section('content')
<div>
    <div class="row">
        <div class="col-xs-12">
            @if (Session::has('createSuccess'))
            <div class="alert text-center alert-info ">
                {{ Session::get('createSuccess') }}
            </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-users fa-fw"></i> {{ Lang::get('messages.admin.posts.manage') }}
                    <div class="@if(Config::get('orientation') === 'rtl') pull-left  @else pull-right @endif">
                        {{ link_to_route('admin.posts.create', Lang::get('messages.admin.posts.create'), null, array('class' => 'btn btn-primary btn-xs pull-right', 'role' => 'button')) }}
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;">{{ Lang::get('messages.admin.posts.title') }}</th>
                                            <th>{{ Lang::get('messages.admin.posts.owner') }}</th>
                                            <th>{{ Lang::get('messages.admin.posts.status') }}</th>
                                            <th>{{ Lang::get('messages.admin.posts.published') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    @if(count($posts)>0)
                                    @foreach($posts as $post)
                                    <tr>
                                        <td>{{ $post->title }}</td>
                                        <td>{{ $post->user->username }}</td>
                                        <td>@if($post->status == 1) <span class="label label-success">{{ Lang::get('messages.admin.posts.status-published') }}</span> @else <span class="label label-danger">{{ Lang::get('messages.admin.posts.status-disabled') }}</span> @endif </td>
                                        <td>{{ $post->created_at }}</td>
                                        <td style="text-align: right;"> 
                                            {{ link_to_route('admin.posts.edit', Lang::get('messages.admin.category.edit'), $post->id, array('class' => 'btn btn-primary btn-xs')); }}
                                            <div style="display: inline-block">
                                                {{ Form::open(array('route' => array('admin.posts.destroy', $post->id), 'method' => 'delete')) }}
                                                {{ Form::submit(Lang::get('messages.admin.category.delete'), array('class' => 'btn btn-danger btn-xs',  'onclick' => 'if (!confirm("'.Lang::get('messages.admin.posts.confirm-delete').'")) {return false;}')) }}
                                                {{ Form::close() }}
                                            </div> 
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    {{$posts->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
        </div>
    </div>
</div>
@stop
