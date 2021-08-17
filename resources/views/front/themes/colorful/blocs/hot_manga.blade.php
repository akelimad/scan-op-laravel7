@section('hotmanga')
@if (count($hotMangaList)>0)
<div class="widget-container widget-schedule clearfix">
    <h2 class="widget-title"><i class="menu-icon-3"></i>{{ Lang::get('messages.front.home.hot-updates') }}</h2>
    <div class="carousel">
        <ul id="schedule">
            @foreach ($hotMangaList as $manga)
            <li class="schedule-item clearfix">
                <div class="schedule-left">
                    <div class="schedule-name">{{ $manga->manga_name }}</div>
                    <div class="schedule-date">{{ "#".$manga->chapter_number."  ".$manga->chapter_name }}</div>
                </div>
                <div class="schedule-right">
                    <div class="schedule-avatar">
                        <a href="#">
                            @if ($manga->manga_cover)
                            <img src='{{asset("uploads/manga/{$manga->manga_slug}/cover/cover_250x350.jpg")}}' alt='{{ $manga->manga_name }}' />
                            @else
                            <img width="250" height="350" src='{{asset("uploads/no-image.png")}}' alt='{{ $manga->manga_name }}' />
                            @endif
                        </a>
                    </div>
                    <div class="schedule-links">
                        <a class="schedule-details" href="{{route('front.manga.show',$manga->manga_slug)}}">
							<i class="icon-small-info"></i>{{ Lang::get('messages.front.home.about-manga') }}
						</a>
                        <a class="schedule-add" href='{{asset("/manga/$manga->manga_slug/$manga->chapter_slug") }}'>
							<i class="glyphicon glyphicon-book"></i>{{ Lang::get('messages.front.home.read-chapter') }}
						</a>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>

        <a class="prev" id="schedule-prev" href="#">&lsaquo;</a>
        <a class="next" id="schedule-next" href="#">&rsaquo;</a>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {

        function scheduleInit() {
            $('#schedule').carouFredSel({
                swipe: {
                    onTouch: true
                },
                prev: '#schedule-prev',
                next: "#schedule-next",
                auto: {
                    play: true,
                    timeoutDuration: 16000
                },
                scroll: {
                    pauseOnHover: true,
                    items: 1,
                    duration: 500,
                    easing: 'swing'
                }
            });
        }

        scheduleInit();

        $(window).resize(function() {
            scheduleInit();
        });
        });
</script>
@endif
@stop