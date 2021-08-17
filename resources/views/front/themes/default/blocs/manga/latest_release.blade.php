@extends('front.layouts.default')

@section('title')
@if(isset($seo->latestrelease->title->global) && $seo->latestrelease->title->global == '1')
{{$settings['seo.title']}} | {{ Lang::get('messages.front.home.latest-manga') }}
@else
{{$seo->latestrelease->title->value}}
@endif
@stop

@section('description')
@if(isset($seo->latestrelease->description->global) && $seo->latestrelease->description->global == '1')
{{$settings['seo.description']}}
@else
{{$seo->latestrelease->description->value}}
@endif
@stop

@section('keywords')
@if(isset($seo->latestrelease->keywords->global) && $seo->latestrelease->keywords->global == '1')
{{$settings['seo.keywords']}}
@else
{{$seo->latestrelease->keywords->value}}
@endif
@stop

@include('front.themes.'.$theme.'.blocs.menu')

@section('allpage')
<h2 class="listmanga-header">
    <i class="fa fa-calendar-o"></i>{{ Lang::get('messages.front.home.latest-manga') }}
</h2>
<hr/>

@if (count($latestMangaUpdates)>0)
<div class="mangalist">
    @foreach ($latestMangaUpdates as $date => $dateGroup)
    @foreach ($dateGroup as $manga)
    <div class="manga-item">
        <h3 class="manga-heading @if(Config::get('orientation') === 'rtl') pull-right @else pull-left @endif">
            <i class="fa fa-book"></i>
            <a href="{{route('front.manga.show',$manga['manga_slug'])}}">{{$manga["manga_name"]}}</a>
            @if($manga["hot"])
            <span class="label label-danger">{{ Lang::get('messages.front.home.hot') }}</span>
            @endif
        </h3>
        <small class="@if(Config::get('orientation') === 'rtl') pull-left @else pull-right @endif" style="direction: ltr;">  
            @if($date == 'Y')
            {{Lang::get('messages.front.home.yesterday')}}
            @elseif($date == 'T')
            {{Lang::get('messages.front.home.today')}}
            @else
            {{$date}}
            @endif
        </small>
        @foreach ($manga['chapters'] as $chapter)
        <div class="manga-chapter">
            <h6 class="events-subtitle">
                {{ link_to("/manga/$manga[manga_slug]/$chapter[chapter_slug]", "#".$chapter['chapter_number'].". ".$chapter['chapter_name']) }}
            </h6>
        </div>
        @endforeach
    </div>
    @endforeach
    @endforeach        
</div>

<div class="row">
    <div class="col-xs-12">
        {{$latestMangaUpdates->links()}}
    </div>
</div>
@else
<div class="center-block">
    <p>{{ Lang::get('messages.front.home.no-chapter') }}</p>
</div>
@endif
@stop
