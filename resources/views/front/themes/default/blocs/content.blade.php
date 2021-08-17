@section('content')
<!-- ads -->
<div class="row">
    <div class="col-xs-12" style="padding: 0">
        <div class="ads-large" style="display: table; margin: 10px auto;">
            {{isset($ads['TOP_LARGE'])?$ads['TOP_LARGE']:''}}
        </div>
        <div style="display: table; margin: 10px auto;">
            <div class="pull-left ads-sqre1" style="margin-right: 10px;">
                {{isset($ads['TOP_SQRE_1'])?$ads['TOP_SQRE_1']:''}}
            </div>
            <div class="pull-right ads-sqre2">
                {{isset($ads['TOP_SQRE_2'])?$ads['TOP_SQRE_2']:''}}
            </div>
        </div>
    </div>
</div>

<!-- news -->
@if (count($mangaNews)>0)
<h2 class="listmanga-header">
    <i class="fa fa-newspaper-o"></i> {{ Lang::get('messages.front.home.news') }}
</h2>
<hr/>

<div class="manganews">
    @foreach ($mangaNews as $post)
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
@endif

<h2 class="listmanga-header">
    <i class="fa fa-bars"></i>{{ Lang::get('messages.front.home.latest-manga') }}
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
@else
<div class="center-block">
    <p>{{ Lang::get('messages.front.home.no-chapter') }}</p>
</div>
@endif

<!-- ads -->
<div class="row">
    <div class="col-xs-12" style="padding: 0">
        <div class="ads-large" style="display: table; margin: 10px auto;">
            {{isset($ads['BOTTOM_LARGE'])?$ads['BOTTOM_LARGE']:''}}
        </div>
        <div style="display: table; margin: 10px auto 0;">
            <div class="pull-left ads-sqre1" style="margin-right: 10px;">
                {{isset($ads['BOTTOM_SQRE_1'])?$ads['BOTTOM_SQRE_1']:''}}
            </div>
            <div class="pull-right ads-sqre2">
                {{isset($ads['BOTTOM_SQRE_2'])?$ads['BOTTOM_SQRE_2']:''}}
            </div>
        </div>
    </div>
</div>
@stop

