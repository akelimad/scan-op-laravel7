@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.comictype.edit'))

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-folder-open fa-fw"></i> {{ Lang::get('messages.admin.comictype.types') }}
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-5">
                        {{ Form::open(array('route' => array('admin.comictype.update', $type->id), 'method' => 'PUT')) }}
                        <div class="form-group">
                            {{Form::label('label', Lang::get('messages.admin.comictype.label'))}}
                            {{Form::text('label', $type->label, array('class' => 'form-control'))}}
                            {{ $errors->first('label', '<label class="error" for="name">:message</label>') }}
                        </div>

                        <div class="actionBtn">
                            {{ link_to_route('admin.manga.index', Lang::get('messages.admin.category.back'), null, array('class' => 'btn btn-default btn-xs')); }}

                            {{Form::submit(Lang::get('messages.admin.comictype.update-type'), array('class' => 'btn btn-primary btn-xs'))}}
                        </div>
                        {{ Form::close() }}
                    </div>
                    <div class="col-lg-7">
                        @if (count($types)>0)
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ Lang::get('messages.admin.comictype.label') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @foreach ($types as $type)
                            <tr>
                                <td>
                                    {{ $type->label }}
                                </td>
                                <td>
                                    {{ link_to_route('admin.comictype.edit', Lang::get('messages.admin.comictype.edit'), $type->id, array('class' => 'btn btn-primary btn-xs')); }}

                                    <div style="display: inline-block">
                                        {{ Form::open(array('route' => array('admin.comictype.destroy', $type->id), 'method' => 'delete')) }}
                                        {{ Form::submit(Lang::get('messages.admin.comictype.delete'), array('class' => 'btn btn-danger btn-xs',  'onclick' => 'if (!confirm("'. Lang::get('messages.admin.comictype.confirm-delete') .'")) {return false;}')) }}
                                        {{ Form::close() }}
                                    </div>   
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        @else
                        <p>{{ Lang::get('messages.admin.comictype.no-type') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
@stop