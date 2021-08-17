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
<h2 class="widget-title">{{ Lang::get('messages.front.home.news') }}</h2>

<div class="manganews" style="margin-bottom: 20px">
    <ul class="chapters">
        @foreach ($mangaNews as $post)
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
@endif

<h2 class="widget-title">{{ Lang::get('messages.front.home.latest-manga') }}</h2>
@if (count($latestMangaUpdatesResutlSet)>0)
<div class="timeline">
    <dl>
        @foreach ($latestMangaUpdatesResutlSet as $manga)
        <dd class="pos-left clearfix">
            <div class="circ"></div>
            <div class="time">  
                {{ App::make("HelperController")->formateCreationDate($manga->chapter_created_at) }}
            </div>
            <div class="events <?php if ($manga->hot) echo 'bg-image-star' ?>">
                <div class="pull-left" style="height: 100px;">
                    @if ($manga->manga_cover)
                    <img class="events-object" src='{{asset("uploads/manga/{$manga->manga_slug}/cover/cover_thumb.jpg")}}' alt='{{ $manga->manga_name }}'>
                    @else
                    <img width="100" height="100" class="events-object" src='{{asset("uploads/no-image.png")}}' alt='{{ $manga->manga_name }}' />
                    @endif
                </div>
                <div class="events-body">
                    <h3 class="events-heading">
                        <a href="{{route('front.manga.show',$manga->manga_slug)}}">{{$manga->manga_name}}</a>
                    </h3>
                    <h6 class="events-subtitle">
                        {{ link_to("/manga/$manga->manga_slug/$manga->chapter_slug", "#".$manga->chapter_number.". ".$manga->chapter_name) }}
                    </h6>
                </div>
            </div>
        </dd>
        @endforeach
    </dl>
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

