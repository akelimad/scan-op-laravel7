@extends('admin.layouts.default')

@section('head')
{{ HTML::style('css/bootstrap-select.min.css') }}

{{ HTML::script('js/vendor/bootstrap-select.min.js') }}
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.settings.ads'))

@section('content')
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
        <a href="#ads-block" aria-controls="ads-block" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.ads.ad-blocks')}}</a>
    </li>
    <li role="presentation">
        <a href="#ads-placement" aria-controls="ads-placement" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.ads.ad-placements')}}</a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="ads-block">
        <div class="row">
            <div class="col-xs-12">
                @if (Session::has('updateSuccess'))
                <div class="alert text-center alert-info ">
                    {{ Session::get('updateSuccess') }}
                </div>
                @endif

                {{ Form::open(array('route' => 'admin.ads.store', 'role' => 'form', 'id' => 'ads')) }}
                @if(count($ads)>0)
                @foreach($ads as $index => $ad)
                <div class="col-xs-6 bloc">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="#" class="pull-right remove-bloc" title="remove bloc"><i class="fa fa-minus"></i></a>{{Lang::get('messages.admin.settings.ads.block')}} {{$ad->bloc_id}}
                        </div>
                        <div class="panel-body">
                            <input name="bloc_id[]" class="form-control" type="text" placeholder="{{Lang::get('messages.admin.settings.ads.block-id')}}" value="{{$ad->bloc_id}}"/>
                            <br/>
                            <textarea name="code[]" class="form-control" rows="5" placeholder="{{Lang::get('messages.admin.settings.ads.block-code')}}">{{$ad->code}}</textarea>
                            <input name="id[]" type="hidden" value="{{$ad->id}}"/>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
                <div class="form-group action" style="clear: both;">
                    <button class="btn btn-default add-bloc"><i class="fa fa-plus"></i>{{Lang::get('messages.admin.settings.ads.add-block')}}</button>
                    {{ Form::submit(Lang::get('messages.admin.settings.save'), ['class' => 'btn btn-primary']) }}
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="ads-placement">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#reader" aria-controls="reader" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.ads.reader-page')}}</a>
            </li>
            <li role="presentation">
                <a href="#homepage" aria-controls="homepage" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.ads.homepage')}}</a>
            </li>
            <li role="presentation">
                <a href="#manga-info" aria-controls="manga-info" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.ads.info-page')}}</a>
            </li>
        </ul>

        <!-- Tab panes -->
        {{ Form::open(array('route' => 'admin.ads.storePlacements', 'role' => 'form', 'id' => 'placement')) }}
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="reader">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="body">
                            <div class="header"></div>
                            <div class="top">
                                {{Form::select('reader[TOP_LARGE]', $adsList, isset($placements['reader']['TOP_LARGE'])?$placements['reader']['TOP_LARGE']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                {{Form::select('reader[TOP_SQRE_1]', $adsList, isset($placements['reader']['TOP_SQRE_1'])?$placements['reader']['TOP_SQRE_1']:'', array('class' => 'selectpicker', 'data-width' => '48%'))}}
                                {{Form::select('reader[TOP_SQRE_2]', $adsList, isset($placements['reader']['TOP_SQRE_2'])?$placements['reader']['TOP_SQRE_2']:'', array('class' => 'selectpicker pull-right', 'data-width' => '48%'))}}
                            </div>
                            <div class="left">
                                <i class="fa fa-image scan"></i>
                                <div class="left-ads">
                                    {{Form::select('reader[LEFT_WIDE_1]', $adsList, isset($placements['reader']['LEFT_WIDE_1'])?$placements['reader']['LEFT_WIDE_1']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                    {{Form::select('reader[LEFT_WIDE_2]', $adsList, isset($placements['reader']['LEFT_WIDE_2'])?$placements['reader']['LEFT_WIDE_2']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                </div>
                                <div class="right-ads">
                                    {{Form::select('reader[RIGHT_WIDE_1]', $adsList, isset($placements['reader']['RIGHT_WIDE_1'])?$placements['reader']['RIGHT_WIDE_1']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                    {{Form::select('reader[RIGHT_WIDE_2]', $adsList, isset($placements['reader']['RIGHT_WIDE_2'])?$placements['reader']['RIGHT_WIDE_2']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                </div>
                            </div>
                            <div class="bottom">
                                {{Form::select('reader[BOTTOM_LARGE]', $adsList, isset($placements['reader']['BOTTOM_LARGE'])?$placements['reader']['BOTTOM_LARGE']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                {{Form::select('reader[BOTTOM_SQRE_1]', $adsList, isset($placements['reader']['BOTTOM_SQRE_1'])?$placements['reader']['BOTTOM_SQRE_1']:'', array('class' => 'selectpicker', 'data-width' => '48%'))}}
                                {{Form::select('reader[BOTTOM_SQRE_2]', $adsList, isset($placements['reader']['BOTTOM_SQRE_2'])?$placements['reader']['BOTTOM_SQRE_2']:'', array('class' => 'selectpicker pull-right', 'data-width' => '48%'))}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="homepage">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="body">
                            <div class="header"></div>
                            <div class="wrapper">
                                <div class="pull-left cntr-left">
                                    <div class="cntr1">
                                        hot manga updates
                                    </div>
                                    <div style="width: 90%; margin: 0 auto;">
                                        {{Form::select('homepage[TOP_LARGE]', $adsList, isset($placements['homepage']['TOP_LARGE'])?$placements['homepage']['TOP_LARGE']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                        {{Form::select('homepage[TOP_SQRE_1]', $adsList, isset($placements['homepage']['TOP_SQRE_1'])?$placements['homepage']['TOP_SQRE_1']:'', array('class' => 'selectpicker', 'data-width' => '48%'))}}
                                        {{Form::select('homepage[TOP_SQRE_2]', $adsList, isset($placements['homepage']['TOP_SQRE_2'])?$placements['homepage']['TOP_SQRE_2']:'', array('class' => 'selectpicker pull-right', 'data-width' => '48%'))}}
                                    </div>
                                    <div class="cntr2">
                                        latest manga updates
                                    </div>
                                    <div style="width: 90%; margin: 0 auto;">
                                        {{Form::select('homepage[BOTTOM_LARGE]', $adsList, isset($placements['homepage']['BOTTOM_LARGE'])?$placements['homepage']['BOTTOM_LARGE']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                        {{Form::select('homepage[BOTTOM_SQRE_1]', $adsList, isset($placements['homepage']['BOTTOM_SQRE_1'])?$placements['homepage']['BOTTOM_SQRE_1']:'', array('class' => 'selectpicker', 'data-width' => '48%'))}}
                                        {{Form::select('homepage[BOTTOM_SQRE_2]', $adsList, isset($placements['homepage']['BOTTOM_SQRE_2'])?$placements['homepage']['BOTTOM_SQRE_2']:'', array('class' => 'selectpicker pull-right', 'data-width' => '48%'))}}
                                    </div>
                                </div>
                                <div class="pull-right" style="width: 30%;">
                                    <div style="width: 90%; margin: 40px auto 0;">
                                        {{Form::select('homepage[RIGHT_SQRE_1]', $adsList, isset($placements['homepage']['RIGHT_SQRE_1'])?$placements['homepage']['RIGHT_SQRE_1']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                        {{Form::select('homepage[RIGHT_WIDE_1]', $adsList, isset($placements['homepage']['RIGHT_WIDE_1'])?$placements['homepage']['RIGHT_WIDE_1']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                    </div>
                                    <div class="cntr2">
                                        content
                                    </div>
                                    <div style="width: 90%; margin: 0 auto;">
                                        {{Form::select('homepage[RIGHT_SQRE_2]', $adsList, isset($placements['homepage']['RIGHT_SQRE_2'])?$placements['homepage']['RIGHT_SQRE_2']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                        {{Form::select('homepage[RIGHT_WIDE_2]', $adsList, isset($placements['homepage']['RIGHT_WIDE_2'])?$placements['homepage']['RIGHT_WIDE_2']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="manga-info">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="body">
                            <div class="header"></div>
                            <div class="wrapper">
                                <div class="pull-left" style="height: 100%; width: 100%;">
                                    <div class="cntr1">
                                        manga info
                                    </div>
                                    <div style="width: 90%; margin: 0 auto;">
                                        {{Form::select('info[TOP_LARGE]', $adsList, isset($placements['info']['TOP_LARGE'])?$placements['info']['TOP_LARGE']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                        {{Form::select('info[TOP_SQRE_1]', $adsList, isset($placements['info']['TOP_SQRE_1'])?$placements['info']['TOP_SQRE_1']:'', array('class' => 'selectpicker', 'data-width' => '48%'))}}
                                        {{Form::select('info[TOP_SQRE_2]', $adsList, isset($placements['info']['TOP_SQRE_2'])?$placements['info']['TOP_SQRE_2']:'', array('class' => 'selectpicker pull-right', 'data-width' => '48%'))}}
                                    </div>
                                    <div class="cntr2">
                                        manga chapters
                                    </div>
                                    <div style="width: 90%; margin: 0 auto;">
                                        {{Form::select('info[BOTTOM_LARGE]', $adsList, isset($placements['info']['BOTTOM_LARGE'])?$placements['info']['BOTTOM_LARGE']:'', array('class' => 'selectpicker', 'data-width' => '100%'))}}
                                        {{Form::select('info[BOTTOM_SQRE_1]', $adsList, isset($placements['info']['BOTTOM_SQRE_1'])?$placements['info']['BOTTOM_SQRE_1']:'', array('class' => 'selectpicker', 'data-width' => '48%'))}}
                                        {{Form::select('info[BOTTOM_SQRE_2]', $adsList, isset($placements['info']['BOTTOM_SQRE_2'])?$placements['info']['BOTTOM_SQRE_2']:'', array('class' => 'selectpicker pull-right', 'data-width' => '48%'))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group action" style="clear: both;">
            {{ Form::submit(Lang::get('messages.admin.settings.ads.save-all-placements'), ['class' => 'btn btn-primary']) }}
        </div>
        {{ Form::close() }}
    </div>
</div>

<script>
    var bloc_template = '<div class="col-xs-6 bloc">' +
            '<div class="panel panel-default">' +
            '<div class="panel-heading">' +
            '<a href="#" class="pull-right remove-bloc" title="remove bloc"><i class="fa fa-minus"></i></a> New bloc' +
            '</div>' +
            '<div class="panel-body">' +
            '<input name="bloc_id[]" class="form-control" type="text" placeholder="Bloc identifier" value=""/>' +
            '<br/>' +
            '<textarea name="code[]" class="form-control" rows="5" placeholder="Your code here"></textarea>' +
            '</div>' +
            '</div>' +
            '</div>';

    $(document).ready(function () {
        $('.add-bloc').click(function (e) {
            e.preventDefault();

            $('form#ads .action').before(bloc_template);
        });

        $('form#ads').on('click', '.remove-bloc', function (e) {
            e.preventDefault();

            parent = $(this).parents('.bloc');
            if (parent.find('input[type="hidden"]').length) {
                id = parent.find('input[type="hidden"]').val();
                if (confirm('are you sure to delete this bloc?')) {
                    $.ajax({
                        url: "{{url('/admin/ads') }}" + '/' + id,
                        type: 'post',
                        data: {_method: 'delete'},
                        success: function () {
                            parent.remove();
                        }
                    });
                }
            } else {
                parent.remove();
            }
        });
    });
</script>
@stop