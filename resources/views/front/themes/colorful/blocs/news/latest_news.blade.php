@extends('front.layouts.colorful')

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
<h2 class="widget-title">{{ Lang::get('messages.front.home.news') }}</h2>

@if (count($posts)>0)
<div class="row">
    <div class="col-xs-12">
        <ul class="chapters">
            @foreach ($posts as $post)
            <li>
                <div class="pull-right">
                    <span class="pull-left">
                        <i class="glyphicon glyphicon-time"></i> {{ App::make("HelperController")->formateCreationDate($post->created_at) }}&nbsp;&middot;&nbsp;
                    </span>
                    <span class="pull-left"><i class="glyphicon glyphicon-user"></i> {{$post->user->username}}</span>
                    @if(!is_null($post->manga))
                    <span class="pull-left">&nbsp;&middot;&nbsp;<i class="glyphicon glyphicon-folder-open"></i> {{ link_to("/manga/{$post->manga->slug}", $post->manga->name) }}</span>
                    @endif
                </div>

                <h3 class="chapter-title-rtl">
                    <a href="{{route('front.news', $post->slug)}}">{{$post->title}}</a>
                </h3>
            </li>
            @endforeach
        </ul>
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
