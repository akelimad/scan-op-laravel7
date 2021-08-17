@extends('admin.layouts.default')

@section('head')
{{ HTML::script('js/vendor/ckeditor/ckeditor.js') }}
{{ HTML::style('css/bootstrap-select.min.css') }}

{{ HTML::script('js/vendor/bootstrap-select.min.js') }}
@stop

@section('breadcrumbs', Breadcrumbs::render())

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-plus-square-o fa-fw"></i> {{ Lang::get('messages.admin.posts.edit') }}
                <div class="@if(Config::get('orientation') === 'rtl') pull-left  @else pull-right @endif">
                    {{ link_to_route('admin.posts.index', Lang::get('messages.admin.manga.back'), null, array('class' => 'btn btn-default btn-xs', 'role' => 'button')) }}
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">

                {{ Form::open(array('route' => array('admin.posts.update', $post->id), 'method' => 'PUT')) }}
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {{ Form::label('title', Lang::get('messages.admin.posts.title')) }}
                            {{ Form::text('title', $post->title, array('class' => 'form-control rtl', 'placeholder' => 'Enter title here')) }}
                            {{ $errors->first('title', '<label class="error" for="title">:message</label>') }}
                        </div>
                        <div class="form-group">
                            {{Form::textarea('content', $post->content, array('id'=>'content', 'class' => 'form-control'))}}
                            <script>
                                CKEDITOR.replace('content', {
                                    filebrowserImageBrowseUrl: "{{route('admin.posts.browseImage')}}",
                                });
                            </script>
                        </div>
                        <div class="form-group">
                            {{Form::label('keywords', 'Keywords')}}
                            <br/>
                            {{Form::text('keywords', $post->keywords, array('placeholder' => 'comma separated', 'class' => 'form-control'))}}
                        </div>
                        <div class="form-group">
                            {{Form::label('manga_id', Lang::get('messages.admin.posts.related-to'))}}
                            <br/>
                            {{Form::select('manga_id', $categories, $post->manga_id, array('class' => 'status selectpicker', 'data-width' => '50%'))}}
                        </div>
                    </div>
                </div>

                <br/>

                <div class="row">
                    <div class="col-sm-6">
                        {{ Form::submit(Lang::get('messages.admin.posts.edit'), array('class' => 'btn btn-primary pull-left save', 'style' => 'min-width: 40%;margin-right: 10px;')) }}
                        {{ Form::submit(Lang::get('messages.admin.posts.save-draft'), array('class' => 'btn btn-default draft', 'style' => 'min-width: 40%')) }}
                        {{ Form::hidden('status', '', array('id' => 'status')) }}
                    </div>
                </div>
                {{ Form::close() }}
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>

<script>
    $('.save').click(function () {
        $('#status').val('1');
    });
    $('.draft').click(function () {
        $('#status').val('0');
    });
</script>
@stop