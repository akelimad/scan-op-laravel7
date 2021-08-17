@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.category.index'))

@section('content')
<div class="row">
    <div class="col-sm-12">
        @if (Session::has('msgSuccess'))
        <div class="alert text-center alert-info ">
            {{ Session::get('msgSuccess') }}
        </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-folder-open fa-fw"></i> {{ Lang::get('messages.admin.category.categories') }}
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-5">
                        {{ Form::open(array('route' => 'admin.category.store', 'role' => 'form')) }}
                        <div class="form-group">
                            {{Form::label('name', Lang::get('messages.admin.category.name'))}}
                            {{Form::text('name','', array('class' => 'form-control'))}}
                            {{ $errors->first('name', '<label class="error" for="name">:message</label>') }}
                        </div>

                        <div class="form-group">
                            {{Form::label('slug', Lang::get('messages.admin.category.slug'))}}
                            {{Form::text('slug','', array('class' => 'form-control', 'placeholder' => Lang::get('messages.admin.category.slug-placeholder')))}}
                            {{ $errors->first('slug', '<label class="error" for="slug">:message</label>') }}
                        </div>
                        <div class="actionBtn">
                            {{ link_to_route('admin.manga.index', Lang::get('messages.admin.category.back'), null, array('class' => 'btn btn-default btn-xs')); }}

                            {{Form::submit(Lang::get('messages.admin.category.create-category'), array('class' => 'btn btn-primary btn-xs'))}}
                        </div>
                        {{ Form::close() }}
                    </div>
                    <div class="col-md-7">
                        @if (count($categories)>0)
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ Lang::get('messages.admin.category.name') }}</th>
                                    <th>{{ Lang::get('messages.admin.category.slug') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @foreach ($categories as $category)
                            <tr>
                                <td>
                                    {{ $category->name }}
                                </td>
                                <td>
                                    {{ $category->slug }}
                                </td>
                                <td>
                                    {{ link_to_route('admin.category.edit', Lang::get('messages.admin.category.edit'), $category->id, array('class' => 'btn btn-primary btn-xs')); }}

                                    <div style="display: inline-block">
                                        {{ Form::open(array('route' => array('admin.category.destroy', $category->id), 'method' => 'delete')) }}
                                        {{ Form::submit(Lang::get('messages.admin.category.delete'), array('class' => 'btn btn-danger btn-xs',  'onclick' => 'if (!confirm("'. Lang::get('messages.admin.category.confirm-delete') .'")) {return false;}')) }}
                                        {{ Form::close() }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @else
                        <p>{{ Lang::get('messages.admin.category.no-category') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
@stop