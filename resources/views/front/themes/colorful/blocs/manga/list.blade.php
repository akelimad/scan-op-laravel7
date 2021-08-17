@extends('front.layouts.colorful')

@section('title')
@if(isset($seo->mangalist->title->global) && $seo->mangalist->title->global == '1')
{{ Lang::get('messages.front.directory.title', array('sitename' => $settings['seo.title'])) }}
@else
{{$seo->mangalist->title->value}}
@endif
@stop

@section('description')
@if(isset($seo->mangalist->description->global) && $seo->mangalist->description->global == '1')
{{$settings['seo.description']}}
@else
{{$seo->mangalist->description->value}}
@endif
@stop

@section('keywords')
@if(isset($seo->mangalist->keywords->global) && $seo->mangalist->keywords->global == '1')
{{$settings['seo.keywords']}}
@else
{{$seo->mangalist->keywords->value}}
@endif
@stop

@section('header')
<script>
    var type = "image";
    var cat = "";
    var catName = "";
    var alpha = "";
    var author = "";
    var tag = "";
    var sortBy = "name";
    var asc = true;

    var ascIcon = '<i class="glyphicon glyphicon-sort-by-attributes"></i>';
    var descIcon = '<i class="glyphicon glyphicon-sort-by-attributes-alt"></i>';

    $(document).ready(function(){
        href=window.location.href;
        tab = href.split('/');
        if(tab[tab.length-2] == 'category'){
            cat = tab[tab.length-1];
            // catName = $(".list-category a[href$='cat="+cat+"']").text();
            $('.filter-text').text(cat + ' Manga');
        } else if(tab[tab.length-2] == 'author'){
            author = tab[tab.length-1];
            $('.filter-text').text('Author: ' + author);
        } else if(tab[tab.length-2] == 'tag'){
            tag = tab[tab.length-1];
            $('.filter-text').text('Tag: ' + tag);
        }
    });
    
    // change filter type
    $(document).on('click', '#filter-types .btn-group', function(e) {
        newType = $(this).find('input').attr('id');
        if (type != newType) {
            type = newType;
            changeMangaList(type);
        }
    });

    // sort
    $(document).on('click', '#sort-types .btn-group', function(e) {
        $('#sort-types').find('i').remove();

        newSortBy = $(this).find('input').attr('id');
        if (sortBy == newSortBy) {
            asc = !asc;
            if (asc) {
                $(this).find('input').after(ascIcon);
            } else {
                $(this).find('input').after(descIcon);
            }
        } else {
            sortBy = newSortBy;
            asc = true;
            $(this).find('input').after(ascIcon);
        }

        getMangaList(1);
    });

    // filter by category
    $(document).on('click', '.category', function(e) {
        e.preventDefault();

        alpha = "";
        author = "";
        tag = "";
        cat = $(this).attr('href').split('cat=')[1];
        catName = $(this).text();
        getMangaList(1);
    });

    // filter by alphabet
    $(document).on('click', '.alphabet', function(e) {
        e.preventDefault();

        cat = "";
        author = "";
        tag = "";
        catName = "";
        alpha = $(this).attr('href').split('alpha=')[1];
        getMangaList(1);
    });

    // paginate
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();

        var page = $(this).attr('href').split('page=')[1];
        getMangaList(page);
    });

    function getMangaList(page) {
        $('#waiting').show();

        $.ajax({
            url: "{{action('FrontController@filterList')}}",
            data: {'page': page, 'cat': cat, 'alpha': alpha, 'sortBy': sortBy, 'asc': asc, 'author': author, 'tag': tag}
        }).done(function(data) {
            $('#waiting').hide();
            $('.filter-content').html(data);

            $('.filter-text').text('');
            if (catName != "") {
                $('.filter-text').text(catName + ' Manga');
            } else if (alpha != "") {
                $('.filter-text').text(alpha);
            }
        });
    }

    function changeMangaList(type) {
        $('#waiting').show();

        $.ajax({
            url: '{{action("FrontController@changeMangaList")}}',
            data: {'type': type}
        }).done(function(data) {
            $('#waiting').hide();
            $('.type-content').html(data);

            if (type == 'text') {
                $('.image-version-sidebar').hide();
                $('.text-version-sidebar').show();
            } else if (type == 'image') {
                cat = "";
                catName = "";
                alpha = "";
                author = "";
                tag = "";
                sortBy = "name";
                asc = true;
                $('.filter-text').text('');

                $('.image-version-sidebar').show();
                $('.text-version-sidebar').hide();
            }
        });
    }
</script>

{{ Jraty::js() }}
@stop

@include('front.themes.'.$theme.'.blocs.menu')
@section('sidebar')
<div id="filter-types" class="btn-group btn-group-justified" role="group" data-toggle="buttons" style="margin-bottom: 10px">
    <div class="btn-group" role="group">
        <label class="btn btn-primary">
            <input type="radio" name="filter-type" id="text" />
            {{ Lang::get('messages.front.directory.text-version') }}
        </label>
    </div>
    <div class="btn-group" role="group">
        <label class="btn btn-primary active">
            <input type="radio" name="filter-type" id="image" />
            {{ Lang::get('messages.front.directory.image-version') }}
        </label>
    </div>
</div>

<div class="image-version-sidebar">
    <div class="panel panel-default">
        <div class="panel-heading">{{ Lang::get('messages.front.directory.browse-category') }}</div>
        <div class="panel-body">
            <ul class="list-category">
                @foreach(Category::all() as $category)
                <li><a href="{{ route('front.manga.list', array('cat' => $category->id))}}" class="category">{{$category->name}}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">{{ Lang::get('messages.front.directory.browse-alphabetic') }}</div>
        <div class="panel-body">
            <div class="list-alphabet">
                <a href="{{ route('front.manga.list', array('alpha' => 'Other'))}}" class="alphabet">#</a>
                @foreach (range('A', 'Z') as $char)
                <a href="{{ route('front.manga.list', array('alpha' => $char))}}" class="alphabet">{{ $char }}</a>
                @endforeach
            </div>
        </div>
    </div>
    <?php $tags = Tag::all();?>
    @if(count($tags)>0)
    <div class="panel panel-default">
        <div class="panel-heading">{{ Lang::get('messages.front.directory.browse-tags') }}</div>
        <div class="panel-body">
            <div class="tag-links">
                @foreach($tags as $index=>$tag)
                {{ link_to("/manga-list/tag/$tag->id", $tag->name) }}
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
<div class="text-version-sidebar" style="display: none;">
    <div class="widget-container boxed" style="padding: 20px;">
        <div class="inner">
            <h6><em>{{ Lang::get('messages.front.directory.browse-alphabetic') }}</em></h6>
            <div>
                <span class="alphabetic media disabled unknown">#</span>
                @foreach (range('A', 'Z') as $char)
                <span class="alphabetic media disabled {{ $char }}">{{ $char }}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div id="waiting" style="display: none;" class="pull-right">
    <img src="{{ asset('images/ajax-loader.gif') }}" />
</div>
<h2 class="widget-title">{{ Lang::get('messages.front.directory.manga-directory') }}</h2>

@include('front.themes.'.$theme.'.blocs.manga.list.image')

@stop
