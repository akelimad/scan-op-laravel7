@extends('admin.layouts.default')

@section('head')
{{ HTML::style('css/bootstrap-select.min.css') }}

{{ HTML::script('js/vendor/bootstrap-select.min.js') }}
@stop

@section('breadcrumbs', Breadcrumbs::render())

@section('content')
<div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#users" aria-controls="users" role="tab" data-toggle="tab">{{ Lang::get('messages.admin.users.users') }}</a>
        </li>
        <li role="presentation">
            <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">{{ Lang::get('messages.admin.users.options') }}</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="users">
            <div class="row">
                <div class="col-xs-12">
                    @if (Session::has('createSuccess'))
                    <div class="alert text-center alert-info ">
                        {{ Session::get('createSuccess') }}
                    </div>
                    @endif

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-users fa-fw"></i> {{ Lang::get('messages.admin.users.users') }}
                            <div class="pull-right">
                                {{ link_to_route('admin.user.create', Lang::get('messages.admin.users.add'), null, array('class' => 'btn btn-primary btn-xs pull-right', 'role' => 'button')) }}
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
                                                    <th>{{ Lang::get('messages.admin.users.username') }}</th>
                                                    <th>{{ Lang::get('messages.admin.users.email') }}</th>
                                                    <th>{{ Lang::get('messages.admin.users.roles') }}</th>
                                                    <th>{{ Lang::get('messages.admin.users.status') }}</th>
                                                    <th>{{ Lang::get('messages.admin.users.manga') }}</th>
                                                    <th>{{ Lang::get('messages.admin.users.chapters') }}</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            @if(count($users)>0)
                                            @foreach($users as $user)
                                            <tr>
                                                <td>{{ $user->username }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ App::make("HelperController")->listAsString($user->roles, ', ') }}</td>
                                                <td>@if($user->confirmed == 1) <span class="label label-success">{{ Lang::get('messages.admin.users.enabled') }}</span> @else <span class="label label-danger">{{ Lang::get('messages.admin.users.disabled') }}</span> @endif </td>
                                                <td>{{ count($user->manga) }}</td>
                                                <td>{{ count($user->chapters) }}</td>
                                                <td style="text-align: right;"> @if($user->id != 1)
                                                    {{ link_to_route('admin.user.edit', Lang::get('messages.admin.users.edit'), $user->id, array('class' => 'btn btn-primary btn-xs')); }}
                                                    <div style="display: inline-block">
                                                        {{ Form::open(array('route' => array('admin.user.destroy', $user->id), 'method' => 'delete')) }}
                                                        {{ Form::submit(Lang::get('messages.admin.users.delete'), array('class' => 'btn btn-danger btn-xs',  'onclick' => 'if (!confirm("'.Lang::get('messages.admin.users.confirm-delete').'")) {return false;}')) }}
                                                        {{ Form::close() }}
                                                    </div> @endif</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="settings">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    {{ Form::open(array('route' => 'admin.users.subsciption')) }}
                                    <div class="form-group">
                                        <label style="vertical-align: text-top;">{{ Lang::get('messages.admin.users.options.allo-subscribe') }}</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="subscribe" value="true" <?php if ($subscription->subscribe === 'true'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.options.yes') }}</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="subscribe" value="false" <?php if ($subscription->subscribe === 'false'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.options.no') }}</label>
                                    </div>
                                    <hr/>
                                    <p>
                                        {{ Lang::get('messages.admin.users.options.new-account') }}
                                    </p>
                                    <div class="form-group">
                                        <label style="vertical-align: text-top;">{{ Lang::get('messages.admin.users.options.admin-activate-it') }}</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="admin_confirm" value="true" <?php if ($subscription->admin_confirm === 'true'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.options.yes') }}</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="admin_confirm" value="false" <?php if ($subscription->admin_confirm === 'false'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.options.no') }}</label>
                                    </div>
                                    <div class="form-group">
                                        <label style="vertical-align: text-top;">{{ Lang::get('messages.admin.users.options.send-confim-email') }}</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="email_confirm" value="true" <?php if ($subscription->email_confirm === 'true'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.options.yes') }}</label>
                                        <label class="radio-inline">
                                            <input type="radio" name="email_confirm" value="false" <?php if ($subscription->email_confirm === 'false'): ?>
                                                   checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.options.no') }}</label>
                                    </div>
                                    <div class="form-group">
                                        {{Form::label('default_role', Lang::get('messages.admin.users.options.default-role'))}}
                                        {{Form::select('default_role', $roles, $subscription->default_role, array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                    </div>
                                    <hr/>
                                    <p>
                                        {{ Lang::get('messages.admin.users.options.mail-conf') }}
                                    </p>
                                    <div id="mailing" class="form-group">
                                        <div class="form-group">
                                            <label>{{ Lang::get('messages.admin.users.options.address') }}</label>
                                            <input type="text" name="address" value="{{ $subscription->address }}" class="form-control"/>
                                        </div>
                                        <div class="form-group">
                                            <label>{{ Lang::get('messages.admin.users.options.name') }}</label>
                                            <input type="text" name="name" value="{{ $subscription->name }}" class="form-control"/>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="mailing" value="sendmail" <?php if ($subscription->mailing === 'sendmail'): ?>
                                                       checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.options.use-php-mail') }}</label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="mailing" value="smtp" <?php if ($subscription->mailing === 'smtp'): ?>
                                                       checked="checked"<?php endif ?>/>{{ Lang::get('messages.admin.users.options.config-smtp') }}</label>
                                        </div>

                                        <div id="smtp-conf" style="display: none;">
                                            <div class="form-group">
                                                <label>{{ Lang::get('messages.admin.users.options.host') }}</label>
                                                <input type="text" name="host" value="{{ $subscription->host }}" class="form-control"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ Lang::get('messages.admin.users.options.port') }}</label>
                                                <input type="text" name="port" value="{{ $subscription->port }}" class="form-control"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ Lang::get('messages.admin.users.options.username') }}</label>
                                                <input type="text" name="username" value="{{ $subscription->username }}" class="form-control"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ Lang::get('messages.admin.users.options.password') }}</label>
                                                <input type="password" name="password" value="{{ $subscription->password }}" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                {{ Form::submit(Lang::get('messages.admin.settings.update'), array('class' => 'btn btn-primary center-block save', 'style' => 'width: 100%')) }}
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        smtpChecked();
    });

    $('#mailing input[type="radio"]').change(function () {
        smtpChecked();
    });

    function smtpChecked() {
        if ($('#mailing input[value="smtp"]').is(':checked')) {
            $('#smtp-conf').show();
        } else {
            $('#smtp-conf').hide();
        }
    }
</script>
@stop
