@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render())

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-key fa-fw"></i> {{ Lang::get('messages.admin.users.permissions') }}
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ Lang::get('messages.admin.users.permission') }}</th>
                                        <th>{{ Lang::get('messages.admin.users.slug') }}</th>
                                    </tr>
                                </thead>
                                @foreach($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->display_name }}</td>
                                    <td>{{ $permission->name }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>
@stop