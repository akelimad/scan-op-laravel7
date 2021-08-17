@extends('front.layouts.default')

@section('title')
@if(isset($seo->latestnews->title->global) && $seo->latestnews->title->global == '1')
{{$settings['seo.title']}} | {{ Lang::get('messages.front.home.news') }}
@else
{{$seo->latestnews->title->value}}
@endif
@stop

@section('description')
@if(isset($seo->latestnews->description->global) && $seo->latestnews->description->global == '1')
{{$settings['seo.description']}}
@else
{{$seo->latestnews->description->value}}
@endif
@stop

@section('keywords')
@if(isset($seo->latestnews->keywords->global) && $seo->latestnews->keywords->global == '1')
{{$settings['seo.keywords']}}
@else
{{$seo->latestnews->keywords->value}}
@endif
@stop

@include('front.themes.'.$theme.'.blocs.menu')

@section('allpage')
<h2 class="listmanga-header">
    <i class="fa fa-newspaper-o"></i> {{ Lang::get('messages.front.home.news') }}
</h2>
<hr/>

@if (count($posts)>0)
<div class="row">
    <div class="col-xs-12">
        @foreach ($posts as $post)
        <div class="news-item" style="display: inline-block; width: 100%;">
            <h3 class="manga-heading @if(Config::get('orientation') === 'rtl') pull-right @else pull-left @endif">
                <i class="fa fa-square"></i>
                <a href="{{route('front.news', $post->slug)}}">{{$post->title}}</a>
            </h3>
            <div class="@if(Config::get('orientation') === 'rtl') pull-left @else pull-right @endif" style="font-size: 13px;">
                <span class="@if(Config::get('orientation') === 'rtl') pull-right @else pull-left @endif">
                    <i class="fa fa-clock-o"></i> {{ App::make("HelperController")->formateCreationDate($post->created_at) }}&nbsp;&middot;&nbsp;
                </span>
                <span class="@if(Config::get('orientation') === 'rtl') pull-right @else pull-left @endif"><i class="fa fa-user"></i> {{$post->user->username}}</span>
                @if(!is_null($post->manga))
                <span class="@if(Config::get('orientation') === 'rtl') pull-right @else pull-left @endif">&nbsp;&middot;&nbsp;<i class="fa fa-folder-open-o"></i> {{ link_to("/manga/{$post->manga->slug}", $post->manga->name) }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        {{$posts->links()}}
    </div>
</div>
@else
<div class="center-block">
    <p>{{ Lang::get('messages.front.home.no-news') }}</p>
</div>
@endif
@stop
