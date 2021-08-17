@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render())

@section('content')
<div class="row">
    <div class="col-lg-12">
        @if (Session::has('updateSuccess'))
        <div class="alert text-center alert-info ">
            {{ Session::get('updateSuccess') }}
        </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-pencil-square-o fa-fw"></i> {{ Lang::get('messages.admin.users.edit-user') }}
                <div class="pull-right">
                    {{ link_to_route('admin.user.index', Lang::get('messages.admin.manga.back'), null, array('class' => 'btn btn-default btn-xs', 'role' => 'button')) }}
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">

                {{ Form::open(array('route' => array('admin.user.update', $user->id), 'method' => 'PUT')) }}
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {{Form::label('username', Lang::get('messages.admin.settings.profile.username'))}}
                            {{Form::text('username', $user->username, array('class' => 'form-control'))}}
                            {{ $errors->first('username', '<label class="error" for="name">:message</label>') }}
                        </div>
                        <div class="form-group">
                            {{Form::label('email', Lang::get('messages.admin.settings.profile.email'))}}
                            <input name="email" type="email" class="form-control" value="{{$user->email}}"/>
                            {{ $errors->first('email', '<label class="error" for="name">:message</label>') }}
                        </div>
                        <div class="form-group">
                            <label style="margin-right: 10px;">{{ Lang::get('messages.admin.users.account-status') }}</label>
                            <label class="radio-inline">
                                <input type="radio" name="confirmed" value="1" <?php if ($user->confirmed == 1): ?>
                                       checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.enabled') }}</label>
                            <label class="radio-inline">
                                <input type="radio" name="confirmed" value="0" <?php if ($user->confirmed == 0): ?>
                                       checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.disabled') }}</label>
                        </div>
                    </div>
                </div>

                <br/>

                <label>{{ Lang::get('messages.admin.users.select-roles') }}</label>
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-1">
                        <div class="list-group" id="list1">
                            <div class="list-group-item active">
                                {{ Lang::get('messages.admin.users.roles') }}
                                <input title="toggle all" class="all pull-right" type="checkbox">
                            </div>
                            @if(count($roles)>0)
                            @foreach ($roles as $role)
                            <div class="list-group-item" data-id="{{ $role->id }}">
                                {{ $role->name }}
                                <input class="pull-right" type="checkbox">
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-2 v-center">
                        <div title="Send to list 2" class="btn btn-default center-block add">
                            <i class="glyphicon glyphicon-chevron-right"></i>
                        </div>
                        <div title="Send to list 1" class="btn btn-default center-block remove">
                            <i class="glyphicon glyphicon-chevron-left"></i>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="list-group" id="list2">
                            <div class="list-group-item active">
                                {{ Lang::get('messages.admin.users.user-roles') }}
                                <input title="toggle all" class="all pull-right" type="checkbox">
                            </div>
                            @if(count($user->roles)>0)
                            @foreach ($user->roles as $role)
                            <div class="list-group-item" data-id="{{ $role->id }}">
                                {{ $role->name }}
                                <input class="pull-right" type="checkbox">
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        {{ Form::submit(Lang::get('messages.admin.settings.update'), array('class' => 'btn btn-primary center-block save', 'style' => 'width: 100%')) }}
                    </div>
                </div>
                {{ Form::hidden('roles', '', array('id' => 'roles')) }}
                {{ Form::close() }}
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>

<script>
    function updateRoles() {
        var roles = Array();
        $("#list2 .list-group-item").each(function (idx, item) {
            id = $(item).attr('data-id');
            if (id !== undefined)
                roles.push($(item).attr('data-id'));
        });

        $('#roles').val(roles);
    }

    updateRoles();

    $('.add').click(function () {
        $('.all').prop("checked", false);
        var items = $("#list1 input:checked:not('.all')");
        var n = items.length;
        if (n > 0) {
            items.each(function (idx, item) {
                var choice = $(item);
                choice.prop("checked", false);
                choice.parent().appendTo("#list2");
            });

            updateRoles();
        } else {
            alert("Choose an item from list 1");
        }
    });

    $('.remove').click(function () {
        $('.all').prop("checked", false);
        var items = $("#list2 input:checked:not('.all')");
        items.each(function (idx, item) {
            var choice = $(item);
            choice.prop("checked", false);
            choice.parent().appendTo("#list1");
        });

        updateRoles();
    });

    /* toggle all checkboxes in group */
    $('.all').click(function (e) {
        e.stopPropagation();
        var $this = $(this);
        if ($this.is(":checked")) {
            $this.parents('.list-group').find("[type=checkbox]").prop("checked", true);
        } else {
            $this.parents('.list-group').find("[type=checkbox]").prop("checked", false);
            $this.prop("checked", false);
        }
    });

    $('[type=checkbox]').click(function (e) {
        e.stopPropagation();
    });

    /* toggle checkbox when list group item is clicked */
    $('.list-group a').click(function (e) {

        e.stopPropagation();

        var $this = $(this).find("[type=checkbox]");
        if ($this.is(":checked")) {
            $this.prop("checked", false);
        } else {
            $this.prop("checked", true);
        }

        if ($this.hasClass("all")) {
            $this.trigger('click');
        }
    });
</script>
@stop