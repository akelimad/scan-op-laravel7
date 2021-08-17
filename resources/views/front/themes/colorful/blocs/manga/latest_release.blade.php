@extends('front.layouts.colorful')

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
<h2 class="widget-title">{{ Lang::get('messages.front.home.latest-manga') }}</h2>
@if (count($latestMangaUpdates)>0)
<div class="timeline">
    <dl>
        @foreach ($latestMangaUpdates as $date => $dateGroup)
        @foreach ($dateGroup as $manga)
        @foreach ($manga['chapters'] as $chapter)
        <dd class="pos-left clearfix">
            <div class="circ"></div>
            <div class="time">  
                {{$date}}
            </div>
            <div class="events <?php if ($manga["hot"]) echo 'bg-image-star' ?>">
                <div class="pull-left" style="height: 100px;">
                    <img class="events-object" src='{{asset("uploads/manga/$manga[manga_slug]/cover/cover_thumb.jpg")}}' alt='{{$manga["manga_name"]}}'>
                </div>
                <div class="events-body">
                    <h3 class="events-heading">
                        <a href="{{route('front.manga.show',$manga['manga_slug'])}}">{{$manga["manga_name"]}}</a>
                    </h3>
                    <h6 class="events-subtitle">
                        {{ link_to("/manga/$manga[manga_slug]/$chapter[chapter_slug]", "#".$chapter['chapter_number'].". ".$chapter['chapter_name']) }}
                    </h6>
                </div>
            </div>
        </dd>
        @endforeach
        @endforeach
        @endforeach
    </dl>
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
