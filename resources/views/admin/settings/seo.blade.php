@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.settings.seo'))

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-google fa-fw"></i> {{ Lang::get('messages.admin.settings.seo.header') }}
            </div>

            <br/>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist" style="padding:0 10px;">
                <li role="presentation" class="active">
                    <a href="#general" aria-controls="general" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.seo.global')}}</a>
                </li>
                <li role="presentation">
                    <a href="#advanced" aria-controls="advanced" role="tab" data-toggle="tab">{{Lang::get('messages.admin.settings.seo.advanced')}}</a>
                </li>
            </ul>

            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        @if (Session::has('updateSuccess'))
                        <div class="alert text-center alert-info ">
                            {{ Session::get('updateSuccess') }}
                        </div>
                        @endif

                        {{ Form::open(array('route' => 'admin.settings.seo.save', 'role' => 'form')) }}
                        <div class="tab-content">
                            <!-- general -->
                            <div role="tabpanel" class="tab-pane active" id="general">
                                <div class="form-group">
                                    {{ Form::label('seo.title', Lang::get('messages.admin.settings.seo.title')) }}
                                    {{ Form::text('seo.title', $options['seo.title'], array('class' => 'form-control')) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('seo.keywords', Lang::get('messages.admin.settings.seo.keywords')) }}
                                    {{ Form::text('seo.keywords', $options['seo.keywords'], array('class' => 'form-control', 'placeholder' => 'comma separated')) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('seo.description', Lang::get('messages.admin.settings.seo.description')) }}
                                    {{ Form::text('seo.description', $options['seo.description'], array('class' => 'form-control')) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('seo.google.analytics', Lang::get('messages.admin.settings.seo.ga-id')) }}
                                    {{ Form::text('seo.google.analytics', $options['seo.google.analytics'], ['class' => 'form-control']) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::label('seo.google.webmaster', Lang::get('messages.admin.settings.seo.gw-id')) }}
                                    {{ Form::text('seo.google.webmaster', $options['seo.google.webmaster'], ['class' => 'form-control']) }}
                                </div>
                            </div>

                            <!-- advanced -->
                            <div role="tabpanel" class="tab-pane" id="advanced">
                                <!-- info -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        {{Lang::get('messages.admin.settings.seo.info-page')}}
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-xs-12 col-sm-8">
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.title')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[info][title][global]" value="1" 
                                                           <?php if (isset($advanced->info->title->global) && $advanced->info->title->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[info][title][value]"
                                                       value="{{$advanced->info->title->value}}"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.description')}}</label>{{Lang::get('messages.admin.settings.seo.desc-info')}} 
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[info][description][global]" value="1" 
                                                           <?php if (isset($advanced->info->description->global) && $advanced->info->description->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[info][description][value]"
                                                       value="{{$advanced->info->description->value}}"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.keywords')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[info][keywords][global]" value="1" 
                                                           <?php if (isset($advanced->info->keywords->global) && $advanced->info->keywords->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[info][keywords][value]"
                                                       value="{{$advanced->info->keywords->value}}"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="well">
                                                <span>{{Lang::get('messages.admin.settings.seo.available-vars')}}</span>
                                                <ul>
                                                    <li>
                                                        <b>%manga_name%</b>
                                                    </li>
                                                    <li>
                                                        <b>%manga_author%</b>
                                                    </li>
                                                    <li>
                                                        <b>%manga_artist%</b>
                                                    </li>
                                                    <li>
                                                        <b>%manga_categories%</b>
                                                    </li>
                                                    <li>
                                                        <b>%manga_description%</b>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- reader -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        {{Lang::get('messages.admin.settings.seo.reader-page')}}
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-xs-12 col-sm-8">
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.title')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[reader][title][global]" value="1" 
                                                           <?php if (isset($advanced->reader->title->global) && $advanced->reader->title->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[reader][title][value]"
                                                       value="{{$advanced->reader->title->value}}"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.description')}}</label>{{Lang::get('messages.admin.settings.seo.desc-info')}} 
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[reader][description][global]" value="1" 
                                                           <?php if (isset($advanced->reader->description->global) && $advanced->reader->description->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[reader][description][value]"
                                                       value="{{$advanced->reader->description->value}}"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.keywords')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[reader][keywords][global]" value="1" 
                                                           <?php if (isset($advanced->reader->keywords->global) && $advanced->reader->keywords->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[reader][keywords][value]"
                                                       value="{{$advanced->reader->keywords->value}}"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="well">
                                                <span>{{Lang::get('messages.admin.settings.seo.available-vars.reader')}}</span>
                                                <ul>
                                                    <li>
                                                        <b>%chapter_title%</b>
                                                    </li>
                                                    <li>
                                                        <b>%chapter_number%</b>
                                                    </li>
                                                    <li>
                                                        <b>%chapter_volume%</b>
                                                    </li>
                                                    <li>
                                                        <b>%page_number%</b>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- news -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        {{Lang::get('messages.admin.settings.seo.news-page')}}
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-xs-12 col-sm-8">
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.title')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[news][title][global]" value="1" 
                                                           <?php if (isset($advanced->news->title->global) && $advanced->news->title->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[news][title][value]"
                                                       value="{{$advanced->news->title->value}}"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.description')}}</label>{{Lang::get('messages.admin.settings.seo.desc-info')}} 
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[news][description][global]" value="1" 
                                                           <?php if (isset($advanced->news->description->global) && $advanced->news->description->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[news][description][value]"
                                                       value="{{$advanced->news->description->value}}"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.keywords')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[news][keywords][global]" value="1" 
                                                           <?php if (isset($advanced->news->keywords->global) && $advanced->news->keywords->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[news][keywords][value]"
                                                       value="{{$advanced->news->keywords->value}}"/>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <div class="well">
                                                <span>{{Lang::get('messages.admin.settings.seo.available-vars.news')}}</span>
                                                <ul>
                                                    <li>
                                                        <b>%post_title%</b>
                                                    </li>
                                                    <li>
                                                        <b>%post_content%</b> 
                                                    </li>
                                                    <li>
                                                        <b>%post_keywords%</b> 
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- manga list -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        {{Lang::get('messages.admin.settings.seo.mangalist-page')}}
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.title')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[mangalist][title][global]" value="1" 
                                                           <?php if (isset($advanced->mangalist->title->global) && $advanced->mangalist->title->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[mangalist][title][value]"
                                                       value="@if(isset($advanced->mangalist->title->value)){{$advanced->mangalist->title->value}}@endif"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.description')}}</label>{{Lang::get('messages.admin.settings.seo.desc-info-max')}} 
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[mangalist][description][global]" value="1" 
                                                           <?php if (isset($advanced->mangalist->description->global) && $advanced->mangalist->description->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[mangalist][description][value]"
                                                       value="@if(isset($advanced->mangalist->description->value)){{$advanced->mangalist->description->value}}@endif"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.keywords')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[mangalist][keywords][global]" value="1" 
                                                           <?php if (isset($advanced->mangalist->keywords->global) && $advanced->mangalist->keywords->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[mangalist][keywords][value]"
                                                       value="@if(isset($advanced->mangalist->keywords->value)){{$advanced->mangalist->keywords->value}}@endif"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- latest release -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        {{Lang::get('messages.admin.settings.seo.latestrelease-page')}}
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.title')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[latestrelease][title][global]" value="1" 
                                                           <?php if (isset($advanced->latestrelease->title->global) && $advanced->latestrelease->title->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[latestrelease][title][value]"
                                                       value="@if(isset($advanced->latestrelease->title->value)){{$advanced->latestrelease->title->value}}@endif"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.description')}}</label>{{Lang::get('messages.admin.settings.seo.desc-info-max')}} 
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[latestrelease][description][global]" value="1" 
                                                           <?php if (isset($advanced->latestrelease->description->global) && $advanced->latestrelease->description->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[latestrelease][description][value]"
                                                       value="@if(isset($advanced->latestrelease->description->value)){{$advanced->latestrelease->description->value}}@endif"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.keywords')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[latestrelease][keywords][global]" value="1" 
                                                           <?php if (isset($advanced->latestrelease->keywords->global) && $advanced->latestrelease->keywords->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[latestrelease][keywords][value]"
                                                       value="@if(isset($advanced->latestrelease->keywords->value)){{$advanced->latestrelease->keywords->value}}@endif"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- latest news -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        {{Lang::get('messages.admin.settings.seo.latestnews-page')}}
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.title')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[latestnews][title][global]" value="1" 
                                                           <?php if (isset($advanced->latestnews->title->global) && $advanced->latestnews->title->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[latestnews][title][value]"
                                                       value="@if(isset($advanced->latestnews->title->value)){{$advanced->latestnews->title->value}}@endif"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.description')}}</label>{{Lang::get('messages.admin.settings.seo.desc-info-max')}} 
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[latestnews][description][global]" value="1" 
                                                           <?php if (isset($advanced->latestnews->description->global) && $advanced->latestnews->description->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[latestnews][description][value]"
                                                       value="@if(isset($advanced->latestnews->description->value)){{$advanced->latestnews->description->value}}@endif"/>
                                            </div>
                                            <div class="form-group">
                                                <label>{{Lang::get('messages.admin.settings.seo.keywords')}}</label>
                                                <div class="pull-right">
                                                    <input type="checkbox" name="seo.advanced[latestnews][keywords][global]" value="1" 
                                                           <?php if (isset($advanced->latestnews->keywords->global) && $advanced->latestnews->keywords->global == '1') { ?> checked="checked" <?php } ?> />
                                                    {{Lang::get('messages.admin.settings.seo.use-global')}}
                                                </div>
                                                <input type="text" class="form-control" name="seo.advanced[latestnews][keywords][value]"
                                                       value="@if(isset($advanced->latestnews->keywords->value)){{$advanced->latestnews->keywords->value}}@endif"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-group">
                                {{ Form::submit(Lang::get('messages.admin.settings.save'), ['class' => 'btn btn-primary']) }}
                            </div>
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