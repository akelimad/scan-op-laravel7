@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.settings.widgets'))

@section('head')
<style>
    body.dragging, body.dragging * {
        cursor: move !important;
    }

    .dragged {
        position: absolute;
        opacity: 0.5;
        z-index: 2000;
    }

    ol.widgets-list li.placeholder {
        position: relative;
    }
    ol.widgets-list li.placeholder:before {
        position: absolute;
    }
</style>
@stop

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-tint fa-fw"></i> {{ Lang::get('messages.admin.settings.widgets') }}
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

                        <div class="input-group">
                            <select class="widgets-select form-control">
                                <option value="none">{{ Lang::get('messages.admin.settings.widgets.select-widgets') }}</option>
                                <option value="site_description">{{ Lang::get('messages.admin.settings.widgets.site_description') }}</option>
                                <option value="social_buttons">{{ Lang::get('messages.admin.settings.widgets.social_buttons') }}</option>
                                <option value="top_rates">{{ Lang::get('messages.admin.settings.widgets.top_rates') }}</option>
                                <option value="top_views">{{ Lang::get('messages.admin.settings.widgets.top_views') }}</option>
                                <option value="custom_code">{{ Lang::get('messages.admin.settings.widgets.custom_code') }}</option>
                                <option value="tags">{{ Lang::get('messages.admin.manga.tags') }}</option>
                            </select>
                            <span class="input-group-btn">
                                <button class="btn btn-primary add-widget">{{ Lang::get('messages.admin.settings.widgets.add-widget') }}</button>
                            </span>
                        </div>
                        <hr/>
                        {{ Form::open(array('route' => 'admin.settings.widgets.save', 'role' => 'form')) }}
                        <ol class="widgets-list">
                            @foreach($widgets as $index=>$widget)
                            @if($widget->type == 'site_description')
                            <li class="highlight">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="fa fa-arrows"></i> {{Lang::get('messages.admin.settings.widgets.site_description.header')}}
                                        <button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>
                                        <input type="hidden" name="site.widgets[][type]" value="site_description"/>
                                    </div>
                                </div>
                            </li>
                            @elseif($widget->type == 'social_buttons')
                            <li class="highlight">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="fa fa-arrows"></i> {{Lang::get('messages.admin.settings.widgets.social_buttons.header') }}
                                        <button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>
                                        <input type="hidden" name="site.widgets[][type]" value="social_buttons"/>
                                    </div>
                                </div>
                            </li>
                            @elseif($widget->type == 'top_rates')
                            <li class="highlight">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="fa fa-arrows"></i> {{Lang::get('messages.admin.settings.widgets.top_rates.header') }}
                                        <button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>
                                        <input type="hidden" name="site.widgets[{{$index}}][type]" value="top_rates"/>
                                    </div>
                                    <div class="panel-body">
                                        <input class="form-control" type="text" name="site.widgets[{{$index}}][title]" placeholder="title" value="{{$widget->title}}"/>
                                        <input class="form-control" type="text" name="site.widgets[{{$index}}][number]" placeholder="number of items" value="{{$widget->number}}"/>
                                    </div>
                                </div>
                            </li>
                            @elseif($widget->type == 'top_views')
                            <li class="highlight">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="fa fa-arrows"></i> {{Lang::get('messages.admin.settings.widgets.top_views.header') }}
                                        <button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>
                                        <input type="hidden" name="site.widgets[{{$index}}][type]" value="top_views"/>
                                    </div>
                                    <div class="panel-body">
                                        <input class="form-control" type="text" name="site.widgets[{{$index}}][title]" placeholder="title" value="{{$widget->title}}"/>
                                        <input class="form-control" type="text" name="site.widgets[{{$index}}][number]" placeholder="number of items" value="{{$widget->number}}"/>
                                    </div>
                                </div>
                            </li>
                            @elseif($widget->type == 'custom_code')
                            <li class="highlight">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="fa fa-arrows"></i> {{Lang::get('messages.admin.settings.widgets.custom_code.header') }}
                                        <button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>
                                        <input type="hidden" name="site.widgets[{{$index}}][type]" value="custom_code"/>
                                    </div>
                                    <div class="panel-body">
                                        <input class="form-control" type="text" name="site.widgets[{{$index}}][title]" placeholder="title" value="{{$widget->title}}"/>
                                        <textarea class="form-control" name="site.widgets[{{$index}}][code]" placeholder="code here" rows="5">{{$widget->code}}</textarea>
                                    </div>
                                </div>
                            </li>
                            @elseif($widget->type == 'tags')
                            <li class="highlight">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <i class="fa fa-arrows"></i> {{Lang::get('messages.admin.manga.tags')}}
                                        <button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>
                                        <input type="hidden" name="site.widgets[][type]" value="tags"/>
                                    </div>
                                </div>
                            </li>
                            @endif
                            @endforeach
                        </ol>
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
{{ HTML::script('js/vendor/jquery-sortable-min.js') }}
<script>
    $(document).ready(function () {
        site_description = '<li class="highlight"><div class="panel panel-default">' +
                '<div class="panel-heading">' +
                '<i class="fa fa-arrows"></i> {{Lang::get("messages.admin.settings.widgets.site_description.header")}}' +
                '<button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>' +
                '<input type="hidden" name="site.widgets[][type]" value="site_description"/>' +
                '</div>' +
                '</div>' +
                '</li>';
        social_buttons = '<li class="highlight"><div class="panel panel-default">' +
                '<div class="panel-heading">' +
                '<i class="fa fa-arrows"></i> {{Lang::get("messages.admin.settings.widgets.social_buttons.header")}}' +
                '<button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>' +
                '<input type="hidden" name="site.widgets[][type]" value="social_buttons"/>' +
                '</div>' +
                '</div>' +
                '</li>';
        top_rates = '<li class="highlight"><div class="panel panel-default">' +
                '<div class="panel-heading">' +
                '<i class="fa fa-arrows"></i> {{Lang::get("messages.admin.settings.widgets.top_rates.header")}}' +
                '<button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>' +
                '<input type="hidden" name="site.widgets[IDX][type]" value="top_rates"/>' +
                '</div>' +
                '<div class="panel-body">' +
                '<input class="form-control" type="text" name="site.widgets[IDX][title]" placeholder="title"></input>' +
                '<input class="form-control" type="text" name="site.widgets[IDX][number]" placeholder="number of items"></input>' +
                '</div>' +
                '</div>' +
                '</li>';
        top_views = '<li class="highlight"><div class="panel panel-default">' +
                '<div class="panel-heading">' +
                '<i class="fa fa-arrows"></i> {{Lang::get("messages.admin.settings.widgets.top_views.header")}}' +
                '<button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>' +
                '<input type="hidden" name="site.widgets[IDX][type]" value="top_views"/>' +
                '</div>' +
                '<div class="panel-body">' +
                '<input class="form-control" type="text" name="site.widgets[IDX][title]" placeholder="title"></input>' +
                '<input class="form-control" type="text" name="site.widgets[IDX][number]" placeholder="number of items"></input>' +
                '</div>' +
                '</div>' +
                '</li>';
        custom_code = '<li class="highlight"><div class="panel panel-default">' +
                '<div class="panel-heading">' +
                '<i class="fa fa-arrows"></i> {{Lang::get("messages.admin.settings.widgets.custom_code.header")}}' +
                '<button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>' +
                '<input type="hidden" name="site.widgets[IDX][type]" value="custom_code"/>' +
                '</div>' +
                '<div class="panel-body">' +
                '<input class="form-control" type="text" name="site.widgets[IDX][title]" placeholder="title"></input>' +
                '<textarea class="form-control" name="site.widgets[IDX][code]" placeholder="code here" rows="5"></textarea>' +
                '</div>' +
                '</div>' +
                '</li>';
        tags = '<li class="highlight"><div class="panel panel-default">' +
                '<div class="panel-heading">' +
                '<i class="fa fa-arrows"></i> {{Lang::get("messages.admin.manga.tags")}}' +
                '<button class="pull-right delete-widget"><i class="fa fa-minus"></i></button>' +
                '<input type="hidden" name="site.widgets[][type]" value="tags"/>' +
                '</div>' +
                '</div>' +
                '</li>';
        $('body').on('click', '.delete-widget', function (e) {
            e.preventDefault();
            $(this).parents('.highlight').remove();
        });

        $('.add-widget').click(function (e) {
            e.preventDefault();

            widgets_select = $('.widgets-select').val();
            switch (widgets_select) {
                case 'site_description':
                    $('.widgets-list').append(site_description);
                    break;
                case 'social_buttons':
                    $('.widgets-list').append(social_buttons);
                    break;
                case 'top_rates':
                    $('.widgets-list').append(top_rates.replace(/IDX/g, new Date().getTime()));
                    break;
                case 'top_views':
                    $('.widgets-list').append(top_views.replace(/IDX/g, new Date().getTime()));
                    break;
                case 'custom_code':
                    $('.widgets-list').append(custom_code.replace(/IDX/g, new Date().getTime()));
                    break;
                case 'tags':
                    $('.widgets-list').append(tags);
                    break;
            }
        });
    });

    $("ol.widgets-list").sortable();
</script>
@stop