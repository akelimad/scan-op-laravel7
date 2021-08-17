@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render())

@section('content')
<div class="row">
    <div class="col-lg-12">
        @if (Session::has('createSuccess'))
        <div class="alert text-center alert-info ">
            {{ Session::get('createSuccess') }}
        </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bars fa-fw"></i> {{ Lang::get('messages.admin.users.roles') }}
                <div class="pull-right">
                    {{ link_to_route('admin.role.create', Lang::get('messages.admin.users.roles.add'), null, array('class' => 'btn btn-primary btn-xs pull-right', 'role' => 'button')) }}
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
                                        <th style="min-width: 200px;">{{ Lang::get('messages.admin.users.roles.role') }}</th>
                                        <th>{{ Lang::get('messages.admin.users.permissions') }}</th>
                                        <th style="min-width: 200px;"></th>
                                    </tr>
                                </thead>
                                @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ App::make("HelperController")->listAsString($role->perms, ', ') }}</td>
                                    <td style="text-align: right;"> {{ link_to_route('admin.role.edit', Lang::get('messages.admin.users.edit'), $role->id, array('class' => 'btn btn-primary btn-xs')); }}
                                        <div style="display: inline-block">
                                            {{ Form::open(array('route' => array('admin.role.destroy', $role->id), 'method' => 'delete')) }}
                                            {{ Form::submit(Lang::get('messages.admin.users.delete') , array('class' => 'btn btn-danger btn-xs',  'onclick' => 'if (!confirm("'.Lang::get('messages.admin.users.roles.confirm-delete').'")) {return false;}')) }}
                                            {{ Form::close() }}
                                        </div></td>
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