@extends('admin.layouts.default')

@section('breadcrumbs', Breadcrumbs::render('admin.manga.hot'))

@section('content')
<div class="row">
    <div class="col-lg-12">
        @if (Session::has('updateSuccess'))
        <div class="alert text-center alert-info ">
            {{ Session::get('updateSuccess') }}
        </div>
        @endif
        
        <div class="panel panel-danger">
            <div class="panel-heading">
                <i class="fa fa-star fa-fw"></i> {{ Lang::get('messages.admin.manga.hot.title') }}
                <div class="pull-right">
                    {{ link_to_route('admin.index', Lang::get('messages.admin.manga.back'), null, array('class' => 'btn btn-default btn-xs', 'role' => 'button')) }}
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <p>{{ Lang::get('messages.admin.manga.hot.notice') }}</p>
                    </div>
                    <div class="col-sm-4 col-sm-offset-1">
                        <div class="list-group" id="list1">
                            <div class="list-group-item active">{{ Lang::get('messages.admin.manga.hot.manga-list') }}<input title="toggle all" class="all pull-right" type="checkbox"></div>
                            @foreach ($mangas as $manga)
                            <div class="list-group-item" data-id="{{ $manga->id }}">{{ $manga->name }}<input class="pull-right" type="checkbox"></div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-sm-2 v-center">
                        {{ Form::open(array('action' => 'MangaController@updateHotManga', 'method' => 'post')) }}
                        {{ Form::submit(Lang::get('messages.admin.manga.hot.save'), array('class' => 'btn btn-primary center-block save', 'style' => 'width: 100%')) }}
                        {{ Form::hidden('hotlist', '', array('id' => 'hotlist')) }}
                        {{ Form::close() }}
                        <button title="Send to list 2" class="btn btn-default center-block add"><i class="glyphicon glyphicon-chevron-right"></i></button>
                        <button title="Send to list 1" class="btn btn-default center-block remove"><i class="glyphicon glyphicon-chevron-left"></i></button>
                    </div>
                    <div class="col-sm-4">
                        <div class="list-group" id="list2">
                            <div class="list-group-item active">{{ Lang::get('messages.admin.manga.hot.hot-list') }}<input title="toggle all" class="all pull-right" type="checkbox"></div>
                            @foreach ($hotest as $manga)
                            <div class="list-group-item" data-id="{{ $manga->id }}">{{ $manga->name }}<input class="pull-right" type="checkbox"></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>

<script>
    function updateHotList() {
        var hotlist = Array();
        $("#list2 .list-group-item")
                .each(function(idx, item) {
                    id = $(item).attr('data-id');
                    if (id !== undefined)
                        hotlist.push($(item).attr('data-id'));
                });

        $('#hotlist').val(hotlist);
    }

    updateHotList();

    $('.add').click(function() {
        $('.all').prop("checked", false);
        var items = $("#list1 input:checked:not('.all')");
        var n = items.length;
        if (n > 0) {
            items.each(function(idx, item) {
                var choice = $(item);
                choice.prop("checked", false);
                choice.parent().appendTo("#list2");
            });

            updateHotList();
        }
        else {
            alert("Choose an item from list 1");
        }
    });

    $('.remove').click(function() {
        $('.all').prop("checked", false);
        var items = $("#list2 input:checked:not('.all')");
        items.each(function(idx, item) {
            var choice = $(item);
            choice.prop("checked", false);
            choice.parent().appendTo("#list1");
        });

        updateHotList();
    });

    /* toggle all checkboxes in group */
    $('.all').click(function(e) {
        e.stopPropagation();
        var $this = $(this);
        if ($this.is(":checked")) {
            $this.parents('.list-group').find("[type=checkbox]").prop("checked", true);
        }
        else {
            $this.parents('.list-group').find("[type=checkbox]").prop("checked", false);
            $this.prop("checked", false);
        }
    });

    $('[type=checkbox]').click(function(e) {
        e.stopPropagation();
    });

    /* toggle checkbox when list group item is clicked */
    $('.list-group a').click(function(e) {

        e.stopPropagation();

        var $this = $(this).find("[type=checkbox]");
        if ($this.is(":checked")) {
            $this.prop("checked", false);
        }
        else {
            $this.prop("checked", true);
        }

        if ($this.hasClass("all")) {
            $this.trigger('click');
        }
    });
</script>
@stop