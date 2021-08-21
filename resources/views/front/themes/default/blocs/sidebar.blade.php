@section('sidebar')

<!-- ads -->
<div class="row">
    <div class="col-xs-12" style="padding: 0">
        <div style="display: table; margin: 10px auto;">
            {{isset($ads['RIGHT_SQRE_1'])?$ads['RIGHT_SQRE_1']:''}}
        </div>
        <div style="display: table; margin: 10px auto;">
            {{isset($ads['RIGHT_WIDE_1'])?$ads['RIGHT_WIDE_1']:''}}
        </div>
    </div>
</div>

@foreach($widgets as $index=>$widget)
    @if($widget->type == 'site_description')
        <!-- About Me -->
        <div class="alert alert-success">
        <div class="about">
            <h2>{{$settings['site.name']}}</h2>
            <h6>{{$settings['site.slogan']}}</h6>
            <p>
                {{$settings['site.description']}}
            </p>
        </div>
    </div>
        <!--/ About Me -->
    @elseif($widget->type == 'social_buttons')
        <!-- Social Buttons -->
    <div class="widget-container widget-social">
        <ul class="clearfix">
            <li class="social-facebook first">
                <div>
                    <div id="fb-root"></div>
                    <script>
                        $(document).ready(function () {
                            get_social_counts("{{ action('FrontController@socialCounts') }}", "<?php echo Request::url() ?>");
                        });
                    </script>
                    <a rel="nofollow" href="http://www.facebook.com/share.php?u=<?php echo Request::url() ?>" class="share-facebook"><i>Facebook</i></a>
                    <div class="follow-counter">0</div>
                </div>
            </li>
            <li class="social-twitter">
                <div>
                    <a rel="nofollow" href="https://twitter.com/share?url=<?php echo Request::url() ?>&text=<?php echo urlencode($settings['site.name'] . ' - ' . $settings['site.slogan']); ?>" class="share-twitter"><i>Twitter</i></a>
                    <div class="follow-counter">0</div>
                </div>
            </li>
            <li class="social-google">
                <div>
                    <a rel="nofollow" href="https://plus.google.com/share?url=<?php echo Request::url() ?>"><i>Google+</i></a>
                    <div class="follow-counter">0</div>
                </div>
            </li>
        </ul>
    </div>
        <!--/ Social Buttons -->
    @elseif($widget->type == 'top_rates')
        <!-- Manga Top 10 -->
    @if (count($topManga)>0)
        <div class="panel panel-success">
        @if(strlen(trim($widget->title))>0)
        <div class="panel-heading">
            <h3 class="panel-title"><strong>{{ $widget->title }}</strong></h3>
        </div>
        @endif
        <ul>
            @foreach ($topManga as $index=>$manga)
            <li class="list-group-item">
                <div class="media">
                    <div class="media-left">
                        <a href="{{route('front.manga.show',$manga->manga_slug)}}">
                            @if ($manga->manga_cover)
                            <img width="50" src='{{asset("uploads/manga/{$manga->manga_slug}/cover/cover_thumb.jpg")}}' alt='{{ $manga->manga_name }}'>
                            @else
                            <img width="50" src='{{asset("uploads/no-image.png")}}' alt='{{ $manga->manga_name }}' />
                            @endif
                        </a>
                    </div>
                    <div class="media-body">
                        <h5 class="media-heading"><a href="{{route('front.manga.show',$manga->manga_slug)}}" class="chart-title"><strong>{{$manga->manga_name}}</strong></a></h5>
                        <a href='{{ asset("/manga/$manga->manga_slug/$manga->chapter_slug") }}' class="chart-title">{{"#".$manga->chapter_number.". ".$manga->chapter_name}}</a>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
        <!--/ Manga Top 10 -->
    @elseif($widget->type == 'top_views')
    @if (count($topViewsManga)>0)
        <div class="panel panel-success">
        @if(strlen(trim($widget->title))>0)
        <div class="panel-heading">
            <h3 class="panel-title"><strong>{{ $widget->title }}</strong></h3>
        </div>
        @endif
        <ul>
            @foreach ($topViewsManga as $index=>$manga)
            <li class="list-group-item">
                <div class="media">
                    <div class="media-left">
                        <a href="{{route('front.manga.show',$manga->slug)}}">
                            @if ($manga->cover)
                            <img width="50" src='{{asset("uploads/manga/{$manga->slug}/cover/cover_thumb.jpg")}}' alt='{{ $manga->name }}'>
                            @else
                            <img width="50" src='{{asset("uploads/no-image.png")}}' alt='{{ $manga->name }}' />
                            @endif
                        </a>
                    </div>
                    <div class="media-body">
                        <h5 class="media-heading"><a href="{{route('front.manga.show',$manga->slug)}}" class="chart-title"><strong>{{$manga->name}}</strong></a></h5>
                        <i class="fa fa-eye"></i> {{ $manga->views }}
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
    @elseif($widget->type == 'custom_code')
        <div class="panel panel-default">
        @if(strlen(trim($widget->title))>0)
        <div class="panel-heading">
            <h3 class="panel-title"><strong>{{ $widget->title }}</strong></h3>
        </div>
        @endif
        <div class="panel-body">
            {{ $widget->code }}
        </div>
    </div>
    @elseif($widget->type == 'tags' && count($tags) > 0)
        <div class="panel tag-widget">
        <div class="tag-links">
            @foreach($tags as $index=>$tag)
            {{ link_to("/manga-list/tag/$tag->id", $tag->name) }}
            @endforeach
        </div>
    </div>
    @endif
@endforeach

<!-- ads -->
<div class="row">
    <div class="col-xs-12" style="padding: 0">
        <div style="display: table; margin: 10px auto;">
            {{isset($ads['RIGHT_SQRE_2'])?$ads['RIGHT_SQRE_2']:''}}
        </div>
        <div style="display: table; margin: 10px auto;">
            {{isset($ads['RIGHT_WIDE_2'])?$ads['RIGHT_WIDE_2']:''}}
        </div>
    </div>
</div>
@stop

