@extends('front.layouts.colorful')

@section('title')
{{ Lang::get('messages.front.adv-search.title', array('sitename' => $settings['seo.title'])) }}
@stop

@section('description')
{{$settings['seo.description']}}
@stop

@section('keywords')
{{$settings['seo.keywords']}}
@stop

@section('header')
{{ HTML::style('css/selectize.css') }}
{{ HTML::script('js/vendor/selectize.js') }}

<script>
    $(document).ready(function () {
        $('select').selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            closeAfterSelect: false,
            allowEmptyOption: true
        });
    });

    // filter
    $(document).on('click', '#search', function (e) {
        e.preventDefault();
        getMangaList(1);
    });

    // paginate
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();

        var page = $(this).attr('href').split('page=')[1];
        getMangaList(page);
    });

    function getMangaList(page) {
        var form = $('#filterForm');
        var params = form.serialize();

        $.ajax({
            type: 'POST',
            url: "{{action('FrontController@advSearchFilter')}}",
            data: {params: params, 'page': page},
            beforeSend: function () {
                $('#waiting').show();
            },
            success: function (html) {
                $('#waiting').hide();
                $('.search-result').show();
                $('#content').html(html);
                $('html,body').animate({scrollTop: $('.search-result').offset().top}, '500', 'swing');
            }
        });
    }
</script>

{{ Jraty::js() }}
@stop

@include('front.themes.'.$theme.'.blocs.menu')

@section('allpage')
<h2 class="widget-title">{{ Lang::get('messages.front.home.adv-search') }}</h2>
<hr/>

<form id="filterForm">
    <div class="col-sm-12">
        <label>{{ Lang::get('messages.front.adv-search.filtre-cat') }}</label>
        <select name="categories[]" multiple="multiple"
                placeholder="{{ Lang::get('messages.front.adv-search.filtre-cat.ph') }}">
            @foreach(Category::all() as $category)
            <option value="{{$category->id}}">{{$category->name}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-sm-6">
        <label>{{ Lang::get('messages.front.adv-search.filtre-stat') }}</label>
        <select name="status[]" multiple="multiple"
                placeholder="{{ Lang::get('messages.front.adv-search.filtre-stat.ph') }}">
            @foreach(Status::all() as $status)
            <option value="{{$status->id}}">{{$status->label}}</option>
            @endforeach
        </select>
    </div>

    <?php $types = ComicType::all(); ?>
    @if(count($types)>0)
    <div class="col-sm-6">
        <label>{{ Lang::get('messages.front.adv-search.filtre-type') }}</label>
        <select name="types[]" multiple="multiple"
                placeholder="{{ Lang::get('messages.front.adv-search.filtre-type.ph') }}">
            @foreach($types as $type)
            <option value="{{$type->id}}">{{$type->label}}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="col-sm-6">
        <div class="form-group">
            <label>{{ Lang::get('messages.front.adv-search.filtre-year') }}</label>
            <input type="text" name="release" class="form-control" 
                   placeholder="{{ Lang::get('messages.front.adv-search.filtre-year.ph') }}" />
        </div>
    </div>

    <div class="col-sm-6">
        <div class="form-group">
            <label>{{ Lang::get('messages.front.adv-search.filtre-author') }}</label>
            <input type="text" name="author" class="form-control" 
                   placeholder="{{ Lang::get('messages.front.adv-search.filtre-author.ph') }}" />
        </div>
    </div>

    <div class="col-sm-12">
        <br/>
        <div class="form-group">
            <button id="search" class="btn-primary">{{ Lang::get('messages.front.adv-search.search') }}</button>
            <div id="waiting" style="display: none;" class="@if(Config::get('orientation') === 'rtl') pull-left @else pull-right @endif">
                <img src="{{ asset('images/ajax-loader.gif') }}" />
            </div>
        </div>
    </div>
</form>

<div class="col-sm-12 search-result" style="display: none">
    <h4 class="widget-title">{{ Lang::get('messages.front.adv-search.search-result') }}</h4>
    <hr/>
    <div id="content">
    </div>
</div>
@stop
